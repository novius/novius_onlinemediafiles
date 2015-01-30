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
    /*
     * Available drivers
     */
    'drivers' => array(
        'Youtube',
        'Dailymotion',
        'Vimeo',
        'Soundcloud',
        'Spotify',
        'Flickr',
        'Instagram',
        'Vine',
        'Slideshare',
        'Storify',
        'Oembed',
    ),
    'responsive' => array(
        // Enable or disable the responsive features
        'enabled'   => false,
        // The path of the responsive stylesheet (set an empty value to disable)
        'css_path'  => 'static/apps/novius_onlinemediafiles/css/responsive.css',
        // The css class wrapping the media (set an empty value to disable)
        'css_class' => 'onlinemediafiles-fluid-wrapper',
    ),
);
