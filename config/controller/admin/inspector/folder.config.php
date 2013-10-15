<?php
\Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common'));

return array(
    'model' => 'Novius\OnlineMediaFiles\Model_Folder',
    'order_by' => 'onfo_title',
    'input' => array(
        'key' => 'onme_folder_id'
    ),
    'appdesk' => array(
        'label'     => __('Folder'),
        'treeGrid' => array(
            'movable'   => false,
            'sortable'  => false,
        ),
    ),
);
