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

class Driver_Dailymotion extends Driver {

	protected $identifier       = false;

    /**
     * Checks whether the driver is compatible with the online media
     *
     * @return bool|mixed
     */
    public function compatible() {
        // Checks whether the driver is compatible by extracting the identifier from the url
        return ($this->url() && $this->identifier(false));
    }

    /**
     * Returns the HTML code to embed the online media
     *
     * @param array $params
     * @return mixed|string
     */
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
		if (empty($response) || empty($response->data)) {
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
                    // Try to match the video ID
                    if (preg_match('`^(?:/embed)?/video/([^_#\?]+)`', $parts['path'], $out)) {
                        $this->identifier = $out[1];
                    }
                    break;
            }
        }
        return $this->identifier;
    }

    /**
     * Returns the clean URL of the online media
     *
     * @return bool|string
     */
    public function cleanUrl() {
        if ($this->identifier()) {
            return 'http://www.dailymotion.com/video/'.$this->identifier();
        }
        return $this->url();
    }
}
