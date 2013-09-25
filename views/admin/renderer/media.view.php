<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
?>
<script type="text/javascript">
    require(
        ['jquery-nos'],
        function ($) {
            $(function() {
                $(':input#<?= $id ?>').each(function() {
                    var $input = $(this);

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

                    require([
                        'static/novius-os/admin/vendor/jquery/jquery-ui-input-file-thumb/js/jquery.input-file-thumb',
                        'link!static/novius-os/admin/vendor/jquery/jquery-ui-input-file-thumb/css/jquery.input-file-thumb.css'
                    ], function() {
                        $(function() {
                            $input.inputFileThumb(options);
                            $input.prependTo($input.closest('.ui-widget-content'));
                        });
                    });

                });
            });
        });
</script>
