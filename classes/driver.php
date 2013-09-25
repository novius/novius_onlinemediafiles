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

    static protected $forged    = array();

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
                    throw new \Exception('OnlineMediaFiles: field `'.(is_numeric($name) ? $field : $name).'` is missing in '.$this->driver_name.' configuration');
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
        if (!isset(static::$forged[$token]) || $force) {
            static::$forged[$token] = new static($url);
        }
        return static::$forged[$token];
    }

    /**
     * Build a new driver
     *
     * @param $driver_name
     * @param $url
     * @param $force
     * @return bool
     */
    public static function build($driver_name, $url, $force = false) {
        // Build driver class
        $driver_class = Driver::buildDriverClass($driver_name);
        if (empty($driver_class)) {
            return false;
        }
        return $driver_class::forge($url, $force);
    }

    /**
     * Build a new driver from a media object
     *
     * @param $media
     * @return bool
     */
    public static function buildFromMedia($media) {
        // Get the driver class name
        $driver_class = static::buildDriverClass($media->onme_driver_name);
        if (empty($driver_class)) {
            return false;
        }

        // Forge the driver
        $driver = $driver_class::forge($media->onme_url);

        // Set the attributes from the media object
        $driver->attributes(array(
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
     * Get or set attributes
     *
     * @param bool|mixed $data
     * @return array
     */
    public function attributes($data = null) {
        if (is_array($data)) {
            // Set attributes
            $this->attributes = static::objectToArray($data);
        }
        // Return attributes
        return (array) $this->attributes;
    }

    /**
     * Get or set an attribute
     *
     * @param $name
     * @param null $data
     * @return mixed|null
     */
    public function attribute($name, $data = null) {
        // Set attribute
        if ($data !== null) {
            $attributes[$name] = $data;
        }
        // Return attribute
        $attributes = $this->attributes();
        return isset($attributes[$name]) ? $attributes[$name] : null;
    }

    /**
     * Enhanced parse_url()
     *
     * @param $url
     * @return mixed
     */
    public static function parseUrl($url) {
        // Add http if necessary
        if (substr($url, 0, 2) == '//') {
            $url = 'http:'.$url;
        } elseif (!preg_match('`^https?://`', $url)) {
            $url = 'http://'.$url;
        }
        return parse_url($url);
    }

    public static function objectToArray($obj) {
        $arrObj = is_object($obj) ? get_object_vars($obj) : $obj;
        if (!is_array($arrObj)) {
            return $obj;
        }
        foreach ($arrObj as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? static::objectToArray($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }

    /**
     * Return the driver's name
     *
     * @return bool|string
     */
    public function driverName() {
        return $this->driver_name;
    }

    /**
     * Return the driver's class name
     * @return bool|string
     */
    public function className() {
        return $this->class_name;
    }

    /**
     * Return the url of the online media
     *
     * @return bool
     */
    public function url() {
        return $this->url;
    }

    /**
     * Return the clean url of the online media
     *
     * @return bool
     */
    public function cleanUrl() {
        return $this->url();
    }

    /**
     * Return the thumbnail url
     *
     * @return mixed
     */
    public function thumbnail() {
        return $this->attribute('thumbnail');
    }

    /**
     * Get the description
     *
     * @return mixed
     */
    public function description() {
        return $this->attribute('description');
    }

    /**
     * Get the metadatas (additionnal fetched attributes)
     *
     * @return mixed
     */
    public function metadatas() {
        return $this->attribute('metadatas');
    }

    /**
     * Get the unique identifier
     *
     * @return bool
     */
    public function identifier() {
        return $this->identifier;
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
