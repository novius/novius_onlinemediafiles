<?php
$uniqid = uniqid('tabs_');
?>
<div id="<?= $uniqid ?>" class="box-sizing-border">
    <?= (string) Request::forge('admin/novius_onlinemediafiles/appdesk/index')->execute(array('media_pick'))->response() ?>
</div>
<script type="text/javascript">
    require(
        ['jquery-nos'],
        function($) {
            $(function() {
                $('#<?= $uniqid ?>').find('a[data-id=close]').click(function(e) {
                    e.preventDefault();
                    $container.nosDialog('close');
                });
            });
        });
</script>
