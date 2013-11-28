<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Novius\OnlineMediaFiles;

class Renderer_Media extends \Fieldset_Field
{
    protected $options                  = array();

    public static function _init()
    {
        \Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common'));
    }

    public function __construct($name, $label = '', array $renderer = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset = null)
    {
        list($attributes, $this->options) = static::parse_options($renderer);
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
        if (!isset($item->{$field_name})) {
            throw new \Exception('Field or relation `'.$field_name.'` cannot be found in '.get_class($item));
        }

        $is_multiple = isset($this->options['multiple']) ? $this->options['multiple'] : is_array($item->{$field_name});

        // Generate the values
        $values = array();
        if (is_array($item->{$field_name})) {
            foreach ($item->{$field_name} as $media) {
                $values[] = $media->onme_id;
            }
        } elseif (is_a($item->{$field_name}, 'Novius\OnlineMediaFiles\Model_Media')) {
            $values = array($item->{$field_name}->onme_id);
        } else {
            $values = array(intval($item->{$field_name}));
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
        $this->template = '<div style="padding: 10px">{field}</div>';
        foreach ($values as $value) {

            // Add brackets at the end of the input name if multiple
            if ($is_multiple) {
                $this->set_attribute('name', $field_name.'[]');
                $this->name = $field_name.'[]';
            }

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
     * Standalone build of the media renderer
     *
     * @param array $renderer $renderer Renderer definition (attributes + renderer_options)
     * @return string The <input> tag + JavaScript to initialise it
     */
    public static function renderer($renderer = array())
    {
        // Génère les attributs et les options à partir de la configuration du renderer
        list($attributes, $renderer_options) = static::parse_options($renderer);

        $field_name = $attributes['name'];

        // Generate a field for each values
        $index = 0;
        $fields = array();
        $renderer['values'] = (array) $renderer['values'];
        foreach ($renderer['values'] as $value) {

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

            // Add brackets to the field name if multiple
            if ($renderer_options['multiple'] && substr($field_attributes['name'], -2) != '[]') {
                $field_attributes['name'] .= '[]';
            }

            // Build the field
            $field = \Fuel\Core\Form::input($field_name, $value, $field_attributes);

            // Generate the field
            $fields[] = \View::forge('novius_onlinemediafiles::admin/renderer/media_field', array(
                'field'     => $field,
                'template'  => '<div style="padding: 10px">{field}</div>'
            ), false);

            // Stop at first value if not multiple
            if (!$renderer['multiple']) {
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
        $relation_name = $this->name;

        // Multiple
        if (!empty($data[$relation_name]) && is_array($data[$relation_name])) {

            // Clear the current linked videos
            $item->{$relation_name} = array();

            // Get the new linked videos
            foreach ($data[$relation_name] as $media_id) {
                if (ctype_digit($media_id) ) {
                    $media_ids[] = intval($media_id);
                }
            }
            if (count($media_ids)) {
                $medias = \Novius\OnlineMediaFiles\Model_Media::find('all', array(
                    'where' => array(array('onme_id', 'IN', array_values($media_ids)))
                ));
                foreach ($media_ids as $k => $id) {
                    if (isset($medias[$id])) {
                        $item->{$relation_name}[$k] = $medias[$id];
                    }
                }
                $item->{$relation_name} = $medias;
            }

            return false;
        }

        // Single
        else {
            $item->{$relation_name} = $data[$relation_name];
            return true;
        }
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
        $attributes['class'] = trim((isset($renderer['class']) ? $renderer['class'] : '').' onlinemediafile_input');
        if (empty($attributes['id'])) {
            $attributes['id'] = uniqid('onlinemediafile_');
        }
        unset($attributes['multiple']);
        unset($attributes['values']);
        unset($attributes['template']);

        // Build options
        $options = array(
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
        );

        // Options du renderer
        if (!empty($renderer['renderer_options'])) {
            $options = \Arr::merge($options, $renderer['renderer_options']);
        }

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
        if (!empty($attributes['value'])) {
            $media = \Novius\OnlineMediaFiles\Model_Media::find($attributes['value']);
            if (!empty($media)) {
                $options['inputFileThumb']['file'] = $media->thumbnail();
                $options['inputFileThumb']['title'] = $media->onme_title;
            }
        }
        if (!empty($attributes['required'])) {
            $options['inputFileThumb']['allowDelete'] = false;
        }
        if (isset($options['values'])) {
            unset($options['values']);
        }
        if (isset($renderer['multiple'])) {
            unset($renderer['multiple']);
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
}
