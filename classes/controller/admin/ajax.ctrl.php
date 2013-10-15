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

use Fuel\Core\Config;

class Controller_Admin_Ajax extends \Nos\Controller_Admin_Application
{
    public function action_fetch() {

        try {
            $url = \Input::post('url', false);

            // Pas d'url spécifiée
            if (empty($url)) {
                throw new \Exception('Veuillez préciser l\'url du média internet');
            }

            // Parcours les drivers disponibles
            foreach ($this->app_config['drivers'] as $driver_name) {

                // Build le driver avec l'url fournie
                if (($driver = Driver::build($driver_name, $url))) {

                    // Check si le driver est compatible avec l'url du média internet
                    if ($driver->compatible()) {

                        // Extrait les attributs du média internet (titre, description...)
                        $attributes = $driver->fetch();

                        // Video introuvable
                        if (empty($attributes)) {
                            throw new \Exception('Ce média internet est introuvable');
                        }

                        $attributes['driver_name'] = $driver_name;
                        $attributes['display'] = $driver->display();
                        $attributes['preview'] = $driver->preview();
                        $attributes['metadatas'] = Driver::objectToArray($attributes['metadatas']);

                        // Retourne les attributs au format json
                        \Response::json($attributes);

                        return true;
                    }
                }
            }

            // Aucun driver n'est compatible avec l'url fournie
            throw new \Exception('Ce média internet n\'est pas reconnu');
        }

        // Gestion des erreurs
        catch (\Exception $e) {
            \Response::json(array('error' => $e->getMessage()));
        }
    }
}
