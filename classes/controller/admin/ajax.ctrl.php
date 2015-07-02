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
            $onme_id = \Input::post('onme_id');
            $url = \Input::post('url', false);
            if (empty($url)) {
                throw new \Exception(__('Please specify the URL of the online media'));
            }

            // Prevents duplicate media
            $config = \Config::load("novius_onlinemediafiles::config", true);
            if (!\Arr::get($config, 'allow_duplicates', false)) {
                // Search another media with the same url
                $foundMedia = Model_Media::query()->where('onme_url', $url);
                if (!empty($onme_id)) {
                    $foundMedia->where('onme_id', '!=', $onme_id);
                }
                $foundMedia = $foundMedia->get_one();
                if (!empty($foundMedia)) {
                    \Response::json(200,
                        array(
                            'duplicate_of' => 'admin/novius_onlinemediafiles/media/insert_update/'.$foundMedia->onme_id
                        )
                    );
                }
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
                        'driver_name'   => $test_item->driver()->className(),
                        'driver_icon'   => $test_item->driver()->driverIcon(),
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
