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

class Driver_Vine extends Driver {

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
            'template' => '{display}<script async src="//platform.vine.co/static/scripts/embed.js" charset="utf-8"></script>',
			'attributes'	=> array(
				'src'				=> 'https://vine.co/v/'.$this->identifier().'/embed/simple',
				'width'				=> 600,
				'height'			=> 600,
				'frameborder'		=> '0',
				'allowfullscreen'	=> true,
                'class'             => 'vine-embed',
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

        // Title is required
        $title = (string) $this->_getVineTitle();
        if (empty($title)) {
            return false;
        }

        // Build attributes
        $attributes = array(
            'title'         => $title,
            'description'   => '',
            'thumbnail'     => $this->_getVineThumbnail(),
            'metadatas'     => '',
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
                case 'vine.co':
                    //take the url part after the /v/
                    $exploded_path = explode('/', $parts['path']);
                    $v_key = array_search('v', $exploded_path);
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

    protected function _getVineTitle() {
        $vine = $this->_getVinePage();
        preg_match('/property="og:title" content="(.*?)"/', $vine, $matches);

        return ($matches[1]) ? $matches[1] : false;
    }

    protected function _getVineThumbnail() {
        $vine = $this->_getVinePage();
        preg_match('/property="og:image" content="(.*?)"/', $vine, $matches);

        return ($matches[1]) ? $matches[1] : false;
    }

    protected function _getVinePage() {
        static $fetch_vine = false;
        static $data = '';
        if (!$fetch_vine) {
            $url = "https://vine.co/v/{$this->identifier()}";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            $data = curl_exec($curl);
            curl_close($curl);
            $fetch_vine = true;
        }

        return (string) $data;
    }

    /**
     * Returns the clean URL of the online media
     *
     * @return bool|string
     */
    public function cleanUrl() {
        if ($this->identifier()) {
            return 'https://vine.co/v/'.$this->identifier();
        }
        return $this->url();
    }
}
