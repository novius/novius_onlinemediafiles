/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

define(
    [ 'jquery-nos', 'jquery-nos-loadspinner' ],
    function($) {
        "use strict";
        return function($btn_synchro, onme_id, form_id) {

            var $form = $('#' + form_id);

            var $wrapper_btn = $(this);
            var $container = $wrapper_btn.closest('.line');
            var $url = $form.find('input[name="onme_url"]');
            var $description = $form.find('textarea[name="onme_description"]');
            var $thumbnail = $form.find('input[name="onme_thumbnail"]');
            var $title = $form.find('input[name="onme_title"]');
            var $metadatas = $form.find('input[name="onme_metadatas"]');
            var $driver_name = $form.find('input[name="onme_driver_name"]');
            var last_url = $url.val();
            var last_sync_url = last_url;

            // Set the "resync" hidden field
            var $resync = $('<input type="hidden" name="resync" value="1" />').prependTo($url.closest('form'));
            if (last_url) {
                $resync.val(0);
            }

            // Move the sync button after the url field
            $wrapper_btn.appendTo($url.parent());

            // Initialize the spinner
            var $spinner = init_spinner()

            // Initialize properties panels
            if ($url.val() && onme_id) {
                show_properties();
            } else {
                hide_properties();
            }

            //à la modification de l'url, on check si le média est pris en charge
            $url.on('keyup change focus', function() {
                if ($url.val().length > 5) {
                    $btn_synchro.attr('disabled', false).animate({ opacity: 1 }, 200);
                } else {
                    $btn_synchro.attr('disabled', true).removeClass('ui-state-hover').animate({ opacity: 0.2 }, 200);
                }
                // Force a resync if the url change
                if (last_sync_url != last_url) {
                    $resync.val(last_sync_url != $url.val() ? 1 : 0);
                }
                last_url = $url.val();
            }).trigger('focus');

            // Sync button
            $btn_synchro.on('click', function(e) {
                e.preventDefault();
                var url = $url.val();
                hide_properties();
                $btn_synchro.removeClass('ui-state-hover');

                // Resync
                last_sync_url = url;
                $resync.val(1);

                // Ajax
                $spinner.trigger('enable');
                $wrapper_btn.nosAjax({
                    url: 'admin/novius_onlinemediafiles/ajax/fetch',
                    data: {
                        'url' : url
                    },
                    dataType    : 'json',
                    type        : 'POST',
                    success: function(json) {
                        if (typeof json.error != "undefined" && json.error.length) {
                            return ;
                        }

                        // Update fields
                        $title.val(json.title);
                        $description.val(json.description);
                        $thumbnail.val(json.thumbnail);
                        $metadatas.val(JSON.stringify(json.metadatas));
                        $driver_name.val(json.driver_name);

                        // Update preview
                        update_preview(json);

                        // Show properties
                        show_properties();

                        // No need to resync anymore
                        $resync.val(0);
                        last_sync_url = url;
                    },
                    complete: function() {
                        $spinner.trigger('disable');
                    }
                });
            });

            function update_preview(json) {
                if (json.display) {
                    $('.wrap_preview').html(json.display);
                } else if (json.thumbnail) {
                    $('.wrap_preview').html($('<img />').attr({
                        'src'   : json.thumbnail,
                        'title' : json.title.replace('"', '\"'),
                        'alt'   : json.title.replace('"', '\"')
                    }));
                } else {
                    $('.wrap_preview').html('<div class="nothing">Pas de prévisualisation disponible</div>');
                }
            }

            /**
             * Initialise le spinner
             *
             * @returns {*|jQuery}
             */
            function init_spinner() {
                return $('<div class="spinner-container tab-spinner"></div>')
                    .hide()
                    .on('enable', function() {
                        $spinner.show().addClass('ui-state-processing').loadspinner({
                            'diameter'    : 100,
                            'density'     : 80,
                            'speed'       : 4,
                            'scaling'     : true
                        }).loadspinner('start');
                        $spinner.closest('.nos-ostabs-panel').find('.ui-button').addClass('ui-state-disabled').attr('disabled', true);
                        $spinner.css({
                            'position'      : 'absolute',
                            'top'           : 0,
                            'left'          : 0,
                            'width'         : '100%',
                            'height'        : '100%',
                            'z-index'       : '1000',
                            'background'    : 'rgba(192, 205, 213, 0.8)'
                        })
                            .find('> canvas').css({
                                'position'      : 'absolute',
                                'top'           : '50%',
                                'left'          : '50%',
                                'margin-top'    : '-50px',
                                'margin-left'   : '-50px'
                            });
                    })
                    .on('disable', function() {
                        $spinner.hide().removeClass('ui-state-processing').loadspinner('destroy');
                        $spinner.closest('.nos-ostabs-panel').find('.ui-button').removeClass('ui-state-disabled').attr('disabled', false);
                    })
                    .prependTo($form);
            }

            /**
             * Affiche les propriétés de la vidéo
             *
             */
            function show_properties()
            {
//                $container.show();//css('visibility', 'visible');
                $container.css('height', 'auto');

//                // Thumbnail
//                $form.find('div.wrap_preview').html('');
//                var url_thumbnail = $thumbnail.val();
//                if (url_thumbnail) {
//                    $form.find('div.wrap_preview').html('<img src="' + url_thumbnail + '" alt="" border="0" style="margin: 5px 0;" />');
//                }

                // Metadatas
                $('.row_metadatas').remove();
                var metadatas = $metadatas.val();
                if (metadatas.length) {
                    try {
                        metadatas = JSON.parse(metadatas);
                        if (metadatas) {
                            $title.closest('table').find('tr:last').after('<tr class="row_metadatas"><th><label>Informations additionnelles</label></th><td><div class="wrap_metadatas"></div></td></tr>');
                            var $wrap_metadatas = $('.wrap_metadatas');
                            $wrap_metadatas.append(generate_table(metadatas));
                            $wrap_metadatas.addClass('expanded');
                            if ($wrap_metadatas.height() > 200) {
                                $wrap_metadatas.removeClass('expanded');
                                $('<a class="more" href="#">Afficher plus</a>').bind('click', function(e) {
                                    e.preventDefault();
                                    if ($wrap_metadatas.hasClass('expanded')) {
                                        $wrap_metadatas.removeClass('expanded');
                                        $(this).html('Afficher plus');
                                    } else {
                                        $wrap_metadatas.addClass('expanded');
                                        $(this).html('Afficher moins');
                                    }
                                }).appendTo($wrap_metadatas);
                            }
                        }
                    } catch (error) {
                        if (console) console.log(error.toString());
                    }
                }
            }

            function hide_properties()
            {
                $container.css('height', 0);
            }

            function generate_table(datas) {
                var $table = $('<ul class="list"></ul>');
                $.each(datas, function(key, val) {
                    if (typeof val == 'object') {
                        $('<li><strong>'+key+'</strong> : </li>').append(generate_table(val)).appendTo($table);
                    } else {
                        $table.append('<li><strong>'+key+'</strong> : '+escapeHTML(val)+'</li>');
                    }
                });
                return $table;
            }

            function escapeHTML(value) {
                if (typeof value != 'string') {
                    return value;
                }
                var tagsToReplace = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;'
                };
                return value.replace(/[&<>]/g, function(tag) {
                    return tagsToReplace[tag] || tag;
                });
            };
        }
    }
);