<?php

//$icons = \Config::load('noviusos_media::icons', true);
$extensions = array();
//foreach ($icons['icons'] as $size => $images) {
//    foreach ($images as $image => $ext_list) {
//        foreach (explode(',', $ext_list) as $ext) {
//            $extensions[$size][$ext] = $image;
//        }
//    }
//}
$media_icon = function ($size) use ($extensions) {
    return function ($item) use($size, $extensions) {
        return '';
//        return isset($extensions[$size][$item->media_ext]) ? 'static/apps/noviusos_media/icons/'.$size.'/'.$extensions[$size][$item->media_ext] : '';
    };
};

return array(
    'i18n' => array(
        // Crud
        'notification item added' => __('Item added !'),
        'notification item deleted' => __('Media deleted !'),

        // General errors
        'notification item does not exist anymore' => __('This media file doesn’t exist any more. It has been deleted.'),
        'notification item not found' => __('We cannot find this media file.'),

        // Deletion popup
        'deleting item title' => __('Deleting the media ‘{{title}}’'),

        # Delete action's labels
        'deleting button 1 item' => __('Yes, delete this media file'),
    ),
    'data_mapping' => array(
        'title' => array(
            'column' => 'onme_title',
            'title' => __('Title'),
            '' => '',
            'cellFormatters' => array(
                'icon' => array(
                    'type' => 'icon',
                    'column' => 'icon',
                    'size' => 16,
                ),
            ),
        ),
        'thumbnailAlternate' => array(
            'value' => $media_icon(64),
        ),
        'icon' => array(
            'value' => $media_icon(16),
        ),
    ),
    'actions' => array(
    ),
);