<form class="mbcms-form" enctype="multipart/form-data"
    <?php attr($method, 'method='); ?>
        style="<?php attr($display, 'display:')?>"
    <?php echo_attrs($__cms_attrs); ?>
    <?php attr($not_ajax_send, 'not_ajax='); ?>>

    <?php echo_modules($modules); ?>
</form>
