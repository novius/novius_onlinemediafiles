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

class Driver_Soundcloud extends Driver_Oembed {

    /**
     * Checks whether the driver is compatible with the online media
     *
     * @return bool|mixed
     */
	public function compatible() {
        return ($this->host(false) == 'soundcloud.com' && parent::compatible());
	}
}
