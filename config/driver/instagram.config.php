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
	'name'		=> __('Instagram'),
	'icon'		=> array(
		'16' => 'instagram.png',
	),
    'api'       => array(
        'host'      => 'api.instagram.com',
        'path'      => '/oembed',
    ),
);
