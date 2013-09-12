<?
$media = \Novius\OnlineMediaFiles\Model_Media::find($enhancer_args['media_id']);
$driver = \Novius\OnlineMediaFiles\Driver::buildFromMedia($media);
?>
<div style="overflow: hidden">
    <?= $driver->display() ?>
</div>
