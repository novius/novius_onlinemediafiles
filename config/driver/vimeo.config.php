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
	'name'		=> __('Viméo'),
	'icon'		=> array(
		'16' => 'vimeo.png',
	),
    // Fill these fields to use the private API (eg. for fetching private videos)
    'private_api' => array(
        'client_id'     => false,
        'client_secret' => false,
        'access_token'  => false,
    ),
);
