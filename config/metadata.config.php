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
    'name'    => __('Médias d\'internet'),
    'version' => '4.0.0.0',
    'icons' => array(
        64 => 'static/apps/novius_onlinemediafiles/img/64-icon.png',
        32 => 'static/apps/novius_onlinemediafiles/img/32-icon.png',
        16 => 'static/apps/novius_onlinemediafiles/img/16-icon.png',
    ),
    'requires' => array(
        'lib_renderers',
    ),
    'permission' => array(),
    'provider' => array(
        'name' => 'Novius',
    ),
    'i18n_file' => 'novius_onlinemediafile::common',
    'namespace' => 'Novius\OnlineMediaFiles',
    'launchers' => array(
		'novius_onlinemediafiles' => array(
            'name'    => 'Online media',
            'action' => array(
                'action' => 'nosTabs',
                'tab' => array(
                    'url' => 'admin/novius_onlinemediafiles/appdesk',
                ),
            ),
        ),
    ),
    'enhancers' => array(
        'novius_onlinemediafiles_display' => array(
            'title'     => 'Online media',
            'desc'      => 'Display an embedded online media file',
            'id'        => 'onlinemediafiles',
            'previewUrl' => 'admin/novius_onlinemediafiles/enhancer/preview',
            'enhancer'  => 'novius_onlinemediafiles/front/show',
            'dialog' => array(
                'contentUrl' => 'admin/novius_onlinemediafiles/enhancer/popup',
                'ajax' => true,
//                'width' => 500,
//                'height' => 300,
            ),
//            'iconUrl'   => 'static/apps/noviusos_news/img/news-16.png',
        ),
    ),
);
