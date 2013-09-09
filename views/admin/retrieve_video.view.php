<?php
$wrapper_button_id = uniqid('wrapper_button_');
$wrapper_synchro_id = uniqid('wrapper_synchro_');
$btn_synchro_id = uniqid('btn_synchro_');
?>
<script type="text/javascript">
    require(['jquery-nos', 'static/apps/novius_onlinemediafiles/js/admin/retrieve_video.js'], function ($, callback_fn) {
        $(function () {
            callback_fn.call($('#<?= $wrapper_button_id ?>'), $('#<?= $wrapper_synchro_id ?>'), $('#<?= $btn_synchro_id ?>'), <?= intval($item->onme_id) ?>, '<?= $fieldset->form()->get_attribute('id') ?>');
        });
    });
</script>
<div id="<?= $wrapper_button_id ?>">
    <button id="<?= $btn_synchro_id ?>" class="ui-icon-refresh primary">Synchroniser le média</button>
</div>
<div id="<?= $wrapper_synchro_id ?>">
A
</div>
