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

    public function preview($metadatas = false) {
        // Charge les attributs du média distant
        $attributes = $this->getAttributes();
        if (!empty($attributes)) {
            ?>
            <img src="<?= $this->attributes['thumbnail'] ?>" alt="<?= $this->attributes['title'] ?>" />
            <?
        }
    }

    public function display() {
        // Charge les attributs du média distant
        $attributes = $this->getAttributes();
        $identifier = $this->extractIdentifier();
        if (!empty($attributes) && !empty($identifier)) {
            // Build embed url
            ?>
            <iframe width="560" height="315" src="//www.youtube.com/embed/<?= $identifier ?>" frameborder="0" allowfullscreen></iframe>
            <?
        }
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

        $api_url = 'http://gdata.youtube.com/feeds/api/videos/'.$this->identifier.'?v=2&alt=jsonc';

        // Load response (atom)
        $json = file_get_contents($api_url);
        if (empty($json)) {
            return false;
        }
        $response = json_decode($json);

        // Extract title (required)
        $attributes['title'] = (!empty($response->data->title) ? $response->data->title : '');
        if (empty($attributes['title'])) {
            return false;
        }

        // Extract description
        $attributes['description'] = (!empty($response->data->description) ? $response->data->description : '');

        // Extract thumbnail
        if (!empty($response->data->thumbnail->hqDefault)) {
            $attributes['thumbnail'] = $response->data->thumbnail->hqDefault;
        } elseif (!empty($response->data->thumbnail->sqDefault)) {
            $attributes['thumbnail'] = $response->data->thumbnail->sqDefault;
        } else {
            $attributes['thumbnail'] = false;
        }

        // Save other attributes as metadatas
        $attributes['metadatas'] = (array) $response->data;

        return $attributes;
    }

    public function getCleanUrl() {
        $identifier = $this->extractIdentifier();
        if (!empty($identifier)) {
            return false;
        }
        return 'http://www.youtube.com/watch?v='.$identifier;
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
        $parts = self::parseUrl($this->getUrl());
        switch ($parts['host']) {

            // Standard url
            case 'www.youtube.com':
            case 'youtube.com':
            case 'm.youtube.com':
                // Extract ID from query args
                parse_str($parts['query'], $args);
                if ($args['v']) {
                    return $args['v'];
                }
                break;

            // Minified url
            case 'youtu.be':
                // Extract ID from path
                $args = array_filter(explode('/', $parts['path']));
                if (count($args) > 0) {
                    return reset($args);
                }
                break;
        }

        return false;
    }

    public function extractMetadatas() {

    }

    public static function SimpleXMLToArray(\SimpleXMLElement $xml) {
        $array = (array) $xml;
        foreach (array_slice($array, 0) as $key => $value) {
            if ($value instanceof SimpleXMLElement) {
                $array[$key] = empty($value) ? null : self::SimpleXMLToArray($value);
            }
        }
        return $array;
    }
}