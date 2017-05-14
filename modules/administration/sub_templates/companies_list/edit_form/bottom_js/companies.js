/**
 * Created by user on 24.02.2017.
 */

new event.site.load().listen(function ()
{
    new event.site.form.success().listen(function ()
    {
        if (this.form_id == 'mc_company_save_data')
        {

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
        }

        if (this.form_id == 'image_uplouder')
        {
            if (this.is_errors())
            {
                this.print_errors();
            }
            else
            {
                location.reload();
            }
        }
    });

    if ($('#mc_company_save_data').length)
    {
        ck_editor.init($('body'));
    }

});