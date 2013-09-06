<?php

return array(
    'controller_url'  => 'admin/novius_onlinemediafiles/media',
    'model' => 'Novius\OnlineMediaFiles\Model_Media',
//    'environment_relation' => 'folder',
    'layout' => array(
        'large' => true,
        'save' => 'save',
        'title' => 'onme_title',
        'content' => array(
            'properties' => array(
                'view' => 'nos::form/expander',
                'params' => array(
                    'title'   => __('Propriétés'),
                    'nomargin' => true,
                    'options' => array(
                        'allowExpand' => true,
                        'fieldset' => 'properties',
                    ),
                    'content' => array(
                        'view' => 'nos::form/fields',
                        'params' => array(
                            'fields' => array(
                                'onme_folder_id',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'fields' => array(
        'onme_id' => array (
            'label' => 'ID: ',
            'form' => array(
                'type' => 'hidden',
            ),
            'dont_save' => true,
        ),
        'onme_folder_id' => array(
            'label' => __('Folder'),
            'renderer' => '\Lib\Renderers\Renderer_Categories',
            'renderer_options' => array(
                'width' => '250px',
                'height' => '250px',
                'namespace' => '\Novius\OnlineMediaFiles',
                'folder' => 'novius_onlinemediafiles',
                'inspector_tree' => 'inspector/folder',
                'columns' => array(
                    array(
                        'dataKey' => 'title',
                    )
                ),
                'class' => 'Model_Folder',
//                'multiple' => '1',
            ),
            'label' => __('Folder'),
            'form' => array(
            ),
            'input_name'    => 'onme_folder_id',
//            'before_save' => function($item, $data) use ($namespace) {
//                $item->categories;//fetch et 'cree' la relation
//                unset($item->categories);
//                $category_class = $namespace.'Model_Category';
//                if (!empty($data['categories'])) {
//                    foreach ($data['categories'] as $cat_id) {
//                        if (is_numeric($cat_id) ) {
//                            $item->categories[$cat_id] = $category_class::find($cat_id);
//                        }
//                    }
//                }
//            },
//            'form' => array(
//                'options' => $classes,
//            ),
//            'populate' => function($item) {
//                if (!empty($item->classes)) {
//                    return array_keys($item->classes);
//                } else {
//                    return array();
//                }
//            },
//            'before_save' => function($item, $data) {
//                $item->classes;//fetch et 'cree' la relation
//                unset($item->classes);
//                if (!empty($data['classes'])) {
//                    foreach ($data['classes'] as $class_id) {
//                        if (ctype_digit($class_id) ) {
//                            $item->classes[$class_id] = \ReservationAtelier\Model_Classe::find($class_id);
//                        }
//                    }
//                }
//            },
        ),
//        'media_folder_id' => array(
//            'renderer' =>  'Nos\Media\Renderer_Folder',
//            'form' => array(
//                'type'  => 'hidden',
//            ),
//            'label' => __('Select a folder where to put your media file:'),
//        ),
//        'media' => array(
//            'dont_save' => true,
//            'form' => array(
//                'type' => 'file',
//            ),
//            'label' => __('File from your hard drive:'),
//        ),
        'onme_title' => array(
            'form' => array(
                'type' => 'text',
            ),
            'label' => __('Title:'),
        ),
//        'media_file' => array(
//            'form' => array(
//                'type' => 'text',
//            ),
//            'label' => __('SEO, Media URL:'),
//        ),
        'save' => array(
            'label' => '',
            'form' => array(
                'type' => 'submit',
                'tag' => 'button',
                // Note to translator: This is a submit button
                'value' => __('Save'),
                'class' => 'primary',
                'data-icon' => 'check',
            ),
        ),
    ),
);
