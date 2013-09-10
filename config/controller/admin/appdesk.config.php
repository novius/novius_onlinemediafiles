<?php

return array(
    'model' => 'Novius\OnlineMediaFiles\Model_Media',
//    'query' => array(
//        'limit' => 10,
//    ),
    'search_text' => 'onme_title',
    'inspectors' => array(
        'folder',
        'driver',
    ),
//    'views' => array(
//        'default' => array(
//            'name' => __('Default view'),
//            'json' => array(
//                'static/apps/noviusos_media/config/common.js',
//            ),
//        ),
//        'flick_through' => array(
//            'name' => __('Flick through view'),
//            'json' => array(
//                'static/apps/noviusos_media/config/common.js',
//                'static/apps/noviusos_media/config/flick_through.js'
//            ),
//        ),
//        'image_pick' => array(
//            'virtual' => true,
//            'json' => array(
//                'static/apps/noviusos_media/config/common.js',
//                'static/apps/noviusos_media/config/image_pick.js'
//            ),
//        ),
//        'onme_pick' => array(
//            'virtual' => true,
//            'json' => array(
//                'static/apps/noviusos_media/config/common.js',
//                'static/apps/noviusos_media/config/onme_pick.js'
//            ),
//        )
//    ),
    'i18n' => array(
        'item' => __('media file'),
        'items' => __('media files'),
        'showNbItems' => __('Showing {{x}} media files out of {{y}}'),
        'showOneItem' => __('Showing 1 media file'),
        'showNoItem' => __('No media files'),
        // Note to translator: This is the action that clears the 'Search' field
        'showAll' => __('Show all media files'),

        'Pick' => __('Pick'),
    ),
    'thumbnails' => true,
    'appdesk' => array(
//        'reloadEvent' => array(
//            'Novius\OnlineMediaFiles\Model_Media',
//            array(
//                'name' => 'Novius\OnlineMediaFiles\Model_Folder',
//                'action' => 'delete',
//            ),
//        ),
        'appdesk' => array(
            'defaultView' => 'thumbnails',
        ),
    ),

);
