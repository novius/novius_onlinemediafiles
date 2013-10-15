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

L'application propose un renderer (Renderer_Media) pour associer un ou plusieurs médias à un Model.

    Renderer dans un CRUD :
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

    Les options spécifiques au renderer sont :
        array(
            'multiple'  => true,
        )
