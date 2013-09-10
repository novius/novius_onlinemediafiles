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

    public function check() {
        if (!$this->getUrl()) {
            return false;
        }

        //$this->url = 'http://youtu.be/H5QdtDPEhhk';

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
        $fields = (!empty($this->config['api_fields']) ? '?fields='.implode(',', $this->config['api_fields']) : '');

        // Check if video exists
        $api_url = 'https://api.dailymotion.com/video/'.$identifier.$fields;
        $headers = get_headers($api_url, 1);
        if (strpos($headers[0], '200 OK') === false) {
            return false;
        }

        // Get video datas
        $json = file_get_contents($api_url);
        if (empty($json)) {
            return false;
        }
        $json = json_decode($json);

        // Extract title
        $attributes['title'] = (!empty($json->title) ? $json->title : '');
        if (empty($attributes['title'])) {
            return false;
        }

        // Extract description
        $attributes['description'] = (!empty($json->description) ? $json->description : '');

        // Extract thumbnail
        $attributes['thumbnail'] = (!empty($json->thumbnail_url) ? $json->thumbnail_url : '');

        // Save other attributes as metadatas
        $attributes['metadatas'] = (array) $json;

        return $attributes;
    }

    public function getCleanUrl() {
        if (($identifier = $this->extractIdentifier())) {
            return 'http://www.dailymotion.com/video/'.$identifier;
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
                        return reset($identifier);
                    }
                }
                break;
        }

        return false;
    }

    public function extractMetadatas() {

    }
}

//view-source:http://www.dailymotion.com/services/oembed?format=json&url=http://www.dailymotion.com/video/x14f6vz_shower-elevator-remi-gaillard-censuree-sur-youtube_fun
