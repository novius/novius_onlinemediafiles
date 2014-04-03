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

class Driver_Dailymotion extends Driver {

	protected $identifier       = false;

    public function compatible() {
        // Check if the driver is compatible by extracting the identifier from the url
        return ($this->url() && $this->identifier(false));
    }

    public function display($params = array()) {
		return parent::display(\Arr::merge(array(
			'attributes'	=> array(
				'src'			=> '//www.dailymotion.com/embed/video/'.$this->identifier(),
				'width'			=> 480,
				'height'		=> 270,
				'frameborder'	=> '0',
			)
		), $params));
    }

    /**
     * Fetch the attributes of the online media (title, description...)
     *
     * @return bool|mixed
     */
    public function fetch() {
        if (!$this->identifier()) {
            return false;
        }

		// Build the API url
		$fields = (!empty($this->config['api_fields']) ? '?fields='.implode(',', $this->config['api_fields']) : '');
		$api_url = 'https://api.dailymotion.com/video/'.$this->identifier().$fields;

		// Check if the API is up
		if (!static::ping($api_url)) {
			return false;
		}

		// Get the json response
		$response = ($json = file_get_contents($api_url)) ? json_decode($json) : false;
		if (empty($response)) {
			return false;
		}

        // Title is required
        if (empty($response->title)) {
            return false;
        }

        // Build attributes
        $attributes = array(
            'title'         => (string) $response->title,
            'description'   => (string) (!empty($response->description) ? $response->description : ''),
            'thumbnail'     => (string) (!empty($response->thumbnail_url) ? $response->thumbnail_url : ''),
            'metadatas'     => (array) $response,
        );

        return $this->attributes($attributes);
    }

    public function cleanUrl() {
        if ($this->identifier()) {
            return 'http://www.dailymotion.com/video/'.$this->identifier();
        }
        return $this->url();
    }

    /**
     * Extract the unique identifier of the online media
     *
     * @param bool $from_cache
     * @return bool|mixed
     */
    public function identifier($from_cache = true) {
        if (!$from_cache || empty($this->identifier)) {
            $this->identifier = false;
            // Extract the identifier by host
            $parts = static::parseUrl($this->url());
            switch ($parts['host']) {
                // Standard pattern
                case 'www.dailymotion.com':
                case 'dailymotion.com':
                    // Extract ID from path
                    $args = array_filter(explode('/', $parts['path']));
                    // Ignore the "embed" part
                    if (reset($args) == 'embed') {
                        array_shift($args);
                    }
                    // Check the "video" part
                    if (reset($args) == 'video') {
                        array_shift($args);
                        // Extract the full identifier
                        $identifier = reset($args);
                        if (!empty($identifier)) {
                            // Extract only the ID
                            $identifier = explode('_', $identifier);
                            $this->identifier = reset($identifier);
                        }
                    }
                    break;
            }
        }
        return $this->identifier;
    }
}
