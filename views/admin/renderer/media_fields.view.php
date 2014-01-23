<?php
/**
 * NOVIUS
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius.com
 */

\Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common'));

?>
<div id="<?= $id ?>" class="onlinemediafiles_renderer onlinemediafiles_renderer_<?= ($options['multiple'] ? 'multiple' : 'single') ?>">
    <?
    // Print the fields
    echo implode(' ', $fields);

    // Print the "add another" button if multiple
    if ($options['multiple']) {
        echo \Form::button('name', __('Add another online media'), array(
            'type'	=> 'button',
            'class' => 'add_another',
        ));
    }
    ?>
    <div style="clear: both"></div>
</div>
