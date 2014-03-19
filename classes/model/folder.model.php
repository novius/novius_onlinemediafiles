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

class Model_Folder extends \Nos\Orm\Model
{
    protected static $_table_name = 'novius_onlinemediafiles_folder';
    protected static $_primary_key = array('onfo_id');

    protected static $_title_property = 'onfo_title';
    protected static $_properties = array(
        'onfo_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'onfo_parent_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'onfo_path' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'onfo_dir_name' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'onfo_title' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'onfo_created_at' => array(
            'data_type' => 'timestamp',
            'null' => false,
        ),
        'onfo_updated_at' => array(
            'data_type' => 'timestamp',
            'null' => false,
        ),
    );

    protected static $_has_one = array();
    protected static $_many_many = array();

    protected static $_has_many = array(
        'children' => array(
            'key_from'       => 'onfo_id',
            'model_to'       => 'Novius\OnlineMediaFiles\Model_Folder',
            'key_to'         => 'onfo_parent_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'media' => array(
            'key_from'       => 'onfo_id',
            'model_to'       => 'Novius\OnlineMediaFiles\Model_Media',
            'key_to'         => 'onme_folder_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );

    protected static $_belongs_to = array(
        'parent' => array(
            'key_from'       => 'onfo_parent_id',
            'model_to'       => 'Novius\OnlineMediaFiles\Model_Folder',
            'key_to'         => 'onfo_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
            'property'=>'onfo_created_at'
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
            'property'=>'onfo_updated_at'
        )
    );

    protected static $_behaviours = array(
        'Nos\Orm_Behaviour_Tree' => array(
            'events' => array('before'),
            'parent_relation' => 'parent',
            'children_relation' => 'children',
        ),
        'Nos\Orm_Behaviour_Virtualpath' => array(
            'events' => array('before_save', 'after_save', 'change_parent'),
            'virtual_name_property' => 'onfo_dir_name',
            'virtual_path_property' => 'onfo_path',
            'extension_property' => '/',
        ),
    );

    protected $_data_events = array();

    public function count_media()
    {
        /// get_ids_children($include_self)
        $folder_ids = $this->get_ids_children(true);

        return Model_Media::count(array(
            'where' => array(
                array('onme_folder_id', 'IN', $folder_ids),
            ),
        ));
    }

    public function count_media_usage()
    {
        $folder_ids = $this->get_ids_children(true);

        return Model_Link::count(array(
            'related' => array('media'),
            'where' => array(
                array('media.onme_folder_id', 'IN', $folder_ids),
            ),
        ));
    }

//    public function _event_before_save()
//    {
//        parent::_event_before_save();
//        $diff = $this->get_diff();
//
//        if (!empty($diff[0]['onfo_path'])) {
//            $this->_data_events = $diff;
//        }
//    }

//    public function _event_after_save()
//    {
//        $diff = $this->_data_events;
//
//        if (!empty($diff[0]['onfo_path'])) {
//            \DB::update(Model_Media::table())
//                    ->set(array(
//                    'media_path' => \DB::expr('REPLACE(media_path, '.\DB::escape($diff[0]['onfo_path']).', '.\DB::escape($diff[1]['onfo_path']).')'),
//                ))
//                ->where('media_path', 'LIKE', $diff[0]['onfo_path'].'%')
//                ->execute();
//        }
//    }
}
