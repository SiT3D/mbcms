/**
 * Created by sit3d on 04.03.2017.
 */


new event.site.load().listen(function ()
{
    if (!isset(site, 'configuration', 'is_static_templates'))
    {
        return;
    }


    if (site.configuration.is_static_templates == 'live')
    {
        $(document)
            .keydown(function (e)
            {
                if (e.keyCode == KEY_F5 && !e.ctrlKey)
                {
                    site.ajax(
                        {
                            data: {
                                class: 'MBCMS\\template->autogenerate_static',
                                idTemplate: site.configuration.idTemplate,
                            },
                            success: function ()
                            {
                                location.reload();
                            }
                        }
                    );
                    return false;
                }
                else if (e.keyCode == KEY_F5 && e.ctrlKey)
                {
                    site.ajax(
                        {
                            data: {
                                class: 'MBCMS\\template->destroy_static_view',
                                idTemplate: site.configuration.idTemplate,
                            },
                            success: function ()
                            {
                                location.reload();
                            }
                        }
                    );
                    return false;
                }
            })
        ;
    }
});