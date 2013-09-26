<?php

namespace Novius\OnlineMediaFiles;

class Controller_Admin_Enhancer extends \Nos\Controller_Admin_Enhancer
{
    public function action_popup($edit = false)
    {
        return \View::forge('novius_onlinemediafiles::admin/enhancer/popup', array(
			'media_id'	=> \Fuel\Core\Input::get('media_id', ''),
			'url'		=> $this->config['controller_url'].'/save',
		));
    }
}