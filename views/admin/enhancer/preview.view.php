<?
$media = \Novius\OnlineMediaFiles\Model_Media::find($enhancer_args['media_id']);
$driver = \Novius\OnlineMediaFiles\Driver::buildFromMedia($media);
?>
<div class="onlinemedia_preview">
    <? $driver->display() ?>
</div>
<style type="text/css">
.onlinemedia_preview {
    overflow: hidden;
    width: 100%;
}
.onlinemedia_preview iframe {
    width: 100%;
}
</style>