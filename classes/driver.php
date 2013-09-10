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

abstract class Driver {

    // Required fields in driver's config file
    protected $required_fields  = array();

    // Url of the online media (set by the constructor)
    protected $url            = false;

    // Unique identifier of the online media (id, clean url...)
    protected $identifier     = false;

    protected $config           = array();
    protected $driver_name      = false;
    protected $class_name       = false;

    static private $forged      = array();

    /**
     * Constructor
     *
     * @param $url
     * @throws \Exception
     */
    public function __construct($url) {

        $this->class_name = get_class($this);
        $this->driver_name = substr($this->class_name, strrpos($this->class_name, '\\') + 1);

        // Load driver config
        $this->loadConfig();

        $this->url = $url;

        // Check required fields
        if (!empty($this->required_fields)) {
            foreach ($this->required_fields as $name => $field) {
                if (empty($this->config[$field])) {
                    throw new \Exception('Authentication: field `'. (is_numeric($name) ? $field : $name) .'` is missing in '. $this->driver_name .' configuration');
                }
            }
        }
    }

    /**
     * Load config and dependencies' config.
     */
    protected function loadConfig() {
        // Load the driver's common config
        list($application, $file) = \Config::configFile(get_parent_class($this));
        $config = \Config::load($application . '::' . $file, true);
        if (!is_array($config)) {
            $config = array();
        }

        // Merge with the current config
        $this->config = \Arr::merge($this->config, $config);

        // Load the driver's custom config
        list($application, $file) = \Config::configFile($this->class_name);
        $config = \Config::load($application . '::' . $file, true);
        if (!is_array($config)) {
            $config = array();
        }

        // Load dependencies config
        $dependencies = \Nos\Config_Data::get('app_dependencies', array());
        if (!empty($dependencies[$application])) {
            foreach ($dependencies[$application] as $app => $dependency) {
                if (is_array($dependency)) {
                    $dependency = $app; // Compatibilité Novius OS 0.2.1
                }
                $config = \Arr::merge($config, \Config::load($dependency . '::' . $file, true));
            }
        }

        // Filter null values
        $config = \Arr::recursive_filter(
            $config,
            function ($var) {
                return $var !== null;
            }
        );

        // Merge with the current config
        $this->config = \Arr::merge($this->config, $config);
    }

    /**
     * @return bool|string
     */
    public function getDriverName() {
        return $this->driver_name;
    }

    /**
     * @return bool|string
     */
    public function getClass() {
        return $this->class_name;
    }

    /**
     * Return the url of the online media
     *
     * @return bool
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Return the clean url of the online media
     *
     * @return bool
     */
    public function getCleanUrl() {
        return $this->getUrl();
    }

    /**
     * Return the unique identifier of the online media
     *
     * @return bool
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    /**
     * Execute a callback with a variable number of arguments and return true or false
     * - first argument is the callback
     * - following arguments are passed to the callback
     *
     * If the callback is empty or not callable then false is returned
     *
     * @return bool|mixed
     */
    protected function executeCallback() {
        $args = func_get_args();
        // First argument is the callback
        if (count($args) > 0) {
            $callback = array_shift($args);
            // Check if it is a valid callbackx
            if (empty($callback) || !is_callable($callback)) {
                return false;
            }
            // We need at least one argument
            return call_user_func_array($callback, count($args) > 0 ? $args : array());
        }
        return false;
    }

    /**
     * Apply a callback with a variable number of arguments
     * - first argument is the callback
     * - following arguments are passed to the callback
     *
     * If the callback is empty or not callable then the first passed args is returned
     *
     * @return bool|mixed
     */
    protected function applyCallback() {
        $args = func_get_args();
        // First argument is the callback
        if (count($args) > 0) {
            $callback = array_shift($args);
            // Check if it is a valid callback
            if (empty($callback) || !is_callable($callback)) {
                // Return the first argument if no callback defined
                return count($args) > 0 ? array_shift($args) : false;
            }
            // We need at least one argument
            return call_user_func_array($callback, count($args) > 0 ? $args : array());
        }
        return false;
    }

    /**
     * Get a column from a dataset or return a callback value
     *
     * @param $column_or_callback
     * @param $dataset
     * @return bool|mixed
     */
    protected function getColumnOrCallback($column_or_callback, $dataset) {
        if (!empty($column_or_callback)) {
            $dataset = (object) $dataset;
            if (is_callable($column_or_callback)) {
                return call_user_func($column_or_callback, $dataset);
            }
            return isset($dataset->{$column_or_callback}) ? $dataset->{$column_or_callback} : false;
        }
        return false;
    }

    /**
     * Forge a new instance
     *
     * @param $url
     * @param bool $force
     * @return mixed
     */
    public static function forge($url, $force = false) {
        $token = get_called_class() .'_'. $url;
        // Not forget yet ?
        if (!isset(self::$forged[$token]) || $force) {
            self::$forged[$token] = new static($url);
        }
        return self::$forged[$token];
    }

    public static function buildDriver($driver_name, $url) {
        // Build namespace for native driver
        if (\Str::sub($driver_name, 0, 1) != '\\') {
            if (\Str::sub($driver_name, 0, 7) != 'Driver_') {
                $driver_name = 'Driver_'.$driver_name;
            }
            $driver_name = '\Novius\OnlineMediaFiles\\'. $driver_name;
        }
        // Forge the driver
        if (class_exists($driver_name)) {
            return $driver_name::forge($url);
        }
        return false;
    }

    /**
     * Fetch the metadatas of the online media (title, description, thumbnail...)
     *
     * @return mixed
     */
    abstract public function fetch();

    /**
     * Check if the url is compatible with the driver
     *
     * @return mixed
     */
    abstract public function check();

}
