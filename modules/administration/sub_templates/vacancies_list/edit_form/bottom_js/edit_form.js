/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


new event.site.load().listen(function ()
{
    new event.site.form.success().listen(function ()
    {
        if (this.form_id != 'admin_edit_vacancies')
        {
            return;
        }

        if (this.is_errors())
        {
            this.print_errors();
        }
        else
        {
            if (this.action_method == 'edit')
            {
                alert('Сохранено');
            }
            else
            {
                this.redirect();
            }
        }
    });

    if ($('#admin_edit_vacancies').length)
    {
        ck_editor.init($('body'));
    }

    $('.vacancy_full_deleter')
        .click(function()
        {
            if (confirm('Удалить?! Нельзя будет восстановить!'))
            {
                site.ajax({
                    data: {class: 'trud\\admin\\templates\\vacancies_list\\edit_form->ajax_remove', vacancy_id: $(this).attr('vacid')},
                    success: function(msg)
                    {
                        get_req(msg);
                        history.back();
                    }
                });
            }

            return false;
        })
    ;
});