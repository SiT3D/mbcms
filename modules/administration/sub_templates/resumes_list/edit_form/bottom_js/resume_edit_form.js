/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

new event.site.load().listen(function ()
{
    new event.site.form.success().listen(function ()
    {
        if (this.form_id != 'admin_resumes_edit')
        {
            return;
        }

        if (this.is_errors())
        {
            this.print_errors();
        }
        else
        {
            if (this.action == 'trud\\admin\\templates\\resumes_list\\edit_form->edit')
            {
                alert('Сохранено');
            }
            else
            {
                this.redirect();
            }
        }
    });

    $('.resume_full_deleter')
        .click(function()
        {
            if (confirm('Удалить?! Нельзя будет восстановить!'))
            {
                site.ajax({
                    data: {class: 'trud\\admin\\templates\\resumes_list\\edit_form->ajax_remove', resume_id: $(this).attr('resid')},
                    success: function(msg)
                    {
                        get_req(msg);
                        window.history.go(-1);
                    }
                });
            }

            return false;
        })
    ;

});

    