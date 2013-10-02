require([
    'jquery-nos',
    'static/novius-os/admin/vendor/jquery/jquery-ui-input-file-thumb/js/jquery.input-file-thumb',
    'link!static/novius-os/admin/vendor/jquery/jquery-ui-input-file-thumb/css/jquery.input-file-thumb.css'
], function ($) {
    $(function() {

        var $skeleton = false;

        $('input.onlinemediafile_input').each(function() {
            initialize_input($(this));
        });

        $('.add_another').each(function() {
            var $this = $(this);
            if (!$this.data('add-offset')) {
                $this.data('add-offset', 1);
                $this.on('click', function(e) {
                    e.preventDefault();
                    var $first = $this.closest('.onlinemediafiles_renderer').find('.add_field:first');
                    var $clone = $skeleton.clone();
                    var $input = $clone.find('input.onlinemediafile_input').val('');

                    // Reset le média selectionné
                    var media_options = $input.data('media-options');
                    media_options.inputFileThumb.file = '';
                    $skeleton.data('media-options', media_options);

                    // Generate new ID
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
                $skeleton = $input.closest('.add_field').clone();
                // Reset le média selectionné
                var media_options = $skeleton.find('input.onlinemediafile_input').data('media-options');
                if (media_options.inputFileThumb && media_options.inputFileThumb.file) {
                    media_options.inputFileThumb.file = '';
                    $skeleton.find('input.onlinemediafile_input').data('media-options', media_options);
                }
            }

            var data = $input.data('media-options');
            data = data || {};
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
                                file: item.thumbnail || item.thumbnailAlternate || item.icon || false
                            });
                            $input.val(item.id).trigger('change', {
                                item : item
                            });
                            $dialog.nosDialog('close');
                        });
                }
            }, data.inputFileThumb || {});

            $input.inputFileThumb(options);
            $input.prependTo($input.closest('.ui-widget-content'));
        }
    });
});
