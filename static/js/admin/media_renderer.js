define([
    'jquery-nos',
    'static/novius-os/admin/vendor/jquery/jquery-ui-input-file-thumb/js/jquery.input-file-thumb',
    'link!static/novius-os/admin/vendor/jquery/jquery-ui-input-file-thumb/css/jquery.input-file-thumb.css'
], function ($) {
    return function (context) {
        var $context = $(context);
        $(function() {

            var $skeleton = false;

            $context.find('input.onlinemediafile_input').each(function() {
                initialize_input($(this));
            });

            $context.find('.add_another').each(function() {
                var $this = $(this);
                if (!$this.data('add-offset')) {
                    $this.data('add-offset', 1);
                    $this.on('click', function(e) {
                        e.preventDefault();
                        var $first = $this.closest('.onlinemediafiles_renderer').find('.add_field:first');
                        var $clone = $skeleton.clone();
                        var $input = $clone.find('input.onlinemediafile_input').val('');

                        // Reset le squelette
                        var media_options = $input.data('media-options') || $input.attr('data-media-options') || {};
                        if (typeof media_options.inputFileThumb != 'object') {
                            media_options.inputFileThumb = {};
                        }
                        media_options.inputFileThumb.file = '';
                        media_options.inputFileThumb.title = '';
                        $input.data('media-options', media_options);

                        // Generate a new ID
                        var offset = $this.data('add-offset');
                        $this.data('add-offset', offset + 1);
                        $input.attr('id', $first.find('input.onlinemediafile_input').attr('id')+'_'+offset);

                        $this.before($clone);
                        initialize_input($input);
                    });
                    $this.data('initialized', true);
                }
            });

            function initialize_input($input) {
                if (!$skeleton) {
                    // Create the skeleton
                    $skeleton = $input.closest('.add_field').clone().attr('id', false);
                }

                var data = $input.data('media-options') || {};
                var contentUrls = {
                        'single'   : 'admin/novius_onlinemediafiles/renderer/popup'
                    },
                    titles = {
                        'single'   : $.nosTexts.chooseMediaFile
                    };

                var options = $.extend({
                    title: $input.attr('title') || 'File',
                    allowDelete : true,
                    classes: data.mode,
                    choose: function() {
                        var $dialog = $input.nosDialog({
                            destroyOnClose : true,
                            contentUrl: contentUrls[data.mode],
                            ajax: true,
                            title: titles[data.mode]
                        }).bind('select_media', function(e, item) {
                                $input.inputFileThumb({
                                    file    : item.thumbnail || item.thumbnailAlternate || item.icon || false,
                                    title   : item.title || false
                                });
                                $input.val(item.id).trigger('change', {
                                    item : item
                                });
                                $dialog.nosDialog('close');
                            });
                    },
                    'delete': function(e) {
                        $input.inputFileThumb({
                            file    : false,
                            title   : false
                        });
                    }
                }, data.inputFileThumb || {});

                $input.inputFileThumb(options);
                $input.prependTo($input.closest('.ui-widget-content'));
            }
        });
    }
});
