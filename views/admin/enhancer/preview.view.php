<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

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
