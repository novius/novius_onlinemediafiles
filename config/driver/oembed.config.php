<?php

return array(
	'name'		=> __('Oembed'),
	'icon'		=> array(
		'16' => 'oembed.png',
	),
    // Oembed api configuration
    'api'       => array(
        'path'      => '/services/oembed',
        'parameters'    => array(
            'format'        => 'json',
        ),
    ),
);
