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

class Driver_Vimeo extends Driver {

    public function check() {
        // Check if the driver is compatible by extracting the identifier from the url
        return ($this->url() && $this->identifier(false));
    }

    public function preview($echo = true) {
        // Charge les attributs du média distant
        $attributes = $this->attributes();
        if (empty($attributes) || empty($attributes['thumbnail'])) {
            return '';
        }
        if (!$echo) ob_start();
        ?>
        <img src="<?= $attributes['thumbnail'] ?>" alt="<?= $attributes['title'] ?>" />
        <?
        return (!$echo ? ob_get_clean() : true);
    }

    public function display($echo = true) {
        if (!$this->identifier()) {
            return '';
        }
        if (!$echo) ob_start();
        ?>
        <iframe src="//player.vimeo.com/video/<?= $this->identifier() ?>" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
        <?
        return (!$echo ? ob_get_clean() : true);
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

        // Build the vimeo API url
        $api_url = 'http://vimeo.com/api/v2/video/'.$this->identifier().'.json';
        // Check if the video exists by checking the HTTP status code of the API url
        $headers = get_headers($api_url, 1);
        if (strpos($headers[0], '200 OK') === false) {
            return false;
        }
        // Get the API response
        $json = file_get_contents($api_url);
        if (empty($json)) {
            return false;
        }
        $response = json_decode($json);
        if (is_array($response)) {
            $response = reset($response);
        }
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

    public function cleanUrl() {
        if ($this->identifier()) {
            return 'http://www.vimeo.com/video/'.$this->identifier();
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
            $parts = self::parseUrl($this->url());
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
}
