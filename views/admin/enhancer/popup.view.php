<?php
\Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'nos::common'));

$appdeskview = (string) Request::forge('admin/novius_onlinemediafiles/appdesk/index')->execute(array('media_pick'))->response();
$uniqid = uniqid('tabs_');
$id_library = $uniqid.'_library';
$id_properties = $uniqid.'_properties';

?>
<style type="text/css">
    .box-sizing-border {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        height: 100%;
    }
</style>
<div id="<?= $uniqid ?>" class="box-sizing-border">
    <ul>
        <li><a href="#<?= $id_library ?>"><?= $media_id ? __('Pick another media') : __('1. Pick a media') ?></a></li>
        <li><a href="#<?= $id_properties ?>"><?= $media_id ? __('Edit properties') : __('2. Set the properties') ?></a></li>
    </ul>

    <div id="<?= $id_library ?>" class="box-sizing-border"></div>

    <div id="<?= $id_properties ?>">
        <form action="<?= $url ?>" method="POST">
            <input type="hidden" name="media_id" data-id="media_id" size="5" id="<?= $uniqid ?>_media_id" value="<?= $media_id ?>" />
            <input type="hidden" name="enhancer" value="novius_onlinemediafiles_display" />
            <table class="fieldset">
                <tr>
                    <th><?= __('Width')?></th>
                    <td>
                        <input type="text" name="media_width" data-id="media_width" size="5" id="media_width" value="<?= \Input::get('media_width', '') ?>" />
                    </td>
                </tr>
                <tr>
                    <th><?= __('Height') ?></th>
                    <td>
                        <input type="text" name="media_height" data-id="media_height" size="5" id="media_height" value="<?= \Input::get('media_height', '') ?>" />
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td> <button type="submit" class="primary" data-icon="check" data-id="save"><?= $media_id ? __('Update this media') : __('Insert this media') ?></button> &nbsp; <?= __('or') ?> &nbsp; <a data-id="close" href="#"><?= __('Cancel') ?></a></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script type="text/javascript">
    require(
        ['jquery-nos', 'static/apps/novius_onlinemediafiles/js/admin/media_wysiwyg.js'],
        function($) {
            $(function() {
                $('#<?= $uniqid ?>').mediaWysiwyg({
                    newMedia: !'<?= ($media_id) ?>',
                    appdeskView: <?= \Format::forge()->to_json($appdeskview) ?>,
                    base_url: '<?= \Uri::base(true) ?>',
                    texts: {
                        imageFirst: <?= \Format::forge()->to_json(__('This is unusual: It seems that no media has been selected. Please try again. Contact your developer if the problem persists. We apologise for the inconvenience caused.')) ?>
                    }
                });
            });
        });
</script>
