<?php

return array(
	'name'		=> __('Dailymotion'),
	'icon'		=> array(
		'16' => 'dailymotion.png',
	),
    // Fields to fetch using the API
    'api_fields' => array(
        'allow_comments',
        'allow_embed',
        'aspect_ratio',
        'comments_total',
        'country',
        'created_time',
        'description',
        'duration',
        'embed_html',
        'embed_url',
        'modified_time',
        'owner',
        'published',
        'rating',
        'tags',
        'thumbnail_240_url',
        'thumbnail_360_url',
        'thumbnail_480_url',
        'thumbnail_720_url',
        'thumbnail_url',
        'title',
        'views_total',
    ),
);
