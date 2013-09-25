<?php

namespace Novius\OnlineMediaFiles;

class Controller_Admin_Renderer extends \Nos\Controller_Admin_Enhancer
{
    public function action_popup($edit = false)
    {
        $view = \View::forge('novius_onlinemediafiles::admin/renderer/popup');
        $view->set('edit', $edit, false);
        $view->set('url', $this->config['controller_url'].'/save', false);
        return $view;
    }

    public function action_save(array $args = null)
    {
        if (empty($args)) {
            $args = $_POST;
        }

        $body = array(
            'debug'  => $this->config['preview'],
            'config'  => $args,
            'preview' => \View::forge($this->config['preview']['view'], array(
                'layout' => $this->config['preview']['layout'],
                'params' => $this->config['preview']['params'],
                'enhancer_args' => $args,
            ))->render(),
        );
        \Response::json($body);
    }
}
