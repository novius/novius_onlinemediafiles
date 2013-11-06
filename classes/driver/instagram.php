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

class Driver_Instagram extends Driver_Oembed {

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
        // Set thumbnail
        $metadatas = $this->metadatas();
        if ($thumbnail = \Arr::get($metadatas, 'url')) {
            $this->attribute('thumbnail', $thumbnail);
        }
        return $this->attributes();
    }
}
