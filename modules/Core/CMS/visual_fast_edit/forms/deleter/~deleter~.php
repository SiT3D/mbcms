<?php echo_modules($modules); ?>

<script this_script_for_deleter>

    var $this_form = $('[this_script_for_deleter]:first');

    if (confirm('Удалить данный модуль: <?php isset_echo($title); ?>?'))
    {
        
        var form = $this_form.prevAll('.fast_edit_form:first');
        var idTemplate = form.find('[name=idTemplate]').val();
        var index = form.find('[name=__cms_output_index]').val();
        var pidTemplate = form.find('[name=pidTemplate]').val();
        
        if (index)
        {
            mbcms.output.remove(pidTemplate, index);
            mbcms.visual_fast_edit.get_targets(undefined, true).remove();
        }
        else if (pidTemplate != undefined)
        {
            mbcms.template.remove(pidTemplate, idTemplate);
            mbcms.visual_fast_edit.get_targets(undefined, true).remove();
        }

        setTimeout(function ()
        {
            mbcms.visual_fast_edit.destroy();
        }, 0);
    }
    else
    {
        setTimeout(function ()
        {
            mbcms.visual_fast_edit.__set_active();
        }, 0);
    }

    $this_form.remove();
</script>

