<?php \Nos\I18n::current_dictionary(array('novius_onlinemediafiles::common', 'noviusos_media::common', 'nos::common')); ?>
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
