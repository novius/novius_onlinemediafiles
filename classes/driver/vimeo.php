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

class Driver_Vimeo extends Driver {

	protected $identifier       = false;

    /**
     * Checks whether the driver is compatible with the online media
     *
     * @return bool|mixed
     */
    public function compatible() {
        // Check if the driver is compatible by extracting the identifier from the url
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
				'src'					=> '//player.vimeo.com/video/'.$this->identifier(),
				'width'					=> 500,
				'height'				=> 281,
				'frameborder'			=> '0',
				'webkitallowfullscreen'	=> true,
				'mozallowfullscreen'	=> true,
				'allowfullscreen'		=> true,
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

        // Try different methods to get video attributes
        $methods = array(
            'fetch_from_public_api',
            'fetch_from_private_api',
            'fetch_from_player',
        );
        $attributes = null;
        foreach ($methods as $method) {
            $response = call_user_func(array($this, $method));
            // Title and thumbnail are required
            if (!empty($response) && !empty($response['title']) & !empty($response['thumbnail'])) {
                $attributes = $response;
                break;
            }
        }

        // Nothing found
        if (empty($attributes)) {
            return false;
        }

        return $this->attributes($attributes);
    }

    /**
     * Try to fetch video by private API (see vimeo's driver configuration file)
     *
     * @return array|bool
     */
    public function fetch_from_private_api() {
        // Vimeo API
        if (!class_exists('Novius\OnlineMediaFiles\Package\Vimeo_Api')) {
            return false;
        }

        // Get private API credentials
        $client_id = \Arr::get($this->config, 'private_api.client_id');
        $client_secret = \Arr::get($this->config, 'private_api.client_secret');
        if (empty($client_id) || empty($client_secret)) {
            return false;
        }

        // Initialize Vimeo API
        $api = new Package\Vimeo_Api($client_id, $client_secret);

        // Set access token
        $api->setToken(\Arr::get($this->config, 'private_api.access_token'));

        // Request video by id
        $response = $api->request('/videos/'.$this->identifier());
        $body = \Arr::get($response, 'body');
        if (empty($body)) {
            return false;
        }

        // Build attributes
        $attributes = array(
            'title'         => strval(\Arr::get($body, 'name')),
            'description'   => strval(\Arr::get($body, 'description')),
            'thumbnail'     => false,
            'metadatas'     => array_filter((array) static::objectToArray($body, 0))
        );

        // Thumbnail
        $pictures = \Arr::get($body, 'pictures.sizes');
        if (!empty($pictures)) {
            $biggest = null;
            foreach ($pictures as $thumb) {
                if (empty($biggest) || \Arr::get($thumb, 'width') > \Arr::get($biggest, 'width')) {
                    $biggest = $thumb;
                }
            }
            \Arr::set($attributes, 'thumbnail', \Arr::get($biggest, 'link'));
        }

        return $attributes;
    }

    /**
     * Try to fetch video from public API
     *
     * @return array|bool
     */
    public function fetch_from_public_api() {
        // Build the API url
        $api_url = 'https://vimeo.com/api/v2/video/'.$this->identifier().'.json';

        // Get the the JSON response
        $response = json_decode(static::get_url_content($api_url));
        !is_array($response) or ($response = reset($response));
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
            'thumbnail'     => false,
            'metadatas'     => (array) $response,
        );

        // Get the biggest thumbnail
        if (!empty($response->thumbnail_large)) {
            $attributes['thumbnail'] = $response->thumbnail_large;
        } elseif (!empty($response->thumbnail_medium)) {
            $attributes['thumbnail'] = $response->thumbnail_medium;
        } elseif (!empty($response->thumbnail_small)) {
            $attributes['thumbnail'] = $response->thumbnail_small;
        }

        // Sanitize description
        $attributes['description'] = preg_replace('`<br\s*/?>`i', "\n", $attributes['description']);

        return $attributes;
    }

    /**
     * Try to fetch video from player's page
     *
     * @return null
     */
    public function fetch_from_player() {
        // Build attributes
        $attributes = array(
            'title'         => null,
            'description'   => null,
            'thumbnail'     => null,
            'metadatas'     => array(),
        );

        // Get player content
        $player_url = 'https://player.vimeo.com/video/'.$this->identifier();
        $player_content = static::get_url_content($player_url);

        // Extract title
        if (preg_match('`"title"\s*:\s*"([^"]+)"`si', $player_content, $out)) {
            $attributes['title'] = $out[1];
        }

        // Extract thumbnail
        if (preg_match_all('`((?:https?:)?//[^/]+/video/[^\.]+\.jpg)`si', $player_content, $out)) {
            $thumbs = array_unique(\Arr::pluck($out, '1'));
            $attributes['thumbnail'] = reset($thumbs);
            $attributes['metadatas']['thumbnails'] = $thumbs;
        }

        return $attributes;
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
                case 'www.vimeo.com':
                case 'vimeo.com':
                    // Extract ID from path
                    $args = array_values(array_filter(explode('/', $parts['path'])));
                    // Extract the identifier
                    $identifier = reset($args);
                    if (!empty($identifier) && ctype_digit($identifier)) {
                        $this->identifier = $identifier;
                    }
                    break;
                // Embed pattern
                case 'player.vimeo.com':
                    $args = array_values(array_filter(explode('/', $parts['path'])));
                    // Check the "video" part
                    if (reset($args) == 'video') {
                        array_shift($args);
                    }
                    $identifier = reset($args);
                    if (!empty($identifier) && ctype_digit($identifier)) {
                        $this->identifier = $identifier;
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
            return 'https://www.vimeo.com/video/'.$this->identifier();
        }
        return $this->url();
    }
}
