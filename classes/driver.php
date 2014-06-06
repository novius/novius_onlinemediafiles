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

abstract class Driver {

    // Required fields in driver's config file
    protected $required_fields  = array();

    protected $url            = false;
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
	 * Ping the given url (check HTTP status 200)
	 *
	 * @param $url
	 * @return bool
     * @throws \Exception
     */
    public static function ping($url) {
        // Ping over https is not supported if the OpenSSL module is not installed
        if (!extension_loaded('openssl') && static::isSSL($url)) {
            throw new \Exception(__('Your server is currently not compatible with this media. Please contact your administrator to enable SSL support.'));
        }

        // Grab the headers and check the HTTP status code
        $headers = get_headers($url, 1);
        return strpos($headers[0], '200 OK') !== false;
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
	 * Convert recursively ojects in array
	 *
	 * @param $obj
	 * @return object
	 */
	public static function objectToArray($obj) {
		if (is_array($obj)) {
			return array_map('static::objectToArray', $obj);
		} elseif (is_object($obj)) {
			$arr = array();
			$obj = get_object_vars($obj);
			foreach ($obj as $key => $val) {
				$arr[$key] = static::objectToArray($val);
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
        // Default params
		$params = \Arr::merge(array(
			'template'		=> '{display}',
			'attributes'	=> array(
				'src'			=> $this->url(),
				'width'			=> 480,
				'height'		=> 270,
				'frameborder'	=> '0',
			)
		), $params);

        // Filter null attributes
		$attributes = \Arr::filter_recursive($params['attributes'], function($value) {
			return ($value !== null);
		});

        // Builds the iframe
		$display = '<iframe'.(!empty($attributes) ? ' '.array_to_attr($attributes) : '').'></iframe>';
		$display = str_replace('{display}', $display, $params['template']);

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
