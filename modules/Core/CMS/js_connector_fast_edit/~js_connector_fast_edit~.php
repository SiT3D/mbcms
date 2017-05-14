<script delete_this="js_connector_fast_edit">

    option.fast_edit("<?php echo $className; ?>", function (data)
    {
        <?php echo ($function_text); ?>
    });
    $('[delete_this=js_connector_fast_edit]:first').remove();
</script>

