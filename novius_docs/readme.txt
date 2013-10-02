=======================================================================================================================
INSTALLATION :
=======================================================================================================================

1) Installer l'application via le gestionnaire de plugins (admin NOS)

=======================================================================================================================
UTILISATION :
=======================================================================================================================

L'application ajoute par défaut un enhancer pour insérer une vidéo dans dans un wysiwyg.

Il est également possible d'utiliser le Renderer_Media pour associer un ou plusieurs médias à un Model.

=======================================================================================================================
Renderer :
=======================================================================================================================

L'application propose un renderer (Renderer_Media) pour associer un ou plusieurs médias à un Model.

    Renderer dans un CRUD :
        $config['fields']['videos'] = array(
            'label' => '',
            'renderer' => 'Novius\OnlineMediaFiles\Renderer_Media',
            'template' => '<div style="padding: 10px;">{field}</div>',
            'form' => array(
                'title' => __('Média distant'),
            ),
        );

    Renderer static :
        return \Novius\OnlineMediaFiles\Renderer_Media::renderer(array(
            'name'      => 'videos2',
            'multiple'  => true,
            'values'    => array(1),
            'template'  => '<div style="padding: 10px;">{field}</div>',
            'form'      => array(
                'title'     => __('Média distant'),
            ),
        ));

    Les options du renderer sont :
        array(
            'multiple'  => true,
        )
