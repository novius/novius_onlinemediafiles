<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

\Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common'));

$enhancer_config = \Config::load('enhancer');

$appdeskview = (string) Request::forge('admin/novius_onlinemediafiles/appdesk/index')->execute(array('media_pick'))->response();

$uniqid = uniqid('tabs_');
$id_library = $uniqid.'_library';
$id_properties = $uniqid.'_properties';

// Set default values
$default_params = \Arr::get($enhancer_config, 'display.default_params', array());
foreach ($default_params as $field => $value) {
    $default_params[$field] = \Input::get($field, $value);
}

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
                        <input type="text" name="media_width" data-id="media_width" size="5" id="media_width" value="<?= \Arr::get($default_params, 'media_width') ?>" />
                    </td>
                </tr>
                <tr>
                    <th><?= __('Height') ?></th>
                    <td>
                        <input type="text" name="media_height" data-id="media_height" size="5" id="media_height" value="<?= \Arr::get($default_params, 'media_height') ?>" />
                    </td>
                </tr>
                <?php if (\Arr::get($novius_onlinemediafiles_config, 'alignment.enabled')) { ?>
                <tr>
                    <th><?= __('Alignment') ?></th>
                    <td>
                        <select name="media_align" id="media_align">
                            <option value=""></option>
                            <option value="left"<?= (\Arr::get($default_params, 'media_align') == 'left') ? ' selected="selected"' : '' ?>><?= __('Left') ?></option>
                            <option value="center"<?= (\Arr::get($default_params, 'media_align') == 'center') ? ' selected="selected"' : '' ?>><?= __('Center') ?></option>
                            <option value="right"<?= (\Arr::get($default_params, 'media_align') == 'right') ? ' selected="selected"' : '' ?>><?= __('Right') ?></option>
                            <option value="left-float"<?= (\Arr::get($default_params, 'media_align') == 'left-float') ? ' selected="selected"' : '' ?>><?= __('Left (floating)') ?></option>
                            <option value="right-float"<?= (\Arr::get($default_params, 'media_align') == 'right-float') ? ' selected="selected"' : '' ?>><?= __('Right (floating)') ?></option>
                        </select>
                    </td>
                </tr>
                <?php } ?>
                <?php if (\Arr::get($novius_onlinemediafiles_config, 'responsive.enabled')) { ?>
                <tr>
                    <th><?= __('Enable mobile support') ?></th>
                    <td>
                        <input type="radio" name="media_responsive" id="media_responsive-no" value="1" <?= \Arr::get($default_params, 'media_responsive') ? 'checked="true"' : '' ?> /> Oui
                        <input type="radio" name="media_responsive" id="media_responsive-yes" value="0" <?= !\Arr::get($default_params, 'media_responsive') ? 'checked="true"' : '' ?> /> Non
                    </td>
                </tr>
                <?php } ?>
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
