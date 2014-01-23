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

class Model_Link extends \Nos\Orm\Model
{
    protected static $_table_name = 'onlinemediafiles_link';
    protected static $_primary_key = array('onli_id');

    protected static $_properties = array(
        'onli_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'onli_from_table' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'onli_foreign_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'onli_key' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'onli_onme_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
    );

    protected static $_has_one = array();
    protected static $_many_many = array();

    protected static $_has_many = array();
    protected static $_belongs_to = array(
        'media' => array(
            'key_from' => 'onli_onme_id',
            'model_to' => 'Novius\OnlineMediaFiles\Model_Media',
            'key_to' => 'onme_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );
}
