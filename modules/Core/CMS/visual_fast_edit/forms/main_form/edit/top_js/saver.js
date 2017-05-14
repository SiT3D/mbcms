mbcms.visual_fast_edit.saver = function ()
{

};

mbcms.visual_fast_edit.saver.idTemplate = undefined;

// тут еще надо сохранять не все значения! а только те которые были изменены! дописать функцию получения всех данных в 1 объект для сравнения
mbcms.visual_fast_edit.saver.submit = function (target, form_index)
{
    var self = this;

    target = target == undefined ? $('body') : target;
    form_index = form_index == undefined ? 0 : form_index;

    var form = target.find('form:eq(' + form_index + ')');

    if (form.length > 0)
    {
        if (!form.attr('action'))
        {
            return;
        }

        var data = form.serializeArray();
        data.push({name: 'class', value: form.attr('action').replace('\\', '\\\\')});
        data.push({name: 'media_screen_size', value: mbcms.visual_fast_edit.adaptation.get_size()});
        self.idTemplate = data[0].value;


        mbcms.ajax({
            method: form.attr('method'),
            data: data,
            success: function ()
            {
                mbcms.visual_fast_edit.saver.submit(target, form_index + 1);
            }
        });
    }
    else if (this.idTemplate != undefined)
    {
        mbcms.dinamic_js_css_loader.load(null, this.idTemplate);
    }
};