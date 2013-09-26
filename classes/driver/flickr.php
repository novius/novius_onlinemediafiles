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

class Driver_Flickr extends Driver_Oembed {

	public function compatible() {
		if (!$this->cleanUrl()) {
			return false;
		}
		if ($this->host() == 'flickr.com') {
			return parent::compatible();
		}
		return false;
	}

	/**
	 * Fetch the online media attributes (title, description, metadatas...)
	 *
	 * @return bool|mixed
	 */
	public function fetch() {
		if (!$this->cleanUrl()) {
			return false;
		}

		parent::fetch();

		$this->attribute('thumbnail', preg_replace('`_s\.([a-z]+)$`i', '_b.$1', $this->attribute('thumbnail')));
		return $this->attributes();
	}
}
