<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

\Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common'));

return array(
    'model' => 'Novius\OnlineMediaFiles\Model_Folder',
    'order_by' => 'onfo_title',
    'input' => array(
        'key' => 'onme_folder_id'
    ),
    'appdesk' => array(
        'label'     => __('Folder'),
        'treeGrid' => array(
            'movable'   => false,
            'sortable'  => false,
        ),
    ),
);
