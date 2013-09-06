<?php

return array(
    'controller_url'  => 'admin/novius_onlinemediafiles/folder',
    'model' => 'Novius\OnlineMediaFiles\Model_Folder',
    'environment_relation' => 'parent',
//    'tab' => array(
//        'iconUrl' => 'static/apps/noviusos_media/img/media-16.png',
//        'labels' => array(
//            'insert' => __('Add a folder'),
//        ),
//    ),
//    'layout' => array(
//        array(
//            'view' => 'noviusos_media::admin/folder',
//        ),
//    ),
//    'views' => array(
//        'delete' => 'noviusos_media::admin/folder_delete',
//    ),
    'layout' => array(
        'large' => true,
        'save' => 'save',
        'title' => 'onfo_title',
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
                                'onfo_parent_id',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'fields' => array(
        'onfo_id' => array(
            'form' => array(
                'type' => 'hidden',
            ),
        ),
//        'onfo_parent_id' => array(
//            'renderer' => 'Nos\Media\Renderer_Folder',
//            'form' => array(
//                'type'  => 'hidden',
//            ),
//            'label' => __('Select a folder where to put your sub-folder:'),
//        ),
        'onfo_parent_id' => array(
            'label' => __('Parent'),
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
//                'multiple' => false,
            ),
            'label' => __('Parent'),
            'form' => array(
            ),
            'input_name'    => 'onfo_parent_id',
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
        'onfo_title' => array(
            'label' => 'Title',
            'form' => array(
                'type' => 'text',
                // Note to translator: This is a placeholder, i.e. a field’s label shown within the field
                'placeholder' => __('Title'),
            ),
            'validation' => array(
                'required',
                'min_length' => array(2),
            ),
        ),
//        'onfo_dir_name' => array(
//            'form' => array(
//                'type' => 'text',
//                'size' => 30,
//            ),
//            'label' => __('SEO, folder URL:'),
//            'validation' => array(
//                'required',
//                'min_length' => array(2),
//            ),
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
