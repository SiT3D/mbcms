
/**
 * 
 * @param {type} e // jquery click info
 * @param {$} $trg
 * @returns {undefined}
 */
mbcms.visual_fast_edit.range_modules = function (e, $trg)
{
    // тут поиск и вывод селекта с датой и прочим
    var getvariants = function (mouseX, mouseY)
    {
        var variants = [];

        var elements = mbcms.visual_fast_edit.register_coords.get_elements(mouseX, mouseY);

        for (var i in elements)
        {
            var key = elements[i];
            var alias = elements[i].attr('template_title') || elements[i].attr('__user_cms_out_title') || elements[i].attr('fast_edit_class');

            if (elements[i].attr('idTemplate'))
            {
                alias = '<span style="color:orange;">' + alias + '</span>';
            }
            else
            {
                alias = '<span style="color:#88f;">' + alias + '</span>';
            }

            variants.unshift([key, alias]);
        }

        return variants;
    };


    var st = $('body').scrollTop();
    var sl = $('body').scrollLeft();

    var variants = getvariants(e.clientX + sl, e.clientY + st);

    if (!e.altKey)
    {
        mbcms.visual_fast_edit.create(option.get_fast_edit_data($trg));
    }
    else
    {
        mbcms.visual_fast_edit.destroy();
        $('.plugin_select-body').remove();
        var select = new mbcms.visual_fast_edit.plugin_select();

        select.dop(
                {
                    hover: function (value)
                    {
                        var data = option.get_fast_edit_data(value[0]);
                        mbcms.visual_fast_edit.create_global_axis(data);
                    },
                    unhover: function (value)
                    {
                        var data = option.get_fast_edit_data(value[0]);
                        mbcms.visual_fast_edit.remove_global_axis(data);
                    }
                });

        select
                .create(variants, {clientX: e.clientX, clientY: e.clientY}, function (v)
                {
                    if (v[0] !== 'null')
                    {
                        mbcms.visual_fast_edit.create(option.get_fast_edit_data(v[0]));
                    }
                }, $trg);
    }

};

