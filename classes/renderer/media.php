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
    protected $options = array();

    public static function _init()
    {
        \Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'nos::common'));
    }

    public function __construct($name, $label = '', array $renderer = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset = null)
    {
        list($attributes, $this->options) = static::parse_options($renderer);
        parent::__construct($name, $label, $attributes, $rules, $fieldset);
    }

    /**
     * Standalone build of the media renderer.
     *
     * @param  array  $renderer Renderer definition (attributes + renderer_options)
     * @return string The <input> tag + JavaScript to initialise it
     */
    public static function renderer($renderer = array())
    {
        list($attributes, $renderer_options) = static::parse_options($renderer);
        static::hydrate_options($renderer_options, $attributes);
        $attributes['data-media-options'] = htmlspecialchars(\Format::forge()->to_json($renderer_options));

        return '<input '.array_to_attr($attributes).' />'.static::js_init($attributes['id']);
    }

    /**
     * How to display the field
     * @return type
     */
    public function build()
    {
		// Get the item
		$item = $this->fieldset()->getInstance();
        // Cherche si le nom du champ correspond à une relation many_many (auquel cas c'est un champ multiple)

		// Multiple
		if (is_array($item->{$this->name})) {
			d('multiple');
		}
		// Single
		else {
			d('single');
		}
        d($this->name);
//        dd($item->relations($this->name));
        // Charge les valeurs par défaut
        // Cherche la relation

        foreach ($item->{$this->name} as $relation) {

        }

        parent::build();
        $this->fieldset()->append(static::js_init($this->get_attribute('id')));
        static::hydrate_options($this->options, array(
            'value' => $this->value,
            'required' => isset($this->rules['required']),
        ));
        $this->set_attribute('data-media-options', htmlspecialchars(\Format::forge()->to_json($this->options)));

        return (string) parent::build();
    }

    /**
     * Parse the renderer array to get attributes and the renderer options
     * @param  array $renderer
     * @return array 0: attributes, 1: renderer options
     */
    protected static function parse_options($renderer = array())
    {
        $renderer['class'] = (isset($renderer['class']) ? $renderer['class'] : '').' onlinemediafile';

        if (empty($renderer['id'])) {
            $renderer['id'] = uniqid('onlinemediafile_');
        }

        // Default options of the renderer
        $renderer_options = array(
            'mode' => 'single',
            'inputFileThumb' => array(
                'title' => __('Online Media'),
                'texts' => array(
                    'add'            => __('Pick a media'),
                    'edit'           => __('Pick another media'),
                    'delete'         => __('No media'),
                    'wrongExtension' => __('This extension is not allowed.'),
                ),
            ),
        );

        if (!empty($renderer['renderer_options'])) {
            $renderer_options = \Arr::merge($renderer_options, $renderer['renderer_options']);
        }
        unset($renderer['renderer_options']);

        return array($renderer, $renderer_options);
    }

    /**
     * Hydrate the options array to fill in the media URL for the specified value
     * @param array $options
     * @param int   $media_id
     */
    protected static function hydrate_options(&$options, $attributes = array())
    {
        if (!empty($attributes['value'])) {
            $media = \Novius\OnlineMediaFiles\Model_Media::find($attributes['value']);
            if (!empty($media)) {
                $options['inputFileThumb']['file'] = $media->thumbnail();
            }
        }
        if (!empty($attributes['required'])) {
            $options['inputFileThumb']['allowDelete'] = false;
        }
    }

    /**
     * Generates the JavaScript to initialise the renderer
     *
     * @param   string  HTML ID attribute of the <input> tag
     * @return string JavaScript to execute to initialise the renderer
     */
    protected static function js_init($id)
    {
        return \View::forge('novius_onlinemediafiles::admin/renderer/media', array(
            'id' => $id,
        ), false);
    }
}
