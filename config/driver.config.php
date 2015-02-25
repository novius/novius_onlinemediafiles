<?php

return array(
    'http' => array(
        'timeout' => 5,
    ),
    // Default params for the "display" method
    'display' => array(
        'template'		=> '{display}',
        'attributes'	=> array(
            'width'			=> 480,
            'height'		=> 270,
            'frameborder'	=> '0',
        ),
        /*
        'responsive' => array(
            'enabled' => true,
            // The path of the responsive stylesheet (set an empty value to disable)
            'css_path'  => 'static/apps/novius_onlinemediafiles/css/responsive-driver.css',
            // The css class wrapping the media (set an empty value to disable)
            'css_class' => 'onlinemediafiles-fluid-wrapper-driver',
        ),
        'align'         => '',
        */
    ),
);
