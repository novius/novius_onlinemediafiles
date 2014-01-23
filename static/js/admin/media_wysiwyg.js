/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

define(['jquery-nos', 'wijmo.wijtabs'],
    function($) {
        "use strict";

        $.fn.extend({
            mediaWysiwyg : function(params) {
                params = params || {
                    newMedia: true,
                    appdeskView: '',
                    base_url: '',
                    texts: {
                        mediaFirst: 'Please choose a media first'
                    }
                };
                return this.each(function() {
                    var $container = $(this)
                            .find('form')
                            .submit(function(e) {
                                e.stopPropagation();
                                e.preventDefault();
                                var self = this;
                                $(self).ajaxSubmit({
                                    dataType: 'json',
                                    success: function(json) {
                                        log(json);
                                        $container.closest('.ui-dialog-content').trigger('save.enhancer', json);
//                                        $('.nosEnhancer').each(function() {
//                                            console.log($(this).html());
//                                            if ($(this).data('enhancer') == 'novius_onlinemediafiles_display') {
//                                                $(this).find('iframe').each(function() {
//                                                    var $this = $(this);
//                                                    if (!$this.closest('.onlinemedia_preview').length) {
//                                                        $this.after('<div class="onlinemedia_preview">'+$this.html()+'</div>');
//                                                        $this.remove();
//                                                    }
//                                                });
//                                            }
//                                        });
                                    },
                                    error: function(error) {
                                        $.nosNotify('An error occured', 'error');
                                    }
                                });
                            })
                            .end()
                            .find('a[data-id=close]')
                            .click(function(e) {
                                e.preventDefault();
                                $container.nosDialog('close');
                            })
//                            .end()
//                            .find('button[data-id=save]')
//                            .click(function(e) {
//                                alert('o');
////                                var img = $('<img />');
////
////                                if (!media || !media.id) {
////                                    $.nosNotify(params.texts.imageFirst, 'error');
////
////                                    return;
////                                }
////
////                                img.attr('height', $height.val());
////                                img.attr('width',  $width.val());
////                                img.attr('title',  $title.val());
////                                img.attr('alt',    $alt.val());
////                                img.attr('style',  $style.val());
////
////                                img.attr('data-media', JSON.stringify(media));
////                                img.attr('src', params.base_url + media.path);
////
////                                $dialog.trigger('insert.media', img);
////                                e.stopPropagation();
////                                e.preventDefault();
//                            })
                            .end()
                            .find('> ul')
                            .css({
                                width : '18%'
                            })
                            .end(),
                        $dialog = $container.closest('.ui-dialog-content')
                            .bind('select_media', function(e, data) {
                                tinymce_media_select(data);
                            }),
                        $library = $container.find('div:eq(0)')
                            .css({
                                width : '100%',
                                padding: 0,
                                margin: 0
                            }),
                        media = null,
                        $media_id = $container.find('input[data-id=media_id]'),
                        tinymce_media_select = function(media_json, media_dom) {
                            media = media_json
                            if (media_dom == null) {
                                $media_id.val(media_json.id);
                                $container.wijtabs('enableTab', 1)
                                    .wijtabs('select', 1);
                                return;
                            }
//
//                            $height.val(media_dom.attr('height'));
//                            $width.val(media_dom.attr('width'));
//                            $title.val(media_dom.attr('title'));
//                            $alt.val(media_dom.attr('alt'));
//                            $style.val(media_dom.attr('style'));
                        },
                        ed = $dialog.data('tinymce');
//                        e = ed.selection.getNode();

//                    // Editing the current image
//                    if (e.nodeName == 'IMG') {
//                        var $img = $(e),
//                            media_id = $img.data('media-id');
//
//                        // No data available yet, we need to fetch them
//                        if (media_id) {
//                            $.ajax({
//                                method: 'GET',
//                                url: params.base_url + 'admin/noviusos_media/appdesk/info/' + media_id,
//                                dataType: 'json',
//                                success: function(item) {
//                                    tinymce_image_select(item, $img);
//                                }
//                            });
//                        } else {
//                            tinymce_image_select($img.data('media'), $img);
//                        }
//                    }
//
                    $container.wijtabs({
                        alignment: 'left',
                        load: function(e, ui) {
                            var margin = $(ui.panel).outerHeight(true) - $(ui.panel).innerHeight();
                            $(ui.panel).height($dialog.height() - margin);
                        },
                        disabledIndexes: params.newMedia ? [1] : [],
                        show: function(e, ui) {
                            $(ui.panel).nosOnShow();
                        }
                    })
                        .find('.wijmo-wijtabs-content')
                        .css({
                            width: '81%',
                            position: 'relative'
                        })
                        .addClass('box-sizing-border')
                        .end()
                        .nosFormUI();
//
//                    $proportional.triggerHandler('change');
//                    $same_title_alt.triggerHandler('change');
//
                    if (!params.newMedia) {
                        $container.wijtabs('select', 1)
                            .bind('wijtabsshow', function() {
                                $library.html(params.appdeskView);
                            });
                    } else {
                        $library.html(params.appdeskView);
                    }
                });
            }
        });

        return $;
    });
