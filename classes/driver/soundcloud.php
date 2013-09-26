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

class Driver_Soundcloud extends Driver_Oembed {

	public function compatible() {
		if (!$this->cleanUrl()) {
			return false;
		}
		if ($this->host() == 'soundcloud.com') {
			return parent::compatible();
		}
		return false;
	}
}
