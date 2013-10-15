<?php
\Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common'));

return array(
    'data_mapping' => array(
        'title' => array(
            'column' => 'onfo_title',
            'title' => __('Folder'),
        ),
    ),
    'i18n' => array(
        // Crud
        'notification item added' => __('Right, your new folder is ready.'),
        'notification item deleted' => __('The folder has been deleted.'),

        // General errors
        'notification item does not exist anymore' => __('This folder doesn’t exist any more. It has been deleted.'),
        'notification item not found' => __('We cannot find this folder.'),

        // Deletion popup
        'deleting item title' => __('Deleting the folder ‘{{title}}’'),

        # Delete action's labels
        'deleting button 1 item' => __('Yes, delete this folder'),
    ),
//    'actions' => array(
//        'Nos\Media\Model_Folder.add' => array(
//            'label' => __('Add a folder'),
//        ),
//        'Nos\Media\Model_Folder.edit' => array(
//            'disabled' => array(function($item) {
//                return empty($item->medif_parent_id) ? __('You can’t edit the root folder.') : false;
//            }),
//        ),
//        'Nos\Media\Model_Folder.delete' => array(
//            'disabled' => array(function($item) {
//                return empty($item->medif_parent_id) ? __('You can’t delete the root folder.') : false;
//            }),
//        ),
//        'Nos\Media\Model_Folder.add_media' => array(
//            'label' => __('Add a media file in this folder'),
//            'icon' => 'plus',
//            'action' => array(
//                'action' => 'nosTabs',
//                'tab' => array(
//                    'url' => 'admin/noviusos_media/media/insert_update?environment_id={{id}}',
//                ),
//            ),
//            'targets' => array(
//                'grid' => true,
//            ),
//        ),
//        'Nos\Media\Model_Folder.add_subfolder' => array(
//            'label' => __('Add a sub-folder to this folder'),
//            'icon' => 'folder-open',
//            'action' => array(
//                'action' => 'nosTabs',
//                'tab' => array(
//                    'url' => '{{controller_base_url}}insert_update?environment_id={{id}}',
//                ),
//                'dialog' => array(
//                    'width' => 800,
//                    'height' => 400
//                ),
//            ),
//            'targets' => array(
//                'grid' => true,
//            ),
//        ),
//    ),
);
