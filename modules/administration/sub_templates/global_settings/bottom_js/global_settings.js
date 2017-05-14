/**
 * Created by user on 04.05.2017.
 */

new event.site.load().listen(function()
{
    new event.site.form.success().listen(function ()
    {
        if (this.form_id == 'global_settings_form')
        {
            location.reload();
        }
    });

});