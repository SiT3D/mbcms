<?php echo_modules($modules); ?>


<?php if (!\MBCMS\routes::is_ajax() ) : ?>
    <div class="bottom_js remove" style="display: none">

        setTimeout(function()
        {
        if (typeof site.configuration == 'undefined')
        {
        site.configuration = get_req('<?php echo($json); ?>');
        }
        }, 0);
    </div>
<?php endif; ?>

