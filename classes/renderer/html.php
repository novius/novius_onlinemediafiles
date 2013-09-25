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

class Renderer_HTML extends \Fieldset_Field
{
    public function __construct($name, $label = '', array $renderer = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset = null)
    {
        parent::__construct($name, $label, $renderer, $rules, $fieldset);
    }

    /**
     * How to display the field
     * @return type
     */
    public function build()
    {
        return str_replace('{field}', (string) $this->value, $this->template);
    }
}
