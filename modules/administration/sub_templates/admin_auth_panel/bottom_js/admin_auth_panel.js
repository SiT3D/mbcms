/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


new event.site.load().listen(function ()
{
    if (/\?fc=1/.test(location.href))
    {
        location.href = location.origin + location.pathname;
    }


    new event.site.form.success().listen(function ()
    {
        if (this.form_id != 'admin_auth_form')
        {
            return;
        }

        if (this.is_errors())
        {
            this.print_errors();
        }
        else
        {
            location.reload();
        }

    });
});