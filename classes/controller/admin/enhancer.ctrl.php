<?php

namespace Novius\OnlineMediaFiles;

class Controller_Admin_Enhancer extends \Nos\Controller_Admin_Enhancer
{
    public function action_popup($edit = false)
    {
        $view = \View::forge('novius_onlinemediafiles::admin/enhancer/popup');
        $view->set('edit', $edit, false);
        $view->set('url', $this->config['controller_url'].'/save', false);
        return $view;
    }
}