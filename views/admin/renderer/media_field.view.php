<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link       http://www.novius.com
 */

?>
<div
    class="add_field <?= $sortable ? 'ui-state-default sortable' : '' ?>">
    <?= str_replace('{field}', $field, (isset($template) ? $template : '{field}')) ?>
</div>
