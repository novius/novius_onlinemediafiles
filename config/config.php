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
    // Available drivers
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

    // The path of the front stylesheet (set an empty value to disable)
    'front_css_path'  => 'static/apps/novius_onlinemediafiles/css/front.css',

    // Alignment feature
    'alignment' => array(
        // Enable or disable the alignment features
        'enabled'   => true,
    ),

    // Responsive feature
    'responsive' => array(
        // Enable or disable the responsive features
        'enabled'   => false,
    ),
    'allow_duplicates' => false
);
