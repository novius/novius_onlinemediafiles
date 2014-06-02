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

class Driver_Instagram extends Driver {

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
                case 'instagram.com':
                    //take the url part after the /v/
                    $exploded_path = explode('/', $parts['path']);
                    $v_key = array_search('p', $exploded_path);
                    $id = $exploded_path[$v_key+1];
                    $id = explode('?', $id);
                    $id = reset($id);
                    if (!empty($id)) {
                        $this->identifier = $id;
                    }
                    break;
            }
        }
        return $this->identifier;
    }

    /**
     * Fetch the online media attributes (title, description, metadatas...)
     *
     * @return bool|mixed
     */
    public function fetch() {

        if (!$this->identifier() || !$this->url()) {
            return false;
        }

        // Call the youtube API
        $api_url = 'http://api.instagram.com/oembed?url='.$this->url();

        // Get the json response
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($data);


        if (empty($response)) {
            return false;
        }

        // Title is required
        $title = (string) !empty($response->title) ? $response->title : __('Untitled by ').$response->author_name;
        if (empty($title)) {
            return false;
        }

        $thumbnail = '';
        $type = $this->getPostType();
        if ($type == 'photo') {
            $thumbnail = $response->url;
        } else {
            $thumbnail = $this->_getInstagramThumbnail();
        }

        $metadatas = array(
            'type' => $type,
            'instagram_type' => $response->type,
            'width' => $response->width,
            'height' => $response->height,
        );


        // Build attributes
        $attributes = array(
            'title'         => $title,
            'description'   => $this->_getInstagramDescription(),
            'thumbnail'     => $thumbnail,
            'metadatas'     => $metadatas,
        );

        return $this->attributes($attributes);
    }

    /**
     * Returns the HTML code to embed the online media
     *
     * @param array $params
     * @return mixed|string
     */
    public function display($params = array()) {
        $type = \Arr::get($this->metadatas(), 'type');
        if ($type == 'photo') {
            return \Html::img($this->thumbnail(), array('alt' => $this->attribute('title')));
        } else {
            return parent::display(\Arr::merge(array(
                'attributes'    => array(
                    'src'               => '//instagram.com/p/'.$this->identifier().'/embed/',
                    'width'             => \Arr::get($this->metadatas(), 'width', 612),
                    'height'            => \Arr::get($this->metadatas(), 'height', 710),
                    'frameborder'       => '0',
                    'scrolling'         => 'no',
                    'allowtransparency' => 'true',
                    'allowfullscreen'   => true,
                )
            ), $params));
        }

    }

    /**
     * Returns the clean URL of the online media
     *
     * @return bool|string
     */
    public function cleanUrl() {
        if ($this->identifier()) {
            return 'http://instagram.com/p/'.$this->identifier();
        }
        return $this->url();
    }

    public function getPostType() {
        $html = $this->_getInstagramPage();
        preg_match('/property="og:type" content="(.*?)"/', $html, $matches);

        return ($matches[1]) ? $matches[1] : false;
    }

    protected function _getInstagramDescription() {
        $html = $this->_getInstagramPage();
        preg_match('/property="og:description" content="(.*?)"/', $html, $matches);

        return ($matches[1]) ? $matches[1] : false;
    }

    protected function _getInstagramThumbnail() {
        $html = $this->_getInstagramPage();
        preg_match('/property="og:image" content="(.*?)"/', $html, $matches);

        return ($matches[1]) ? $matches[1] : false;
    }

    protected function _getInstagramPage() {
        static $fetch_instagram = false;
        static $data = '';
        if (!$fetch_instagram) {
            $url = "http://instagram.com/p/{$this->identifier()}";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            $data = curl_exec($curl);
            curl_close($curl);
            $fetch_instagram = true;
        }

        return (string) $data;
    }

}
