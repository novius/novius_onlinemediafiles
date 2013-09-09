define(
    [
        'jquery-nos'
    ],
    function($) {
        "use strict";
        return function($wrapper_synchro, $btn_synchro, onme_id, form_id) {

            var $wrapper_btn = $(this);
            var $left_col = $wrapper_btn.parent();
            var $right_col = $left_col.next('div');
            var $form = $('#' + form_id);
            var $url = $form.find('input[name="onme_url"]');
            var first_url = $url.val();
            var $wrapper_url = $url.parent();
            var btn_visible = false;

            //on déplace $wrapper en dehors des 2 colonnes
            $wrapper_btn.appendTo($wrapper_url);

            //si le média n'est pas encore récupérée, on masque les propriétés pour n'afficher que l'url
            if (!first_url || !onme_id) {
                init_retrieve();
            }

            //à la modification de l'url, on check si le média est pris en charge
            $url.on('keyup change focus', function(){
                verif_url();
            });

            //action de synchro du media
            $btn_synchro.on('click', function(e){
                //appel ajax
                var $return = {
                    'title' : 'Titre',
                    'thumbnail-maxi' : '',
                    'description' : ''
                };
                e.preventDefault();
            });

            function init_retrieve ()
            {
                $wrapper_btn.css({opacity: 0});
                $left_col.css('visibility', 'hidden');
                $right_col.css('visibility', 'hidden');
            }

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

        }
    }
);