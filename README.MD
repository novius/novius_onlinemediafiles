# Introduction

Online Media Files is an application for Novius OS for managing and displaying online media files.

Supported providers are :
* Youtube
* Dailymotion
* Vimeo
* Flickr
* Instagram
* Soundcloud

In addition, any platforms that implements the oEmbed format is supported (see below for more informations).

# Requirements

The Online Media File application runs on Novius OS Chiba 2 and upper.

# Installation

* [How to install a Novius OS application](http://community.novius-os.org/how-to-install-a-nos-app.html)

# Documentation

After you installed this application in your Novius OS, you will be able to manage online media files and display them on your front pages using an enhancer and attach them on a model

## Driver

This application uses drivers to fetch and display online media files.

### Structure

A driver is composed of two files :

* A configuration file `novius_onlinemediafiles/config/driver/example.config.php` :

```php
return array(
    'name'		=> __('Example'),
    'icon'		=> array(
        '16' => 'example.png',
    ),
);
```

* A class file `novius_onlinemediafiles/config/driver/example.config.php` that extends `Driver` and implements at least these methods :

```php
class Driver_Example extends Driver {

    /**
     * Test whether the driver is compatible with the online media
     *
     * @return bool|mixed
     */
    public function compatible();

    /**
     * Fetch the online media attributes (title, description, metadatas...)
     *
     * @return bool|mixed
     */
    public function fetch();
}
```

### Available drivers

Available drivers are set in the configuration file `novius_onlinemediafiles/config/config.php` :

```php
    /*
     * Available drivers
     */
    'drivers' => array(
        'Youtube',
        'Dailymotion',
        'Vimeo',
    		'Soundcloud',
    		'Flickr',
    		'Instagram',
    		'Oembed',
    ),
```

### Create a driver

You can create a new driver if the existing ones doesn't fit your needs. You just have to create the class file with the required methods (see the **Structure** section above) :

```php
namespace Demo;

class Driver_Custom extends Novius\OnlineMediaFiles\Driver {
  ...
}
```

... and the configuration file:

```php
return array(
    'name'		=> __('Example'),
    'icon'		=> array(
        '16' => 'example.png',
    ),
);
```

Then add your driver to the list of available drivers:

```php
\Event::register_function('config|novius_onlinemediafiles::config', function(&$config) {
    $config['drivers'][] = 'Demo\Driver_Custom';
});
```

### oEmbed

The driver `Driver_Oembed` supports plateforms that implements the oEmbed API.

Because the oEmbed API implementation path differs between plateforms, the driver uses the `path_mapping` property in the driver's configuration to match the API path by host. If no host matches then the default path `api.path` is used. 

You can add custom host/path map :

```php
\Event::register_function('config|novius_onlinemediafiles::driver/oembed', function(&$config) {
    $config['path_mapping']['example.com'] = '/custom/path/oembed';
});
```

## Online media on a model

### Attach a single online media :

Add a new property on your model :
```php
protected static $_properties = array(
    'model_onme_id' => array(
        'default' => null,
        'data_type' => 'int unsigned',
        'null' => true,
    ),
);
```

Create the new field in the table :
```sql
ALTER TABLE  `model` ADD `model_onme_id` INT( 11 ) NULL DEFAULT NULL;
```

Add a belongs_to relation on your model :
```php
protected static $_belongs_to = array(
    'media' => array(
        'key_from'       => 'model_onme_id',
        'model_to'       => 'Novius\OnlineMediaFiles\Model_Media',
        'key_to'         => 'onme_id',
        'cascade_save'   => false,
        'cascade_delete' => false,
    ),
);
```

Add the renderer in the CRUD configuration file :

```php
'fields'  => array(
  'online_media' = array(
      'label' => '',
      'renderer' => 'Novius\OnlineMediaFiles\Renderer_Media',
      'template' => '<div>{field}</div>',
      'form' => array(
          'title' => __('Online media'),
      ),
  )
)
```

### Attach many online medias :

Add a many_many relation on your model :
```php
protected static $_many_many = array(
    'medias' => array( // key must be defined, relation will be loaded via $blocmagique->key
        'table_through'     => 'model_onlinemedias',
        'key_from'          => 'model_id',
        'key_through_from'  => 'moom_model_id'
        'key_through_to'    => 'moom_onme_id',
        'key_to'            => 'onme_id',
        'cascade_save'      => false,
        'cascade_delete'    => false,
        'model_to'          => 'Novius\OnlineMediaFiles\Model_Media',
    )
);
```

Create a new table to store the links between your model and the online medias :
```sql
CREATE TABLE IF NOT EXISTS `model_onlinemedias` (
    `moom_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `moom_model_id` int(11) unsigned NOT NULL,
    `moom_onme_id` int(11) unsigned NOT NULL,
    PRIMARY KEY (`moom_id`),
    KEY `omedia_model_id` (`moom_model_id`),
    KEY `omedia_media_id` (`moom_onme_id`)
) DEFAULT CHARSET=utf8 ;
```

Add the renderer in the CRUD configuration file :

```php
'fields'  => array(
  'online_medias' = array(
      'label' => '',
      'renderer' => 'Novius\OnlineMediaFiles\Renderer_Media',
      'template' => '<div>{field}</div>',
      'form' => array(
          'title' => __('Online media'),
      ),
  )
)
```

## Display an online media

### Enhancer

You can use the `Online media` enhancer to display an online media in a page or wysiwyg.

### From a model

Example on a model with a `many_many` relation named `online_medias`:

```php
foreach ($model->online_medias as $online_media) {
    
    // Displays the online media thumbnail
    echo Html::img($online_media->thumbnail());
    
    // Displays the embedded online media
    echo $online_media->display();
}
```

You can get the driver using the `driver()`:

```php
// Get the driver
$driver = $model->media->driver();
if (!empty($driver)) {
    
    // Dump the online media attributes
    var_dump($driver->attributes());
}
```

## Renderer

This application provides a renderer `Novius\OnlineMediaFiles\Renderer_Media` similar to `Nos\Media\Renderer_Media`.

Example of static renderer :

```php
\Novius\OnlineMediaFiles\Renderer_Media::renderer(array(
    'name'      => 'online_media',
    'multiple'  => true,
    'values'    => array(1),
    'template'  => '<div>{field}</div>',
    'form'      => array(
        'title'     => __('Média internet'),
    ),
));
```

When using in a CRUD the renderer determines automatically the `multiple` attribute, based on the relation type used to store de online media(s) (`belongs_to` or `many_many`).