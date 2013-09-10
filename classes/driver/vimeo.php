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
        if (!$this->getUrl()) {
            return false;
        }

        // Check if the host match by extracting the identifier
        if (($identifier = $this->extractIdentifier())) {
            $this->identifier = $identifier;
            return true;
        }

        return false;
    }

    /**
     * Fetch the attributes of the online media (title, description...)
     *
     * @return bool|mixed
     */
    public function fetch() {
        $identifier = $this->extractIdentifier();
        if (empty($identifier)) {
            return false;
        }

        $attributes = array();

        // Extract attributes using the API
        $json = file_get_contents('http://vimeo.com/api/v2/video/'.$identifier.'.json');
        if (empty($json)) {
            return false;
        }

        // Extract json response
        $response = json_decode($json);
        if (is_array($response)) {
            $response = reset($response);
        }
        if (empty($response)) {
            return false;
        }

        // Extract title
        $attributes['title'] = (!empty($response->title) ? $response->title : '');
        if (empty($attributes['title'])) {
            return false;
        }

        // Extract description
        $attributes['description'] = (!empty($response->description) ? $response->description : '');

        // Extract thumbnail
        $attributes['thumbnail'] = (!empty($response->thumbnail_url) ? $response->thumbnail_url : '');

        // Save other attributes as metadatas
        $attributes['metadatas'] = (array) $response;

        return $attributes;
    }

    public function getCleanUrl() {
        if (($identifier = $this->extractIdentifier())) {
            return 'http://www.vimeo.com/video/'.$identifier;
        }
        return false;
    }

    /**
     * Extract the unique identifier of the online media
     *
     * @return bool|mixed
     */
    public function extractIdentifier() {
        // Already extracted
        if (!empty($this->identifier)) {
            return $this->identifier;
        }

        // Extract by host
        $parts = parse_url($this->getUrl());
        switch ($parts['host']) {

            // Standard url
            case 'www.vimeo.com':
            case 'vimeo.com':
                // Extract ID from path
                $args = array_values(array_filter(explode('/', $parts['path'])));

                // Extract the identifier
                $identifier = reset($args);
                if (!empty($identifier) && ctype_digit($identifier)) {
                    return $identifier;
                }
                break;

            // Embed url
            case 'player.vimeo.com':
                $args = array_values(array_filter(explode('/', $parts['path'])));

                // Check the "video" part
                if (reset($args) == 'video') {
                    array_shift($args);
                }

                $identifier = reset($args);
                if (!empty($identifier) && ctype_digit($identifier)) {
                    return $identifier;
                }
                break;

        }

        return false;
    }

    public function extractMetadatas() {

    }
}

//view-source:http://www.vimeo.com/services/oembed?format=json&url=http://www.vimeo.com/video/x14f6vz_shower-elevator-remi-gaillard-censuree-sur-youtube_fun
