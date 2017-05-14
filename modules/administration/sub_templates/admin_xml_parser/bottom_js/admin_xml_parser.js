/**
 * Created by user on 09.03.2017.
 */

new event.site.load().listen(function()
{
    $('#load_xml_file_to_parse')
        .find('[type=submit]')
        .click(function ()
        {
            site.messages.factory(site.form.message_name).create().append_content('Начало работы, дождитесь оконачания');
        });

    new event.site.form.success().listen(function()
    {
        if (this.form_id == 'load_xml_file_to_parse')
        {
            if (this.is_errors())
            {
                this.print_errors();
            }
            else
            {
                if (!this.print_upload_errors())
                {
                    site.messages.factory(site.form.message_name).create().append_content('Вакансии были обработаны.');
                }
            }
        }
    });
});