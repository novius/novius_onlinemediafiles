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

class Controller_Admin_Ajax extends \Nos\Controller_Admin_Application
{
    public function action_fetch() {
        try {
            $url = \Input::post('url', false);
            if (empty($url)) {
                throw new \Exception(__('Please specify the URL of the online media'));
            }

            // Builds a test item
            $test_item = Model_Media::forge(array(
                'onme_url'  => $url,
            ));

            // Finds a compatible driver and synchronizes attributes
            if (!$test_item->sync(false) || !$test_item->driver()) {
                throw new \Exception(__('This online media is not recognized'));
            }

            // Returns the fetched attributes
            \Response::json(
                \Arr::merge(
                    $test_item->driver()->attributes(),
                    array(
                        'driver_name'   => $test_item->driver()->driverName(),
                        'display'       => $test_item->driver()->display(),
                        'preview'       => $test_item->driver()->preview(),
                    )
                )
            );
        }

        // Gestion des erreurs
        catch (\Exception $e) {
            \Response::json(array('error' => $e->getMessage()));
        }
    }
}
