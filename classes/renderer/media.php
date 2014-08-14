<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

namespace Novius\OnlineMediaFiles;

class Renderer_Media extends \Fieldset_Field
{
    protected $options                  = array();
    protected $_key_prefix              = 'media_';

    public static function _init()
    {
        \Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common'));
    }

    public function __construct($name, $label = '', array $renderer = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset = null)
    {
        list($attributes, $this->options) = static::parse_options($renderer);
        if (\Arr::get($this->options, 'key_prefix', false)) {
            $this->_key_prefix = \Arr::get($this->options, 'key_prefix').'_';
        }
        parent::__construct($name, $label, $attributes, $rules, $fieldset);
    }

    /**
     * CRUD build of the media renderer
     *
     * @return bool|string
     * @throws \Exception
     */
    public function build()
    {
        // Add some style
        $this->fieldset()->append(static::css_init());

        $item = $this->fieldset()->getInstance();
        $field_name = $this->name;
        $class = get_class($item);
        $exploded_field_name = explode('->', $field_name);
        if (count($exploded_field_name) > 1) {
            $provider = $class::providers($exploded_field_name[0]);
        } else {
            $provider = null;
        }
        $relation_name = \Arr::get($this->options, 'provider_relation', $field_name);
        $relation = $class::relations($relation_name);
        $attributes = $class::properties();
        $field_found = !empty($relation) || isset($attributes[$field_name]) || !empty($provider);
        if (!$field_found) {
            throw new \Exception('Field or relation `'.$field_name.'` cannot be found in '.get_class($item));
        }

        $is_multiple = isset($this->options['multiple']) ? $this->options['multiple'] : is_array($item->{$field_name});

        /* This is the start to protect common online media in crud. It's not working with just that */
		/* @todo make it work with multiple fields */
		/*
        //We defined if this is a common field
        $is_common = false;
        $twin_relation = $relation;
        if (empty($twin_relation)) {
            if (!empty($provider)) {
                $twin_relation = $class::relations($provider['relation']);
            }
        }
        if (!empty($twin_relation) && \Str::starts_with(get_class($twin_relation),'Nos\Orm_Twinnable')) {
            $is_common = true;
        }
        if ($is_common) {
            //prepare context label in case of common field
            $contexts = $item->get_all_context();
            $context_labels = array();
            foreach ($contexts as $context) {
                $context_labels[] = \Nos\Tools_Context::contextLabel($context);
            }
            $context_labels = htmlspecialchars(\Format::forge($context_labels)->to_json());
            //This is a twinable relation, so this is a common field
            $this->set_attribute('disabled', true);
            $this->set_attribute('context_common_field', true);
            $this->set_attribute('data-other-contexts', $context_labels);
        }
        */

        // Generate the values
        if (!empty($this->attributes['value'])) {
            if (is_array($this->attributes['value'])) {
                $values = $this->_getItemValues(); //We use a private method to retrive the good items in case of a provider.
            } else {
                $values = array($this->attributes['value']);
            }

        } else {
            $values = $this->_getItemValues(); //If no values are set, we check them via the item
        }

        // Set an empty value by default
        $values = array_filter($values);
        if (!count($values)) {
            $values = array('');
        }

        // Generate a field for each values
        $index = 0;
        $template_id = false;
        //id used for applying JS on every created field
        $uniqid = uniqid('renderer_onlinemedia_');
        $fields = array();
        //template will be stored to apply it on all fields, not only the input
        $template = $this->template;
        $this->template = !empty($this->options['field_template']) ? $this->options['field_template'] : '<div style="padding: 10px">{field}</div>';

        // Add brackets at the end of the input name if multiple
        if ($is_multiple) {
            $this->set_attribute('name', $field_name.'[]');
            $this->name = $field_name.'[]';
        }

        foreach ($values as $value) {

            // Build the field
            $this->set_attribute('id', '');
            $this->set_value($value, false);
            parent::build();
            $this->fieldset()->append(static::js_init($uniqid));

            // Set the field ID
            if (!$template_id) {
                // Save the generated ID as a template for the next fields
                $template_id = $this->get_attribute('id');
            } else {
                // Set the ID using the template ID with an incremented offset
                $this->set_attribute('id', $template_id.'_'.(++$index));
            }

            // Add the renderer options
            $this_options = $this->options;
            static::hydrate_options($this_options, array(
                'value' 	=> $value,
                'required' 	=> isset($this->rules['required']),
            ));
            $this->set_attribute('data-media-options', htmlspecialchars(\Format::forge()->to_json($this_options)));

            // Generate the field
            $fields[] = \View::forge('novius_onlinemediafiles::admin/renderer/media_field', array(
                'field' => parent::build(),
            ), false);

            // Stop at first value if not multiple
            if (!$is_multiple) {
                break;
            }
        }
        $this->template = $template;
        return $this->template(\View::forge('novius_onlinemediafiles::admin/renderer/media_fields', array(
            'options'   => \Arr::merge($this->options, array('multiple' => $is_multiple)),
            'fields'    => $fields,
            'id'        => $uniqid
        ), false));
    }

    /**
     * Retrieve the item value to populate the renderer
     * @return array
     */
    protected function _getItemValues()
    {
        $item = $this->fieldset()->getInstance();
        $field_name = \Arr::get($this->options, 'provider_relation', $this->name);
        $values = array();
        if (is_array($item->{$field_name})) {
            foreach ($item->{$field_name} as $media_id => $media) {
                //If the provider_relation option is set, $media is a Model_Link
                if (\Arr::get($this->options, 'provider_relation', false)) {
                    //We need to filter them by key.
                    if (!\Str::starts_with($media->onli_key, $this->_key_prefix)) {
                        unset($item->{$field_name}[$media_id]);
                        continue;
                    }
                }
                $values[] = $media->onme_id;
            }
        } elseif (is_a($item->{$field_name}, 'Novius\OnlineMediaFiles\Model_Media')) {
            $values = array($item->{$field_name}->onme_id);
        } else {
            $values = array(intval($item->{$field_name}));
        }
        return $values;
    }

    /**
     * Standalone build of the media renderer
     *
     * @param array $renderer $renderer Renderer definition (attributes + renderer_options)
     * @return string The <input> tag + JavaScript to initialise it
     */
    public static function renderer($renderer = array())
    {
        // Default values
        $values = array();
        if (isset($renderer['values'])) {
            $values = (array) $renderer['values'];
            unset($renderer['values']);
        }
        // Set an empty value by default
        $values = array_filter($values);
        if (!count($values)) {
            $values = array('');
        }

        // Generate attributes and options from configuration renderer
        list($attributes, $renderer_options) = static::parse_options($renderer);
        $is_multiple = \Arr::get($renderer_options, 'multiple');

        // Field name
        $field_name = \Arr::get($attributes, 'name');
        // Add brackets to the field name if multiple
        if ($is_multiple && substr($field_name, -2) != '[]') {
            $field_name .= '[]';
        }

        // Generate a field for each values
        $index = 0;
        $fields = array();
        foreach ($values as $value) {

            // Generate the renderer options for this field
            $field_options = $renderer_options;
            static::hydrate_options($field_options, array(
                'value' 	=> $value,
                'required' 	=> isset($field_options['required']),
            ));

            // Generate the attributes for this field
            $field_attributes = \Arr::merge($attributes, array(
                'value'                 => $value,
                'required' 	            => isset($field_options['required']),
                'data-media-options'    => htmlspecialchars(\Format::forge()->to_json($field_options)),
                'id'                    => $attributes['id'].'_'.(++$index),
            ));
            \Arr::delete($field_attributes, 'name');

            // Build the field
            $field = \Fuel\Core\Form::input($field_name, $value, $field_attributes);

            // Generate the field
            $fields[] = \View::forge('novius_onlinemediafiles::admin/renderer/media_field', array(
                'field'     => $field,
                'template'  => !empty($renderer_options['field_template']) ? $renderer_options['field_template'] : '<div style="padding: 10px">{field}</div>'
            ), false);

            // Stop at first value if not multiple
            if (!$is_multiple) {
                break;
            }
        }
        //used for applying JS on every created field
        $uniqid = uniqid('renderer_onlinemedia_');
        return \View::forge('novius_onlinemediafiles::admin/renderer/media_fields', array(
            'options'   => $renderer_options,
            'fields'    => $fields,
            'id'        => $uniqid,
        ), false) . static::js_init($uniqid) . static::css_init();
    }

    /**
     * Set medias as relations before save (multiple renderer)
     *
     * @param $item
     * @param $data
     * @return bool
     */
    public function before_save($item, $data)
    {
        $relation_name = \Arr::get($this->options, 'provider_relation', $this->name);

        // Get the new values
        $values = \Arr::get($data, $this->name);
        if (\Arr::get($this->options, 'provider_relation') && !is_array($values)) {
            // Force the new values as an array if a provider relation is used
            $values = array($values);
        }

        // Single values are already handled by the default save mechanism
        if (!is_array($values)) {
            return true;
        }

        // Store the original linked media
        $original_links = $item->{$relation_name};
        foreach ($original_links as $link_id => $oModel_Link) { //filter the links with the prefix
            if (!\Str::starts_with($oModel_Link->onli_key, $this->_key_prefix)) {
                unset($original_links[$link_id]);
                continue;
            } else {
                // Clear the current linked videos
                unset($item->{$relation_name}[$link_id]);
            }
        }
        $linked_media_keys = \Arr::assoc_to_keyval($original_links, 'onli_onme_id', 'onli_key');
        $links_ids = \Arr::assoc_to_keyval($original_links, 'onli_onme_id', 'onli_id');

        // Get the new linked online media IDs
        $media_ids = array();
        foreach ($values  as $media_id) {
            if (ctype_digit($media_id) ) {
                $media_ids[] = intval($media_id);
            }
        }
        // Don't go further if there are no linked online medias
        if (!count($media_ids)) {
            if (\Arr::get($this->options, 'provider_relation')) {
                // Delete existing links
                $this->deleteLinks($links_ids);
            }
            return false;
        }

        $medias = \Novius\OnlineMediaFiles\Model_Media::find('all', array(
            'where' => array(array('onme_id', 'IN', array_values($media_ids)))
        ));

        // Save new links for a provider
        if (\Arr::get($this->options, 'provider_relation')) {
            $relation = $item::relations(\Arr::get($this->options, 'provider_relation'));
            $key_from = $relation->key_from;
            $key_from = reset($key_from);
            $key_to = $relation->key_to;
            $key_to = reset($key_to);

            $links_to_keep = array();
            $i = 0;
            foreach ($medias as $media) {
                //Media is already linked to the model
                if (array_key_exists($media->id, $linked_media_keys) && $linked_media_keys[$media->id] == $this->_key_prefix.$i) {
                    $links_to_keep[] = $links_ids[$media->id];
                    $original_links[$links_ids[$media->id]]->onli_key = $this->_key_prefix.$i;
                    $original_links[$links_ids[$media->id]]->save();
                    $item->{$relation_name}[$links_ids[$media->id]] = $original_links[$links_ids[$media->id]];
                } else { //Otherwise we create a new link
                    $media_link = \Novius\OnlineMediaFiles\Model_Link::forge(
                        array(
                            'onli_from_table' => $item::table(),
                            $key_to => $item->{$key_from},
                            'onli_key' => $this->_key_prefix.$i,
                        )
                    );
                    $media_link->media = $media;
                    $media_link->save();
                    $item->{$relation_name}[$media_link->id] = $media_link;
                }
                $i++;
            }
            $links_to_delete = array_diff($links_ids, $links_to_keep);
            $this->deleteLinks($links_to_delete);
        }
        // Save new links for a relation
        else {
            foreach ($media_ids as $k => $id) {
                if (isset($medias[$id])) {
                    $item->{$relation_name}[$k] = $medias[$id];
                }
            }
        }

        return false;
    }

    /**
     * Parse the renderer array to get attributes and the renderer options
     *
     * @param  array $renderer
     * @return array 0: attributes, 1: renderer options
     */
    protected static function parse_options($renderer = array())
    {
        // Build attributes
        $attributes = $renderer;
        !empty($attributes['id']) or ($attributes['id'] = uniqid('onlinemediafile_'));
        \Arr::set($attributes, 'class', trim(\Arr::get($renderer, 'class').' onlinemediafile_input'));
        \Arr::delete($attributes, 'template');
        \Arr::delete($attributes, 'renderer_options');

        // Build options
        $options = \Arr::merge(array(
            'mode' => 'single',
            'inputFileThumb' => array(
                'title' => __('Online Media'),
                'texts' => array(
                    'add'            => __('Pick an internet media'),
                    'edit'           => __('Pick another internet media'),
                    'delete'         => __('No internet media'),
                    'wrongExtension' => __('This extension is not allowed.'),
                ),
            ),
        ), \Arr::get($renderer, 'renderer_options', array()));

        return array($attributes, $options);
    }

    /**
     * Hydrate the options array to fill in the media URL for the specified value
     *
     * @param $options
     * @param array $attributes
     */
    protected static function hydrate_options(&$options, $attributes = array())
    {
        // Value
        if (!empty($attributes['value'])) {
            $media = \Novius\OnlineMediaFiles\Model_Media::find($attributes['value']);
            if (!empty($media)) {
                $options['inputFileThumb']['file'] = $media->thumbnail();
                $options['inputFileThumb']['title'] = $media->onme_title;
            }
        }

        // Required
        if (!empty($attributes['required'])) {
            $options['inputFileThumb']['allowDelete'] = false;
        }

        // Unset options that are not necessary
        if (isset($options['values'])) {
            unset($options['values']);
        }
        if (isset($options['multiple'])) {
            unset($options['multiple']);
        }
    }

    /**
     * Generates the JavaScript to initialise the renderer
     *
     * @param  bool|integer  HTML ID attribute of the <input> tag
     * @return string JavaScript to initialise the renderers
     */
    protected static function js_init($id)
    {
        return \View::forge('novius_onlinemediafiles::admin/renderer/media_js', array(
            'id' => $id,
        ), false);
    }

    /**
     * Generates the CSS to style the renderers
     *
     * @return string CSS to style the renderers
     */
    protected static function css_init()
    {
        return \View::forge('novius_onlinemediafiles::admin/renderer/media_css', array(), false);
    }

    /**
     * Clean the link media table
     *
     * @param array $links_ids
     */
    protected function deleteLinks($links_ids = array())
    {
        if (!is_array($links_ids) || empty($links_ids)) return;

        $links = \Novius\OnlineMediaFiles\Model_Link::find('all', array(
            'where' => array(array('onli_id', 'IN', array_values($links_ids)))
        ));
        foreach ($links as $oModel_Link) {
            $oModel_Link->delete();
        }
    }
}
