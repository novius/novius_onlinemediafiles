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

class Model_Media extends \Nos\Orm\Model
{
    protected static $_table_name = 'novius_onlinemediafiles';
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
     * Builds the driver using the item
     *
     * @param bool $force
     * @return bool|Driver
     */
    public function driver($force = false) {
        if ($this->driver === false || $force) {
            $this->driver = Driver::buildFromMedia($this);
        }
        return $this->driver;
    }

    /**
     * Finds a compatible driver and fetch its attributes
     *
     * @param bool $save Don't save the item if false
     * @return bool
     * @throws \Exception
     */
    public function sync($save = true) {
        if (empty($this->onme_url)) {
            return false;
        }

        // Loads available drivers
        $config = \Config::load('novius_onlinemediafiles::config', true);
        $drivers = \Arr::get($config, 'drivers');
        if (empty($drivers)) {
            throw new \Exception(__('No driver available'));
        }

        // Search through available drivers
        foreach ($drivers as $driver_name) {

            // Builds the driver with the supplied url
            $driver = Driver::build($driver_name, $this->onme_url);

            // Checks whether the driver is compatible
            if (empty($driver) || !$driver->compatible()) {
                continue;
            }

            // Extrait les attributs du média internet (titre, description...)
            $maxPass = 30; // le nombre de passe par défaut
            $timeToWait = 1; // En secondes
            $pass = 0; // passe courante

            // Par défaut il se peu que youtube mette un peu de temps à traiter la vidéo et que du coup les attributs
            // ne soient pas disponible immédiatement, du coup dans ce cas là on va aller les cherchers toutes les secondes
            // 30 fois au maximum (30sec) si au bout des 30sec on a toujours rien, alors on considère que l'upload
            // a foiré.
            $attributes = $driver->fetch();
            while(empty($attributes)) {
                $attributes = $driver->fetch();
                sleep($timeToWait);

                if(++$pass >= $maxPass) {
                    throw new \Exception(__('Online media not found, please check the URL'));
                }
            }


            // Set new driver
            $this->driver = $driver;

            // Set new attributes
            $this->onme_driver_name = $driver_name;
            $this->onme_title       = \Arr::get($attributes, 'title');
            $this->onme_description = \Arr::get($attributes, 'description');
            $this->onme_thumbnail   = \Arr::get($attributes, 'thumbnail');
            $this->onme_metadatas   = serialize(\Arr::get($attributes, 'metadatas'));

            return $save ? $this->save() : true;
        }

        // No driver found
        return false;
    }

    /**
     * Returns the online media thumbnail URL
     *
     * @return mixed
     */
    public function thumbnail() {
        return $this->driver() ? $this->driver()->thumbnail() : null;
    }

    /**
     * Returns the HTML code to display the online media
     *
     * @param array $params
     * @return mixed|string
     */
    public function display($params = array()) {
        return $this->driver() ? $this->driver()->display($params) : null;
    }
}
