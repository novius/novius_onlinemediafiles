=======================================================================================================================
INSTALLATION :
=======================================================================================================================

Installer l'application via le gestionnaire de plugins (admin NOS).

3 tables seront créées par le système de migrations.

=======================================================================================================================
UTILISATION :
=======================================================================================================================

1) Back-office :

    L'application propose un CRUD similaire à celui de la médiathèque.

    Un enhancer permet l'insertion d'un média dans dans un wysiwyg.

    Il est également possible d'utiliser le Renderer_Media pour associer un ou plusieurs médias à un Model, des
    exemples d'utilisation du Renderer sont disponibles plus bas dans la doc.

    Vous pouvez associer un ou plusieurs médias internet à un model :
        - dans le cas d'un seul média il suffit d'ajouter une colonne de type "INT(11)" sur votre model.
        - dans le cas de plusieurs médias il faut créer une relation has_many (et donc une table de liaison).

2) Front-office :

    Pour afficher le média internet associé à un model il y a plusieurs méthodes disponible.

        Afficher le player :
            $model->onlinemedia->display();

        Afficher la miniature :
            $model->onlinemedia->thumbnail();

    Vous pouvez accéder directement au driver associé au média et donc à ses méthodes en utilisant la méthode driver() :
            $model->onlinemedia->driver();

=======================================================================================================================
RENDERER :
=======================================================================================================================

The app provides you a renderer (Renderer_Media) to associate one or many media to any Model.

    Renderer in a CRUD :
        $config['fields']['onlinemedia'] = array(
            'label' => '',
            'renderer' => 'Novius\OnlineMediaFiles\Renderer_Media',
            'template' => '<div>{field}</div>',
            'form' => array(
                'title' => __('Média internet'),
            ),
        );

    Renderer static :
        \Novius\OnlineMediaFiles\Renderer_Media::renderer(array(
            'name'      => 'onlinemedias',
            'multiple'  => true,
            'values'    => array(1),
            'template'  => '<div>{field}</div>',
            'form'      => array(
                'title'     => __('Média internet'),
            ),
        ));

    Specific options are :
        array(
            'multiple'  => true,
            'provider_relation' => true, //if you use a provider to link medias to your model, this is needed
            'key_prefix' => 'my_prefix' //When you use a provider with the multiple option, you can set a custom prefix for your medias
        )

The new system of providers set by Novios OS 5.0 (Elche) permit you to easily add medias to any model. You have multiple way to use it.

* First, you need to set the provider to your model. Here is the configuration that is needed. We provide you all model that you need to link any model to many medias.

    Add an "has many" relation :
        'linked_online_medias' => array(
            'key_from'       => 'your_primary_key_field',
            'model_to'       => '\Novius\OnlineMediaFiles\Model_Link',
            'key_to'         => 'onli_foreign_id',
            'condition'      => 'where' => array(
                array('onli_from_table', '=', \DB::expr(\DB::quote(static::$_table_name))),
            ),
            'cascade_save'   => true,
            'cascade_delete' => true,
        ),

    Add the provider himself :
        'online_medias' => array(
            'relation' => 'linked_online_medias',
            'key_property' => 'onli_key',
            'value_property' => 'onli_onme_id',
            'value_relation' => 'media',
            'table_name_property' => 'onli_from_table',
        ),

    After, that, your provider is ready, you can use it into your crud in two ways :

    * For multiple media, you need to add a field wich name is the "has many" relation name. The 'provider_relation' option is also needed. Note that you can use this configuration without the multiple option.
        $config['fields']['linked_online_medias'] = array(
            'label' => __('Online medias'),
            'renderer' => 'Novius\OnlineMediaFiles\Renderer_Media',
            'renderer_options' => array(
                'multiple' => true,
                'provider_relation' => true,
            ),
            'form' => array(
                'title' => __('Online medias'),
            )
        ),

    * For a single relation type, just name it like a regular medias or a wysiwyg
        $config['fields']['online_medias->my_media_key->onli_onme_id'] = array(
            'label' => __('Online medias'),
            'renderer' => 'Novius\OnlineMediaFiles\Renderer_Media',
            'renderer_options' => array(
            ),
            'form' => array(
                'title' => __('Online medias'),
            )
        ),

=======================================================================================================================
TODO :
=======================================================================================================================

Charger le CSS via un require comme pour le JS et le faire prefixer par link!