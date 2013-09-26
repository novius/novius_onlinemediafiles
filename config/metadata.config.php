<?php
return array(
    'name'    => __('Médias d\'internet'),
    'version' => 'beta',
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
            'name'    => 'Médias d\'Internet',
            'action' => array(
                'action' => 'nosTabs',
                'tab' => array(
                    'url' => 'admin/novius_onlinemediafiles/appdesk',
                ),
            ),
        ),
    ),
    'enhancers' => array(
        'novius_onlinemediafiles_display' => array(
            'title'     => 'Média distant',
            'desc'      => '',
            'id'        => 'onlinemediafiles',
            'previewUrl' => 'admin/novius_onlinemediafiles/enhancer/preview',
            'enhancer'  => 'novius_onlinemediafiles/front/show',
            'dialog' => array(
                'contentUrl' => 'admin/novius_onlinemediafiles/enhancer/popup',
                'ajax' => true,
//                'width' => 500,
//                'height' => 300,
            ),
//            'iconUrl'   => 'static/apps/noviusos_news/img/news-16.png',
        ),
    ),
);
