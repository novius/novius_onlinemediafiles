<?php

namespace Novius\OnlineMediaFiles;

class Controller_Admin_Media extends \Nos\Controller_Admin_Crud
{
    public function before_save($item, $data) {
        // Resync the online media file
        if (!empty($_POST['resync'])) {
            if (!$item->sync(false)) {
                $this->send_error(new \Exception(__('La synchronisation du média a échoué !')));
            }
        }
    }
}
