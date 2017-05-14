
option.standart_template_settings = function ()
{

};

option.standart_template_settings.init_window = function (window)
{
    option.init_option_block(window.$, window);
//    // инит кнопок
    this.__init_generate_button(window);
    this.__init_clear_button(window);
    this.__init_load_styles_at_class(window);
    option.standart_template_settings.__load_styles(window);
};

option.standart_template_settings.__load_styles = function (window)
{
    option.standart_template_settings.__clear(window);
    $.ajax(
            {
                url: '/ajax',
                data:
                        {
                            class: 'MBCMS\\template->load_styles',
                            current_class: option.standart_template_settings.__get_current_class(window),
                            idTemplate: window.$.find('[idtemplate]:first').attr('idtemplate')
                        },
                success: function (msg)
                {
                    var req = get_req(msg);

                    if (isset(req, 'options'))
                        for (var i in req['options'])
                        {
                            var key = i;
                            var value = req['options'][i];
                            var $opt = window.$.find('.settings-editor-info-module[key="' + key + '"]');
                            if ($opt.attr('mtype') === 'mselect')
                            {
                                var nv = value;
                                value = {};
                                value[0] = {}; // тут еще разбивать еси мульти пришел?? То тут эксплодом или имплодом надо делать массив
                                value[0].key = nv;
                                value[0].name = nv;
                            }
                            else if ($opt.attr('mtype') === 'mimage')
                            {
                                var nv = value;
                                value = {};
                                value[0] = {}; // тут еще разбивать еси мульти пришел?? То тут эксплодом или имплодом надо делать массив
                                value[0].key = Options__mimage.removeURL(nv);
                                value[0].name = nv;
                            }

                            $opt.optionValue(value);
                            option.event($opt, window);
                        }

                    var $opt = window.$.find('.settings-editor-info-module[key="styles"]');
                    if (isset(req, 'options'))
                        if (req['styles'] !== '' && req['styles'] !== null)
                            $opt.optionValue(req['styles']);

                    option.event($opt, window);
                }
            });
};

option.standart_template_settings.__init_load_styles_at_class = function (window)
{
    window.$
            .find('.settings-editor-info-module[key=class][option_group=CSS]:first')
            .change(function ()
            {
                option.standart_template_settings.__load_styles(window);
            })
            ;
};

option.standart_template_settings.__get_current_class = function (window)
{
    return window.$
            .find('.settings-editor-info-module[key=class][option_group=CSS]:first')
            .find('select option:selected')
            .attr('key')
            ;
};

option.standart_template_settings.__init_clear_button = function (window)
{
    window.$
            .find('#clear_styles')
            .click(function ()
            {
                option.standart_template_settings.__clear(window);
                return false;
            })
            ;
};

option.standart_template_settings.__remove_module_attrs = function (window)
{
    mbcms.controll_window.get()
            .find('[idtemplate=' + window.$.find('[idtemplate]').attr('idtemplate') + ']')
            .removeAttr('style')
            ;
};

option.standart_template_settings.__clear = function (window)
{
    option.standart_template_settings.__remove_module_attrs(window);

    window.$
            .find('#MBCMS_STANDART_SETTINGS_EDITOR_MODULE .settings-editor-info-module')
            .each(function ()
            {
                if ($(this).attr('is_style') === '1')
                {
                    if ($(this).attr('mtype') === 'mselect' || $(this).attr('mtype') === 'mimage')
                    {

                        $(this).optionValue({0: {key: 'null'}});
                    }
                    else
                    {
                        $(this).optionValue('');
                    }
                }
            });
};


/**
 * 
 * @param {type} idTemplate
 * @param {type} parent_class
 * @param {type} current_class
 * @param {type} options_data
 * @param {type} callback
 * @param {type} options_data_old
 * @returns {undefined}
 */
option.standart_template_settings.update_styles = function (idTemplate, parent_class, current_class, options_data, callback, options_data_old)
{
    var media_screen_size = mbcms.visual_fast_edit.adaptation.get_size();

    for (var i in options_data_old)
    {
        if (isset(options_data, i) && isset(options_data_old, i))
            if (options_data[i] == options_data_old[i])
            {
                delete options_data[i];
            }
    }
    
    
    if (!$.isEmptyObject(options_data))
        mbcms.ajax(
                {
                    data:
                            {
                                class: 'MBCMS\\template->update_styles',
                                idTemplate: idTemplate,
                                parent_class: parent_class,
                                current_class: current_class,
                                options_data: options_data,
                                media_screen_size: media_screen_size
                            },
                    success: function ()
                    {
                        mbcms.dinamic_js_css_loader.load('', idTemplate);
                        if (typeof callback === 'function')
                            callback();
                    },
                    sync: true
                });
};