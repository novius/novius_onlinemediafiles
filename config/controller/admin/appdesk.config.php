<?php
\Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common'));

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
        'item' => __('internet media'),
        'items' => __('internet medias'),
        'showNbItems' => __('Showing {{x}} internet medias out of {{y}}'),
        'showOneItem' => __('Showing 1 internet media'),
        'showNoItem' => __('No internet medias'),
        // Note to translator: This is the action that clears the 'Search' field
        'showAll' => __('Show all internet medias'),

        'Pick' => __('Pick'),
    ),
    'views' => array(
        'default' => array(
            'name' => __('Default view'),
            'json' => array(
                'static/apps/novius_onlinemediafiles/config/common.js',
            ),
        ),
        'media_pick' => array(
            'virtual' => true,
            'json' => array(
                'static/apps/novius_onlinemediafiles/config/common.js',
                'static/apps/novius_onlinemediafiles/config/media_pick.js'
            ),
        ),
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
