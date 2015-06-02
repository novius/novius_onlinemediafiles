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

use \Nos\Nos;
use \Fuel\Core\Inflector;
use \Nos\Config_Data;

abstract class Driver {

    // Required fields in driver's config file
    protected $required_fields  = array();

    protected $url              = false;
    protected $attributes       = array();

    protected $app_config       = array();
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
        $this->driver_name = static::buildDriverName($this->class_name);

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
     * Build driver name from class
     *
     * @param $driver_class
     * @return string
     */
    public static function buildDriverName($driver_class) {
        return substr($driver_class, strrpos($driver_class, '\\') + 1);
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
        $this->config = \Arr::merge_assoc($this->config, $config);

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
                $config = \Arr::merge_assoc($config, \Config::load($dependency . '::' . $file, true));
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
        $this->config = \Arr::merge_assoc($this->config, $config);

        // Load app config
        $this->app_config = \Config::load('config', true);
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
            $this->attributes[$name] = $data;
        }
        // Return attribute
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Enhanced parse_url()
     *
     * @param $url
     * @param $component
     * @return mixed
     */
    public static function parseUrl($url, $component = -1) {
        // Add http if necessary
        if (substr($url, 0, 2) == '//') {
            $url = 'http:'.$url;
        } elseif (!preg_match('`^https?://`', $url)) {
            $url = 'http://'.$url;
        }
        return parse_url($url, $component);
    }

    /**
     * Check if $url uses the SSL protocol
     *
     * @param $url
     * @return bool
     */
    public static function isSSL($url) {
        return (strtolower(substr($url, 0, 8)) == 'https://');
    }

    /**
     * Return the content of an $url
     *
     * @param $url
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function get_url_content($url) {
        $http_timeout = \Arr::get($this->config, 'http.timeout', 5);
        $http_user_agent = \Arr::get($_SERVER, 'HTTP_USER_AGENT', 'Custom');

        // Curl
        if (function_exists('curl_init')) {

            // Create the context
            $context = curl_init();
            curl_setopt($context, CURLOPT_URL, $url);
            curl_setopt($context, CURLOPT_HEADER, 0);
            curl_setopt($context, CURLOPT_VERBOSE, 1);
            curl_setopt($context, CURLOPT_TIMEOUT, $http_timeout);
            curl_setopt($context, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($context, CURLOPT_USERAGENT, $http_user_agent);
            curl_setopt($context, CURLOPT_SSL_VERIFYPEER, static::isSSL($url));

            // Get the response
            $response = curl_exec($context);

            // Check the status code of the HTTP response (200 = success)
            if (curl_getinfo($context, CURLINFO_HTTP_CODE) != 200) {
                return false;
            }
        }

        // Native
        else {

            // We need the OpenSSL module for SSL urls
            if (static::isSSL($url) && !extension_loaded('openssl')) {
                throw new \Exception(__('Your server is currently not compatible with this media. Please contact your administrator to enable SSL support.'));
            }

            // Create the context
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'GET',
                    'user_agent' => $http_user_agent,
                    'timeout' => $http_timeout,
                )
            ));

            // Get the response
            $response = file_get_contents($url, false, $context);
        }

        return $response;
    }

    /**
     * Convert recursively objects in array
     *
     * @param $obj
     * @param $max_depth
     * @return object
     */
    public static function objectToArray($obj, $max_depth = null) {
        $arr = array();
        if (is_object($obj)) {
            $obj = get_object_vars($obj);
        }
        if (is_array($obj)) {
            if (!is_null($max_depth) && $max_depth < 0) {
                return null;
            }
            foreach ($obj as $key => $val) {
                $arr[$key] = static::objectToArray($val, !is_null($max_depth) ? $max_depth - 1 : $max_depth);
            }
            return $arr;
        }
        return $obj;
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
     * Return the driver's icon
     *
     * @param int $size
     * @return mixed
     */
    public function driverIcon($size = 16) {
        $icon = \Arr::get($this->config, 'icon.'.$size);

        return static::driverIconPath($size, $icon);
    }

    /**
     * Return the driver's icon path
     *
     * @param $size
     * @param $icon
     * @return string
     */
    public static function driverIconPath($size, $icon) {
        if (mb_strpos($icon, '/') === false) {
            $icon = 'static/apps/novius_onlinemediafiles/icons/'.$size.'/'.$icon;
        }
        return $icon;
    }

    /**
     * Return the driver's class name
     * @return bool|string
     */
    public function className() {
        // Get current class name
        $class = $this->class_name;
        !\Str::starts_with($class, '\\') and $class = '\\'.$class;

        // Get current app namespace
        $namespace = Config_Data::get('app_installed.novius_onlinemediafiles.namespace', null);
        !\Str::starts_with($namespace, '\\') and $namespace = '\\'.$namespace;

        // Remove namespace for drivers located in this app
        if (\Str::starts_with($class, $namespace)) {
            $class = Inflector::denamespace($class);
        }

        return $class;
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
     * Returns the clean URL of the online media
     *
     * @return bool|string
     */
    public function cleanUrl() {
        return $this->url();
    }

    /**
     * Return the host
     *
     * @param bool $with_subdomain
     * @return mixed
     */
    public function host($with_subdomain = true) {
        if ($this->url()) {
            $host = self::parseUrl($this->url(), PHP_URL_HOST);
            if (!$with_subdomain) {
                // Remove subdomains
                $host = implode('.', array_slice(explode('.', $host), -2));
            }
            return $host;
        }
        return false;
    }

    /**
     * Return the title
     *
     * @return mixed
     */
    public function title() {
        return $this->attribute('title');
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
     * Display the embedded online media
     *
     * @param array $params
     * @return mixed|string
     */
    public function display($params = array()) {

        // Build display params
        $params = \Arr::merge(
            \Arr::get($this->config, 'display', array()),
            array(
                'attributes'	=> array(
                    'src' => $this->url(),
                )
            ),
            $params
        );

        // Filter null attributes
        $attributes = \Arr::filter_recursive($params['attributes'], function($value) {
            return ($value !== null);
        });

        // Build the responsive configuration
        $config_responsive = \Arr::merge(
            (array) \Arr::get($this->app_config, 'responsive', array()),
            (array) \Arr::get($params, 'responsive', array())
        );

        $wrapper_classes = array();

        // Alignment
        $align = \Arr::get($params, 'align');
        if (\Arr::get($this->app_config, 'alignment.enabled') && !empty($align)) {
            $wrapper_classes[] = 'onlinemediafiles-align-'.$align;
        }

        // Responsive
        if (\Arr::get($config_responsive, 'enabled')) {
            $wrapper_classes[] = 'onlinemediafiles-fluid-wrapper';
        }

        // Builds the iframe
        $display = '<iframe'.(!empty($attributes) ? ' '.array_to_attr($attributes) : '').'></iframe>';

        // Wraps the media if there are wrapper classes
        if (!empty($wrapper_classes)) {
            $display = sprintf('<div class="%s">%s</div>', implode(' ', $wrapper_classes), $display);
        }

        // Appends the front stylesheet
        $css_path = \Arr::get($this->app_config, 'front_css_path');
        if (!empty($css_path)) {
            $main_controller = Nos::main_controller();
            if (!empty($main_controller) && method_exists($main_controller, 'addCss')) {
                $main_controller->addCss($css_path, false);
            }
        }

        // Apply the template
        $display = str_replace('{display}', $display, \Arr::get($params, 'template'));

        return $display;
    }

    /**
     * Show a preview of the online media
     *
     * @param array $params
     * @return mixed|string
     */
    public function preview($params = array()) {
        $params = \Arr::merge(array(
            'template'	=> '{preview}',
        ), $params);

        if (!$this->thumbnail()) {
            return '';
        }

        $preview = '<img src="'.$this->thumbnail().'" title="'.e($this->title()).'" alt="'.e($this->title()).'" />';
        $preview = str_replace('{preview}', $preview, $params['template']);
        return $preview;
    }

    /**
     * Check if the url is compatible with the driver
     *
     * @return mixed
     */
    abstract public function compatible();

    /**
     * Fetch the attributes (title, description, thumbnail...)
     *
     * @return mixed
     */
    abstract public function fetch();
}
