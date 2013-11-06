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

class Driver_Youtube extends Driver {

	protected $identifier       = false;

    public function compatible() {
        // Check if the driver is compatible by extracting the identifier from the url
        return ($this->url() && $this->identifier(false));
    }

	public function display($params = array()) {
        return parent::display(\Arr::merge(array(
			'attributes'	=> array(
				'src'				=> '//www.youtube.com/embed/'.$this->identifier().'?&wmode=opaque',
				'width'				=> 560,
				'height'			=> 315,
				'frameborder'		=> '0',
				'allowfullscreen'	=> true,
			)
		), $params));
	}

    /**
     * Fetch the online media attributes (title, description, metadatas...)
     *
     * @return bool|mixed
     */
    public function fetch() {
        if (!$this->identifier()) {
            return false;
        }

        // Call the youtube API
        $api_url = 'http://gdata.youtube.com/feeds/api/videos/'.$this->identifier().'?v=2&alt=jsonc';

		// Check if the API is up
		if (!static::ping($api_url)) {
			return false;
		}

		// Get the json response
		$response = ($json = file_get_contents($api_url)) ? json_decode($json) : false;
		if (empty($response) || empty($response->data)) {
			return false;
		}

        // Title is required
        if (empty($response->data->title)) {
            return false;
        }

        // Build attributes
        $attributes = array(
            'title'         => (string) $response->data->title,
            'description'   => (string) (!empty($response->data->description) ? $response->data->description : ''),
            'thumbnail'     => false,
            'metadatas'     => (array) $response->data,
        );

        // Get the biggest thumbnail
        if (!empty($response->data->thumbnail->hqDefault)) {
            $attributes['thumbnail'] = (string) $response->data->thumbnail->hqDefault;
        } elseif (!empty($response->data->thumbnail->sqDefault)) {
            $attributes['thumbnail'] = (string) $response->data->thumbnail->sqDefault;
        }

        return $this->attributes($attributes);
    }

    public function cleanUrl() {
        if ($this->identifier()) {
            return 'http://www.youtube.com/watch?v='.$this->identifier();
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
                case 'www.youtube.com':
                case 'youtube.com':
                case 'm.youtube.com':
                    // Extract ID from query args
                    parse_str($parts['query'], $args);
                    if ($args['v']) {
                        $this->identifier = $args['v'];
                    }
                    break;
                // Minified pattern
                case 'youtu.be':
                    // Extract ID from path
                    $args = array_filter(explode('/', $parts['path']));
                    if (count($args) > 0) {
                        $this->identifier = reset($args);
                    }
                    break;
            }
        }
        return $this->identifier;
    }
}