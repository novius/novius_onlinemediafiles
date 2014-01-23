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

class Controller_Admin_Renderer extends \Nos\Controller_Admin_Enhancer
{
    public function action_popup($edit = false)
    {
        return \View::forge('novius_onlinemediafiles::admin/renderer/popup', array(
            'edit'  => $edit,
            'url'   => \Arr::get($this->config, 'controller_url').'/save',
        ), false);
    }

    public function action_save(array $args = null)
    {
        if (empty($args)) {
            $args = \Input::post();
        }

        // Builds the preview
        $preview = \View::forge(\Arr::get($this->config, 'preview.view'), array(
            'layout' => \Arr::get($this->config, 'preview.layout'),
            'params' => \Arr::get($this->config, 'preview.params'),
            'enhancer_args' => $args,
        ))->render();

        \Response::json(array(
            'debug'  => \Arr::get($this->config, 'preview'),
            'config'  => $args,
            'preview' => $preview,
        ));
    }
}
