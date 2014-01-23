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

class Controller_Admin_Media extends \Nos\Controller_Admin_Crud
{
    public function before_save($item, $data) {
        // Resync the online media file if needed
        if (\Input::post('resync')) {
            if (!$item->sync(false)) {
                $this->send_error(new \Exception(__('La synchronisation du média a échoué !')));
            }
        }
    }
}
