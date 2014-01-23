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

$wrapper_button_id = uniqid('wrapper_button_');
$btn_synchro_id = uniqid('btn_synchro_');
?>
<script type="text/javascript">
    require(['jquery-nos', 'static/apps/novius_onlinemediafiles/js/admin/sync_video.js'],
        function ($, callback_fn) {
            $(function () {
                callback_fn.call($('#<?= $wrapper_button_id ?>'), $('#<?= $btn_synchro_id ?>'), <?= intval($item->onme_id) ?>, '<?= $fieldset->form()->get_attribute('id') ?>');
            });
        }
    );
</script>
<style type="text/css">
.wrap_preview .nothing {
    padding: 10px;
    font-style: italic;
    color: #555555;
}
.wrap_preview * {
    max-width: 100%;
}
.wrap_preview iframe {
    max-height: 280px;
}
.wrap_metadatas {
    position: relative;
    overflow: hidden;
    max-height: 200px;
    padding: 5px 10px 30px 10px;
    border: 1px solid #a8a8a8;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}
.wrap_metadatas.expanded {
    max-height: none;
}
.wrap_metadatas ul {
    margin: 0;
    list-style-type: none;
}
.wrap_metadatas ul ul {
    margin: 0.5em 10px;
}
.wrap_metadatas ul.value {
    list-style: none;
}
.wrap_metadatas ul li {
    margin: 0.5em 0;
    word-wrap: break-word;
    word-break: break-all;
    white-space: normal;
    padding-left: 15px;
}
.wrap_metadatas ul li:before {
    content: "\2023";
    position: absolute;
    margin-left: -15px;
    font-size: 20px;
    line-height: 18px;
    color: #aaaaaa;
}
.wrap_metadatas .more {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 5px 0;
    text-align: center;
    text-decoration: none;
    font-size: 12px;
    color: #555555;
    background: #eeeeee;
    border-top: 1px solid #ccc;
}
</style>
<div id="<?= $wrapper_button_id ?>">
    <button id="<?= $btn_synchro_id ?>" class="ui-icon-refresh primary"><?= __('Synchroniser') ?></button>
</div>
