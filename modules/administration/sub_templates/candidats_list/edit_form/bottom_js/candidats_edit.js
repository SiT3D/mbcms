/**
 * Created by user on 24.02.2017.
 */


new event.site.load().listen(function()
{
    new event.site.form.success().listen(function()
    {
        if (this.form_id != 'candidate_admin_edit')
        {
            return;
        }

        if (this.is_errors())
        {
            this.print_errors();
        }
        else
        {
            if (this.action_method == 'add')
            {
                this.redirect();
            }
            else
            {
                alert('Сохранено');
            }
        }
        
    });

    $('#remove_candidat')
        .click(function ()
        {
            if (confirm('Удалить соискателя и все резюме безвозвратно?!'))
            {
                site.ajax(
                    {
                        data: {class: 'trud\\admin\\templates\\candidats\\edit_form->remove_user', uid: $(this).attr('uid')},
                        success: function(msg)
                        {
                            get_req(msg);
                            history.back();
                        }
                    }
                );
            }
            return false;
        })
        .css({marginTop: '20px'})
    ;
});