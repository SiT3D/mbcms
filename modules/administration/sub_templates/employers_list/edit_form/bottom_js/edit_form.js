/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


new event.site.load().listen(function ()
{
    new event.site.form.success().listen(function ()
    {
        if (this.form_id != 'admin_employers_edit')
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

    $('#delete_employer')
        .click(function()
        {
            if (confirm('Удалить этого пользоваетля и все связанные с ним элементы навсегда?!'))
            {
                site.ajax(
                    {
                        data: {class : 'trud\\admin\\templates\\employers_list\\edit_form->remove_user', user_id: $(this).attr('uid')},
                        success: function(msg)
                        {
                            get_req(msg);
                            history.back();
                        }
                    }
                );
            }
        })
        .css({marginTop: '20px'})
    ;
});