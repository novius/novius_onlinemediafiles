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
    <label>
        <input type="radio" name="perm[novius_onlinemediafiles::media][]" value="2_full_access" <?= (int) $role->getPermissionValue('novius_onlinemediafiles::media', 2) == 2 ? 'checked' : '' ?> />
        <?= __('Can add, edit and delete media files') ?>
    </label>
</p>

<p>
    <label>
        <input type="radio" name="perm[novius_onlinemediafiles::media][]" value="1_draft_only" <?= (int) $role->getPermissionValue('novius_onlinemediafiles::media', 2) == 1 ? 'checked' : '' ?> />
        <?= __('Can visualise and use media files only') ?>
    </label>
</p>

<p>
    <label>
        <input class="valueUnchecked" type="checkbox" name="perm[novius_onlinemediafiles::folder][]" value="yes" value-unchecked="no" <?= $role->getPermissionValue('novius_onlinemediafiles::folder', 'yes') == 'yes' ? 'checked' : '' ?> />
        <?= __('Can add, edit and delete folders') ?>
    </label>
</p>
