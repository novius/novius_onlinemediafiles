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

class Controller_Admin_Enhancer extends \Nos\Controller_Admin_Enhancer
{
    public function action_popup($edit = false)
    {
        return \View::forge('novius_onlinemediafiles::admin/enhancer/popup', array(
			'media_id'	=> \Input::get('media_id', ''),
			'url'		=> \Arr::get($this->config, 'controller_url').'/save',
		));
    }
}
