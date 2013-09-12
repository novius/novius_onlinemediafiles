<?php
$wrapper_button_id = uniqid('wrapper_button_');
$btn_synchro_id = uniqid('btn_synchro_');
?>
<script type="text/javascript">
    require(['jquery-nos', 'static/apps/novius_onlinemediafiles/js/admin/retrieve_video.js'], function ($, callback_fn) {
        $(function () {
            callback_fn.call($('#<?= $wrapper_button_id ?>'), $('#<?= $btn_synchro_id ?>'), <?= intval($item->onme_id) ?>, '<?= $fieldset->form()->get_attribute('id') ?>');
        });
    });
</script>
<div id="<?= $wrapper_button_id ?>">
    <button id="<?= $btn_synchro_id ?>" class="ui-icon-refresh primary">Synchroniser le média</button>
</div>
<style type="text/css">
.wrap_thumbnail img {
    max-width: 100%;
}
</style>
