<?php

namespace Novius\OnlineMediaFiles;

class Model_Media extends \Nos\Orm\Model
{
    public static $services_url = array(
        'youtube.com',
        'www.youtube.com',
        'vimeo.com',
        'www.vimeo.com',
        'dailymotion.com',
        'www.dailymotion.com',
    );

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
        'onme_description' => array(
            'default' => null,
            'data_type' => 'text',
            'null' => true,
        ),
        'onme_thumbnail' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => true,
        ),
        'onme_metadatas' => array(
            'default' => null,
            'data_type' => 'text',
            'null' => true,
        ),
        'onme_driver_name' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'onme_url' => array(
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

    public static function accept_service ($url)
    {
        $url = parse_url($url);
        if (!in_array($url['path'], self::$services_url)) {
            return false;
        } else {
            return true;
        }
    }
}
