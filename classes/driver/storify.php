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

class Driver_Storify extends Driver {

    protected $identifiers = null;

    /**
     * Checks whether the driver is compatible with the online media
     *
     * @return bool|mixed
     */
    public function compatible() {
        // Check if the driver is compatible by extracting the identifier from the url
        return ($this->host(false) == 'storify.com');
    }

    /**
     * Get the story username
     *
     * @return mixed
     */
    public function getUsername() {
        return \Arr::get($this->identifiers(), 'username');
    }

    /**
     * Get the story slug
     *
     * @return mixed
     */
    public function getSlug() {
        return \Arr::get($this->identifiers(), 'slug');
    }

    /**
     * Extract the unique identifier of the online media
     *
     * @param bool $from_cache
     * @return bool|mixed
     */
    public function identifiers($from_cache = true) {
        if (!$from_cache || is_null($this->identifiers)) {
            $this->identifiers = array();
            // Extract username and slug from url
            $parts = static::parseUrl($this->url());
            list($username, $slug) = explode('/', trim($parts['path'], '/'));
            $slug = \Arr::get(preg_split('`[#\?]`', $slug), 0);
            if (!empty($username) && !empty($slug)) {
                $this->identifiers = array(
                    'username' => $username,
                    'slug' => $slug
                );
            }
        }
        return $this->identifiers;
    }

    /**
     * Returns the HTML code to embed the online media
     *
     * @param array $params
     * @return mixed|string
     */
	public function display($params = array()) {
        return \View::forge('novius_onlinemediafiles::front/driver/storify/embed', array(
            'driver' => $this,
		), $params)->render();
	}

    /**
     * Fetch the online media attributes (title, description, metadatas...)
     *
     * @return bool|mixed
     */
    public function fetch() {
        // Extract identifiers (username and slug)
        $identifiers = $this->identifiers();
        if (empty($identifiers)) {
            return false;
        }

        // Call the storify API
        $api_url = 'http://api.storify.com/v1/stories/'.$this->getUsername().'/'.$this->getSlug();

		// Get the json response
        $response = json_decode(static::get_url_content($api_url));
		if (empty($response) || empty($response->content)) {
			return false;
		}

        // Title is required
        if (empty($response->content->title)) {
            return false;
        }

        // Build attributes
        $attributes = array(
            'title'         => (string) $response->content->title,
            'description'   => (string) (!empty($response->content->description) ? $response->content->description : ''),
            'thumbnail'     => $response->content->thumbnail,
            'metadatas'     => (array) $response->content,
        );

        return $this->attributes($attributes);
    }

    /**
     * The clean url
     *
     * @return bool|string
     */
    public function cleanUrl() {
        return '//storify.com/'.$this->getUsername().'/'.$this->getSlug();
    }
}
