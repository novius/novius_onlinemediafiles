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

class Controller_Front extends \Nos\Controller_Front_Application {

    public function action_show($args = array()) {

        // Charge le média
        if (empty($args['media_id'])) {
            return false;
        }
        $media = Model_Media::find($args['media_id']);

		$params = array();
		if (!empty($args['media_width'])) {
			$params['attributes']['width'] = $args['media_width'];
		}
		if (!empty($args['media_height'])) {
			$params['attributes']['height'] = $args['media_height'];
		}
        // Affiche le media
        return $media->display($params);
    }
}
