<?php

namespace Novius\OnlineMediaFiles;

class Model_Media extends \Nos\Orm\Model
{
    protected static $_table_name = 'onlinemediafiles';
    protected static $_primary_key = array('onme_id');

    protected static $_title_property = 'onme_title';
    protected static $_properties = array(
        'onme_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'onme_folder_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'onme_title' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'onme_created_at' => array(
            'data_type' => 'timestamp',
            'null' => false,
        ),
        'onme_updated_at' => array(
            'data_type' => 'timestamp',
            'null' => false,
        ),
    );

    protected static $_has_one = array();
    protected static $_many_many = array();

    protected static $_belongs_to = array(
        'folder' => array(
            'key_from'       => 'onme_folder_id',
            'model_to'       => 'Novius\OnlineMediaFiles\Model_Folder',
            'key_to'         => 'onfo_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );

    protected static $_has_many = array(
        'link' => array(
            'key_from' => 'onme_id',
            'model_to' => 'Novius\OnlineMediaFiles\Model_Link',
            'key_to' => 'onli_onme_id',
            'cascade_save' => false,
            'cascade_delete' => false,
        ),
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
            'property'=>'onme_created_at'
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
            'property'=>'onme_updated_at'
        )
    );
}
