/**
 * Created by user on 16.03.2017.
 */


new event.site.load().listen(function()
{
    new event.site.form.success().listen(function()
    {

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

        if (this.form_id == 'admin_partners_form' || this.form_id == 'admin_partners_remove_form')
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
});