<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

return array(
    // Configuration de la popup
    'popup' => array(
        'layout' => array(
            'view' => 'novius_onlinemediafiles::admin/enhancer/popup',
        ),
    ),
    'preview' => array(
        'view'  => 'novius_onlinemediafiles::admin/enhancer/preview',
    ),
    // Configuration de la prévisualisation
//    'preview' => array(
//        // (facultatif) vue à utiliser pour le rendu (valeur par défaut en exemple)
//        'view' => 'lib_blocs::admin/enhancer/preview',
//        // (facultatif) fichiers de vues additionnels (inclus par la view au-dessus)
//        //'layout' => array(),
//        'params' => array(
//            // (optionnel) reprend le titre de l'enhancer par défaut
//            'title' => "Mon super enhancer",
//            // 'icon' (optionnel) reprend celui de l'application en taille 64*64 par défaut
//        ),
//    ),
);