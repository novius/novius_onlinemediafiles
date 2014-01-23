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
	'name'		=> __('Oembed'),
	'icon'		=> array(
		'16' => 'oembed.png',
	),
    // Oembed api configuration
    'api'       => array(
        'path'  => '/services/oembed',
        'parameters'    => array(
            'format'        => 'json',
        ),
    ),

    // Custom API paths depending on host
    'path_mapping'  => array(
        'soundcloud.com'   => '/oembed'
    ),

);
