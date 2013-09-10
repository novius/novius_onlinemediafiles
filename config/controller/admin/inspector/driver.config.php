<?php

return array(
    'data' => array(
        array(
            'id' => 'Youtube',
            'title' => __('vidéo Youtube'),
            'icon' => 'archive.png',
        ),
        array(
            'id' => 'Dailymotion',
            'title' => __('vidéo Dailymotion'),
            'icon' => 'archive.png',
        ),
        array(
            'id' => 'Vimeo',
            'title' => __('vidéos Vimeo'),
            'icon' => 'archive.png',
        ),
    ),
    'input' => array(
        'key' => 'onme_driver_name',
        'query' =>
        function ($value, $query)
        {
            $drivers = array(
                'Youtube',
                'Dailymotion',
                'Vimeo',
            );
            $ext = array();
            $other = array();
            $value = (array) $value;

            foreach ($drivers as $driver) {
                if (in_array($driver, $value)) {
                    $ext[] = $driver;
                } else {
                    $other[] = $driver;
                }
            }
            $opened = false;
            if (!empty($ext)) {
                $opened or $query->and_where_open();
                $opened = true;
                $query->or_where(array('onme_driver_name', 'IN', $ext));
            }
            if (in_array('other', $value)) {
                $opened or $query->and_where_open();
                $opened = true;
                $query->or_where(array('onme_driver_name', 'NOT IN', $other));
            }
            $opened and $query->and_where_close();

            return $query;
        },
    ),
    'appdesk' => array(
        'vertical' => true,
        'label' => __('Types'),
        'url' => 'admin/novius_onlinemediafiles/inspector/driver/list',
        'inputName' => 'onme_driver_name[]',
        'grid' => array(
            'columns' => array(
                'title' => array(
                    'headerText' => __('Types'),
                    'dataKey' => 'title',
                ),
                'id' => array(
                    'visible' => false,
                ),
                'icon' => array(
                    'visible' => false,
                ),
            ),
        ),
    ),
);
