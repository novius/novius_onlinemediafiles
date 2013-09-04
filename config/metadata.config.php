<?php
return array(
    'name'    => __('Online Meida Files'),
    'version' => 'dev',
    'icons' => array(
        64 => 'static/apps/novius_onlinemediafiles/img/64-icon.png',
        32 => 'static/apps/novius_onlinemediafiles/img/32-icon.png',
        16 => 'static/apps/novius_onlinemediafiles/img/16-icon.png',
    ),
    'permission' => array(
    ),
    'provider' => array(
        'name' => 'Novius',
    ),
    'namespace' => 'Novius\OnlineMediaFiles',
    'launchers' => array(
		'novius_onlinemediafiles' => array(
            'name'    => 'Vidéos d \'Internet',
            'action' => array(
                'action' => 'nosTabs',
                'tab' => array(
                    'url' => 'admin/novius_onlinemediafiles/appdesk',
                ),
            ),
        ),
    ),
//    'enhancers' => array(
//    ),
);
