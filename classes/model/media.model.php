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
            'data_type' => 'serialize',
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

    protected static $_belongs_to = array(
        'folder' => array(
            'key_from'       => 'onme_folder_id',
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
            'property'=>'onme_created_at'
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
            'property'=>'onme_updated_at'
        ),
        'Orm\\Observer_Typing'
    );

    protected $driver           = false;

    /**
     * Construit le driver à partir du média distant
     *
     * @param bool $force
     * @return Driver
     */
    public function driver($force = false) {
        if ($this->driver === false || $force) {
            $this->driver = Driver::buildFromMedia($this);
        }
        return $this->driver;
    }

    /**
     * Synchronise le média distant (trouve le bon driver et fetch les attributs)
     *
     * @param bool $save
     * @return bool
     */
    public function sync($save = true) {
        $config = \Config::load('novius_onlinemediafiles::config', true);

        // Reset le driver courant
        $this->onme_driver_name = $this->driver = false;

        if (!empty($this->onme_url)) {
            // Search through available drivers
            foreach ($config['drivers'] as $driver_name) {
                // Build the driver with the supplied le driver avec l'url fournie
                if (($driver = Driver::build($driver_name, $this->onme_url))) {
                    // Is the driver compatible ?
                    if ($driver->compatible()) {
                        // Save the new driver
                        if (($attributes = $driver->fetch())) {
                            $this->onme_driver_name = $driver_name;
                            $this->driver = $driver;
                            // Save attributes
                            $this->onme_title = $attributes['title'];
                            $this->onme_description = $attributes['description'];
                            $this->onme_thumbnail = $attributes['thumbnail'];
                            $this->onme_metadatas = serialize($attributes['metadatas']);
                            break;
                        }
                    }
                }
            }
        }
        if (!$this->driver) {
            return false;
        }
        return $save ? $this->save() : true;
    }

    public function thumbnail() {
        return $this->driver()->thumbnail();
    }

    public function display($params = array()) {
        return $this->driver()->display($params);
    }
}
