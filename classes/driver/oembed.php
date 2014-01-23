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

class Driver_Oembed extends Driver {

	protected $checked		= array();

    /**
     * Checks whether the driver is compatible with the online media
     *
     * @return bool|mixed
     */
    public function compatible() {
		if (!$this->cleanUrl()) {
			return false;
		}

		// Build the API url
		$api_url = $this->apiUrl(array(
            'parameters' => array(
			    'url' => $this->cleanUrl()
            )
		));

		// Check if oembed is compatible on this host
		if (!isset($this->checked[$api_url])) {
			$this->checked[$api_url] = static::ping($api_url);
		}
		return $this->checked[$api_url];
    }

    /**
     * Returns the HTML code to embed the online media
     *
     * @param array $params
     * @return mixed|string
     */
    public function display($params = array()) {
		$params = \Arr::merge(array(
			'template'	=> '{display}',
		), $params);

		$display = \Arr::get((array) $this->metadatas(), 'html', '');
		$display = str_replace('{display}', $display, $params['template']);
		return $display;
    }

    /**
     * Fetch the online media attributes (title, description, metadatas...)
     *
     * @return bool|mixed
     */
	public function fetch() {
		if (!$this->cleanUrl()) {
			return false;
		}

		// Build the API url
        $api_url = $this->apiUrl(array(
            'parameters' => array(
                'url' => $this->cleanUrl()
            )
        ));

		// Check if the API is up
		if (!static::ping($api_url)) {
			return false;
		}

		// Get the json response
		$response = ($json = file_get_contents($api_url)) ? json_decode($json) : false;
		if (empty($response)) {
			return false;
		}

		// Build attributes
		$attributes = array(
			'title'  		=> (string) (!empty($response->title) ? $response->title : 'Sans titre'),
			'description'   => (string) (!empty($response->description) ? $response->description : ''),
			'thumbnail'     => (string) (!empty($response->thumbnail_url) ? $response->thumbnail_url : ''),
			'metadatas'     => static::objectToArray($response),
		);

		return $this->attributes($attributes);
    }

    /**
     * Builds and returns the oEmbed API url
     *
     * @param array $options
     * @return string
     */
    public function apiUrl($options = array()) {
        $options = \Arr::merge((array) \Arr::get($this->config, 'api'), $options);

        // Build scheme and host
        $parts = static::parseUrl(\Arr::get($options, 'url', $this->cleanUrl()));
		$url  = \Arr::get($options, 'scheme', $parts['scheme']).'://';
        $url .= \Arr::get($options, 'host', $parts['host']);

        // Add path
        $url .= $this->apiPath(\Arr::get($this->config, 'path'));

        // Add GET parameters
        $parameters = (array) \Arr::get($options, 'parameters');
        $url .= '?'.http_build_query((array) $parameters, null, '&');

        return $url;
	}

    /**
     * Returns the path to the oEmbed API
     *
     * @param null $default_path
     * @return mixed|null
     */
    public function apiPath($default_path = null) {
        // Try to match a custom API path by host
        $paths = array_filter((array) \Arr::get($this->config, 'path_mapping'));
        foreach ($paths as $host => $path) {
            if ($host == $this->host()) {
                return $path;
            }
        }
        // Returns default path
        return $default_path ? $default_path : \Arr::get($this->config, 'api.path');
    }
}