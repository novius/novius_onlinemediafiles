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

class Driver_Instagram extends Driver_Oembed {

    /**
     * Checks whether the driver is compatible with the online media
     *
     * @return bool|mixed
     */
	public function compatible() {
        return ($this->host(false) == 'instagram.com' && parent::compatible());
	}

    /**
     * Fetch the online media attributes (title, description, metadatas...)
     *
     * @return bool|mixed
     */
    public function fetch() {
        if (!parent::fetch()) {
            return false;
        }
        // Set the thumbnail
        $metadatas = $this->metadatas();
        if ($thumbnail = \Arr::get($metadatas, 'url')) {
            $this->attribute('thumbnail', $thumbnail);
        }
        return $this->attributes();
    }
}
