define(
    [
        'jquery-nos'
    ],
    function($) {
        "use strict";
        return function($btn_synchro, onme_id, form_id) {

            var $wrapper_btn = $(this);
            var $left_col = $wrapper_btn.parent();
            var $right_col = $left_col.next('div');
            var $form = $('#' + form_id);
            var $url = $form.find('input[name="onme_url"]');
            var $description = $form.find('textarea[name="onme_description"]');
            var $thumbnail = $form.find('input[name="onme_thumbnail"]');
            var $title = $form.find('input[name="onme_title"]');
            var $metadatas = $form.find('input[name="onme_metadatas"]');
            var $driver_name = $form.find('input[name="onme_driver_name"]');
            var first_url = $url.val();
            var $wrapper_url = $url.parent();
            var btn_visible = false;

            //on déplace $wrapper en dehors des 2 colonnes
            $wrapper_btn.appendTo($wrapper_url);

            //si le média n'est pas encore récupérée, on masque les propriétés pour n'afficher que l'url
            if (!first_url || !onme_id) {
                init_retrieve();
            } else {
                show_thumbnail();
                $wrapper_btn.css({opacity: 0});
            }

            //à la modification de l'url, on check si le média est pris en charge
            $url.on('keyup change focus', function(){
                verif_url();
            });

            //action de synchro du media
            $btn_synchro.on('click', function(e){
                if (!btn_visible) {
                    e.preventDefault();
                    return false;
                }

                btn_visible = false;
                $wrapper_btn.css({opacity: 0});

                if (!onme_id) {
                    first_url = $url.val();
                }

                //appel ajax
                $wrapper_btn.nosAjax({
                    url: 'admin/novius_onlinemediafiles/ajax/fetch',
                    data: {
                        'url' : $url.val()
                    },
//                    cache: false,
//                    contentType: false,
//                    processData: false,
                    type: 'POST',
                    success: function(json) {
                        if (typeof json.error === "undefined") {
                            $description.val(json.description);
                            $thumbnail.val(json.thumbnail);
                            $title.val(json.title);
                            $metadatas.val(json.metadatas);
                            $driver_name.val(json.driver_name);
                            show_thumbnail();
                            $left_col.css({opacity: 0, visibility: "visible"}).animate({opacity: 1}, 200);
                            $right_col.css({opacity: 0, visibility: "visible"}).animate({opacity: 1}, 200);
                        } else {
//                            btn_visible = true;
//                            $wrapper_btn.css({opacity: 0});
                        }
                    },
                    error: function() {
                        console.log('error');
                    }
                });

                /*
                la valeur $return.result contient 1 pour success et 0 pour echec
                 */
                var $return = {
                    'result' : 0,
                    'title' : 'Titre',
                    'thumbnail-maxi' : '',
                    'description' : ''
                };
                //success
                if ($return.result == 1) {

                }
                e.preventDefault();
            });

            /**
             *
             */
            function init_retrieve ()
            {
                $wrapper_btn.css({opacity: 0});
                $left_col.css('visibility', 'hidden');
                $right_col.css('visibility', 'hidden');
            }

            /**
             *
             */
            function verif_url ()
            {
                if ($url.val() && $url.val() != first_url) {
                    if (!btn_visible) {
                        $wrapper_btn.css({opacity: 0, visibility: "visible"}).animate({opacity: 1}, 200);
                        btn_visible = true;
                    }
                } else {
                    if (btn_visible) {
                        $wrapper_btn.css({opacity: 1, visibility: "visible"}).animate({opacity: 0}, 100);
                        btn_visible = false;
                    }
                }
            }

            /**
             *
             */
            function show_thumbnail ()
            {
                var url_thumbnail = $thumbnail.val();
                if ($form.find('div.wrap_thumbnail').length) {
                    $form.find('div.wrap_thumbnail').remove();
                }
                if (url_thumbnail) {
                    $('<div class="wrap_thumbnail"><br /><img src="' + url_thumbnail + '" alt="" border="0" style="margin: 5px 0;" /></div>').insertAfter($description);
                }
            }

        }
    }
);