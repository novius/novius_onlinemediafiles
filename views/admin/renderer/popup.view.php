<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

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
