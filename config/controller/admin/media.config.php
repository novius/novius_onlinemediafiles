<?php

return array(
    'controller_url'  => 'admin/novius_onlinemediafiles/media',
    'model' => 'Novius\OnlineMediaFiles\Model_Media',
//    'environment_relation' => 'folder',
    'layout' => array(
        'large' => true,
        'save' => 'save',
        'title' => 'onme_url',
        'content' => array(
            'params_js' => array(
                'view' => 'novius_onlinemediafiles::admin/retrieve_video',
            ),
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
                                'onme_title',
                                'onme_description',
                                'onme_driver_name',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'menu' => array(
            'thumbnail' => array(
                'view' => 'nos::form/expander',
                'params' => array(
                    'title'   => __('Prévisualisation'),
                    'nomargin' => true,
                    'options' => array(
                        'allowExpand' => true,
                        'fieldset' => 'properties',
                    ),
                    'content' => array(
                        'view' => 'nos::form/fields',
                        'params' => array(
                            'fields' => array(
                                'thumbnail',
                            ),
                        ),
                    ),
                ),
            ),
            'accordion' => array(
                'view' => 'nos::form/accordion',
                'params' => array(
                    'accordions' => array(
                        'folder' => array(
                            'title' => __('Folder'),
                            'fields' => array(
                                'onme_folder_id'
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
            'validation' => array('required'),
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
            'label' => __('Title'),
        ),
        'onme_url' => array(
            'form' => array(
                'type' => 'text',
            ),
            'label' => __('URL'),
        ),
        'onme_description' => array(
            'form' => array(
                'type' => 'textarea',
                'rows' => '20',
            ),
            'label' => 'Description',
        ),
        'onme_metadatas' => array (
            'label' => 'metadata',
            'form' => array(
                'type' => 'hidden',
            ),
            'populate' => function($item) {
                return json_encode($item->onme_metadatas);
            },
            'before_save'   => function($item, $data) {
                $item->onme_metadatas = json_decode($data['onme_metadatas']);
            }
        ),
        'onme_driver_name' => array (
            'label' => 'driver_name',
            'form' => array(
                'type' => 'hidden',
            ),
        ),
        'onme_thumbnail' => array (
            'label' => 'ID: ',
            'form' => array(
                'type' => 'hidden',
            ),
        ),
        'thumbnail' => array(
            'template' => '<div class="wrap_preview">{field}</div>',
            'renderer' => 'Novius\OnlineMediaFiles\Renderer_HTML',
            'dont_save' => true,
            'populate' => function($item) {
                if ($item->onme_id) {
                    return $item->display();
                }
                return '';
            },
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
