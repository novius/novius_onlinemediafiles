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

class Driver_Youtube extends Driver_Oembed {

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
     * Return the driver's icon
     *
     * @param int $size
     * @return mixed
     */
    public function driverIcon($size = 16) {
        $icon = \Arr::get($this->config, 'icon.'.$size);

        return static::driverIconPath($size, $icon);
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
				'src'				=> '//www.youtube.com/embed/'.$this->identifier().'?&wmode=opaque',
				'width'				=> 560,
				'height'			=> 315,
				'frameborder'		=> '0',
				'allowfullscreen'	=> true,
			)
		), $params));
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
                case 'www.youtube.com':
                case 'youtube.com':
                case 'm.youtube.com':
                    // Extract ID from query args
                    parse_str($parts['query'], $args);
                    if ($args['v']) {
                        $this->identifier = $args['v'];
                    }
                    break;

                // Minified pattern
                case 'youtu.be':
                    // Extract ID from path
                    $args = array_filter(explode('/', $parts['path']));
                    if (count($args) > 0) {
                        $this->identifier = reset($args);
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
            return 'http://www.youtube.com/watch?v='.$this->identifier();
        }
        return $this->url();
    }
}
