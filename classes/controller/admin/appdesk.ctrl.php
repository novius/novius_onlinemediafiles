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

class Controller_Admin_Appdesk extends \Nos\Controller_Admin_Appdesk
{
    public function before()
    {
        try {
            parent::before();
        } catch (\Nos\Access_Exception $e) {
            if (\Input::is_ajax()) {
                \Response::json(array(
                    'error' => 'We’re afraid you’ve not be given access to the Media Centre. Don’t blame us though, we’re not the ones who decide the permissions.'
                ));
            } else {
                throw $e;
            }
        }
    }
}
