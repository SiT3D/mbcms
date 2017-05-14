/**
 * Created by user on 22.02.2017.
 */

mbcms.cloner = function ()
{

};

/**
 * Отправка на сервер!
 */
mbcms.cloner.clone = function ()
{

};

mbcms.cloner.__remove_clones = function ()
{
    $('.mbcms_visual_fast_edit-opacity_clone').remove();
};

mbcms.cloner.__create_opacity_clones = function (count)
{
    this.__remove_clones();

    count = isNaN(parseInt(count)) ? 0 : parseInt(count);
    count = count < 0 ? 0 : count;

    var current_element = mbcms.visual_fast_edit.get_targets(null, true);

    for (var i = 0; i < count; i++)
    {
        var clone = current_element.clone(true);
        clone
            .css({opacity: 0.4})
            .addClass('mbcms_visual_fast_edit-opacity_clone')
        ;

        current_element.parent().append(clone);
    }
};


mbcms.cloner.__add_in_struct = function (count)
{
    var data = mbcms.visual_fast_edit.get_current_data();

    count = isNaN(parseInt(count)) ? 0 : count;

    if (data.connect_type == '__cms_connect_type_OUTPUT')
    {
        mbcms.ajax
        (
            {
                data:
                    {
                        class: 'MBCMS\\output->add_more',
                        idTemplate: data.idTemplate,
                        out_class: data.fast_edit_class,
                        data: data.out_index,
                        count: count
                    },
                success: function ()
                {
                    mbcms.controll_window.load();
                    mbcms.template.reload_views(data.idTemplate);
                }
            }
        );
    }
    else if (data.connect_type == '__cms_connect_type_TEMPLATE')
    {
        var parentIdTemplate = data.this.parents('[idtemplate]:first').attr('idtemplate');

        mbcms.ajax(
            {
                data:
                    {
                        class: 'MBCMS\\template->tclone',
                        parent_idTemplate: parentIdTemplate,
                        children_idTemplate: data.idTemplate,
                        count: count
                    },
                success: function (req)
                {
                    req = $.parseJSON(req);

                    if (isset(req, 'new_idTemplate'))
                    {
                        for (var i in req.new_idTemplate)
                        {
                            mbcms.dinamic_js_css_loader.load('', req.new_idTemplate[i]);
                        }
                    }
                    mbcms.controll_window.load();
                }
            });
    }


};

new event.site.load().listen(function ()
{
    new event.visual_fast_edit.init().listen(function ()
    {
        var form = this.jq_container;

        new event.visual_fast_edit.main_option.go().listen(function ()
        {
            var count = form.find('.cloner_counter').val();
            mbcms.cloner.__create_opacity_clones(count);
        });
    });


    new event.visual_fast_edit.close_form().listen(function ()
    {
        var count = this.form.find('.cloner_counter').val();
        if (count)
        {
            mbcms.cloner.__add_in_struct(count);
        }

        mbcms.cloner.__remove_clones();
    });
});
