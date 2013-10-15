<?
Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common'));

$media = \Novius\OnlineMediaFiles\Model_Media::find($enhancer_args['media_id']);
$driver = \Novius\OnlineMediaFiles\Driver::buildFromMedia($media);
?>
<div class="onlinemedia_preview ui-resizable">
    <?= $driver->display() ?>
</div>
<style type="text/css">
.onlinemedia_preview {
    overflow: hidden;
    width: 100%;
}
.onlinemedia_preview iframe {
    width: 100%;
}
[data-enhancer="novius_onlinemediafiles_display"] > a {
    display: inline-block;
}
</style>
