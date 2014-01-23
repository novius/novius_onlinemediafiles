<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */
?>
<script type="text/javascript">
require(['static/apps/novius_onlinemediafiles/js/admin/media_renderer.js'],
    function(renderer) {
        // Initialize the renderer
        renderer('#<?= $id ?>');
}
);
</script>
