<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

?>
<p>
    <?= __('Note: when no folders are selected, no restriction applies, all folders are accessible. The root folder is always accessible.') ?>
</p>

<p>
<?php
foreach (\Novius\OnlineMediaFiles\Model_Folder::find('all', array(
    'where' => array(
        array('onfo_parent_id', 2),
    ),
    'order_by' => 'onfo_title',
)) as $folder) {
    ?>
    <label style="display:block;">
        <input type="checkbox" name="perm[novius_onlinemediafiles::restrict_folders][]" value="<?= $folder->onfo_id ?>" <?= (int) $role->checkPermission('novius_onlinemediafiles::restrict_folders', $folder->onfo_id) ? 'checked' : '' ?> />
        <?= $folder->title_item(); ?>
    </label>
    <?php
}
?>
</p>
