/**
 * Created by sit3d on 04.03.2017.
 */


new event.site.load().listen(function ()
{
    if (!isset(site, 'configuration', 'is_static_templates'))
    {
        return;
    }


    if (site.configuration.is_static_templates == 'live' && site.configuration.is_admin)
    {
        $(document)
            .keydown(function (e)
            {
                if (e.keyCode == KEY_F5 && !e.ctrlKey)
                {
                    site.messages.factory('reload_and_generate_template').create().append_content('Обновляю страницу').locked(true);

                    site.ajax(
                        {
                            data: {
                                class: 'MBCMS\\template->autogenerate_static',
                                idTemplate: site.configuration.idTemplate,
                            },
                            success: function ()
                            {
                                __delay(this, 'lulgeneratecontentreloaded', 200, function()
                                {
                                    location.reload();
                                });
                            }
                        }
                    );
                    return false;
                }
                else if (e.keyCode == KEY_F5 && e.ctrlKey)
                {
                    site.messages.factory('reload_and_generate_template').create().append_content('Переход к тестовому виду').locked(true);

                    site.ajax(
                        {
                            data: {
                                class: 'MBCMS\\template->destroy_static_view',
                                idTemplate: site.configuration.idTemplate,
                            },
                            success: function ()
                            {
                                __delay(this, 'lulgeneratecontentreloaded', 200, function()
                                {
                                    location.reload();
                                });
                            }
                        }
                    );
                    return false;
                }
            })
        ;
    }
});