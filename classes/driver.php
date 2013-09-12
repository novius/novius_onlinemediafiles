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
    protected $identifier       = false;
    protected $attributes       = array();

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
     * Forge a new instance of the driver
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

    /**
     * Build a new driver
     *
     * @param $driver_name
     * @param $url
     * @return bool
     */
    public static function build($driver_name, $url) {
        // Build driver class
        $driver_class = Driver::buildDriverClass($driver_name);
        if (empty($driver_class)) {
            return false;
        }
        return $driver_class::forge($url);
    }

    /**
     * Build a new driver from a media object
     *
     * @param $media
     * @return bool
     */
    public static function buildFromMedia($media) {

        // Get the driver class name
        $driver_class = self::buildDriverClass($media->onme_driver_name);
        if (empty($driver_class)) {
            return false;
        }

        // Forge the driver
        $driver = $driver_class::forge($media->onme_url);

        // Set the attributes from the media object
        $driver->setAttributes(array(
            'title'         => $media->onme_title,
            'description'   => $media->onme_description,
            'thumbnail'     => $media->onme_thumbnail,
            'metadatas'     => $media->onme_metadatas,
        ));

        return $driver;
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
     * Build the driver class from the driver name
     *
     * @param $driver_name
     * @return bool|string
     */
    public static function buildDriverClass($driver_name) {
        if (empty($driver_name)) {
            return false;
        }
        // Check if no namespace
        if (\Str::sub($driver_name, 0, 1) != '\\') {
            // Build namespace for native driver
            if (\Str::sub($driver_name, 0, 7) != 'Driver_') {
                $driver_name = 'Driver_'.$driver_name;
            }
            $driver_name = '\Novius\OnlineMediaFiles\\'. $driver_name;
        }
        return class_exists($driver_name) ? $driver_name : false;
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

    public function getAttributes($auto_fetch = true) {
        if ($auto_fetch && empty($this->attributes)) {
            // Fetch attributes if not set
            $this->fetch();
        }
        return $this->attributes;
    }

    public function setAttributes($attributes) {
        $this->attributes = $attributes;
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

    public static function parseUrl($url) {
        // Add http if necessary
        if (substr($url, 0, 2) == '//') {
            $url = 'http:'.$url;
        } elseif (!preg_match('`^https?://`', $url)) {
            $url = 'http://'.$url;
        }
        return parse_url($url);
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

    /**
     * Show the online media
     *
     * @return mixed
     */
    abstract public function display();

    /**
     * Show a preview of the online media
     *
     * @return mixed
     */
    abstract public function preview();

}
