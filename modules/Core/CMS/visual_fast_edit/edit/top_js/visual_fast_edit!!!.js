event.visual_fast_edit = function ()
{
    this.__key = 'event.visual_fast_edit';
};
event.visual_fast_edit.prototype = Object.create(event.prototype);


mbcms.visual_fast_edit = function ()
{

};


mbcms.visual_fast_edit.__icos = [];
mbcms.visual_fast_edit.__list = [];
mbcms.visual_fast_edit.__forms = {};
mbcms.visual_fast_edit.resolution = true;

mbcms.visual_fast_edit.__create_icons_plg = function (ico_css)
{
    if (typeof this.__list !== 'undefined')
    {
        var obj = {};
        for (var i in this.__list)
        {
            obj[this.__list[i]] = this.__list[i];
        }
        this.__list = obj;
    }

    for (var option in obj)
    {
        if (isset(this, '__list', option))
        {
            if (isset(this.__forms, option))
            {
                var settings = this.__forms[option];

                var ico = $('<div />')
                        .addClass('mbcms_visual_fast_edit_ico ' + settings.css)
                        .css(ico_css)
                        .attr('title', settings.css)
                        .click(function ()
                        {
                            mbcms.visual_fast_edit.__set_active($(this));

                            return false;
                        })
                        .appendTo(this.__panel)
                        .data(settings)
                    ;

                this.__icos.push(ico);
            }
        }
    }
};

/**
 * Убирает двойные пробелы, заменяя их одинарными
 *
 * @param {type} string
 */
mbcms.visual_fast_edit.__trim = function (string)
{
    if (/\s\s/.test(string))
    {
        string = string.replace('  ', ' ');
        return this.__trim(string);
    }

    return $.trim(string);
};

/**
 *
 * @param {type} $block
 * @param {type} $target
 * @returns {undefined}
 */
mbcms.visual_fast_edit.append_new_visual_block = function ($block, $target)
{
    var templates = this.get_templates($target);

    if (templates.length > 0 && $block.attr('connect_type') != '__cms_connect_type_TEMPLATE')
    {
        $(templates[0]).before($block);
    }
    else
    {
        $block.appendTo($target);
    }
};

/**
 *  * IN THE DOM!!!
 * @param {$} parent // __fast_edit_data.parent
 * @param {string} css_class // not dot!! // out_text
 */
mbcms.visual_fast_edit.get_outs = function (parent, css_class)
{
    css_class = this.remove_prepars_string(css_class);
    css_class = typeof css_class !== 'undefined' ? css_class : '';
    css_class = this.__trim(css_class);
    return parent.find('[css_class=' + css_class + ']' + '[parentidtemplate='
        + parent.attr('idtemplate') + '][connect_type=__cms_connect_type_OUTPUT]');
};

mbcms.visual_fast_edit.remove_prepars_string = function (string)
{
    if (string == undefined)
    {
        return string;
    }

    //noinspection
    string = string.replace(/\{\{.*\}\}/, '');
    string = string.replace('{', '');
    string = string.replace('}', '');
    return string;
};


/**
 *  * IN THE DOM!!!
 *
 * @param {$} parent // __fast_edit_data.parent
 * @param {string} css_class // not dot!! // out_text
 */
mbcms.visual_fast_edit.get_addm = function (parent, css_class)
{
    css_class = this.remove_prepars_string(css_class);
    css_class = typeof css_class !== 'undefined' ? css_class : '';
    css_class = this.__trim(css_class);
    css_class = $.trim(css_class) !== '' ? '.' + css_class.split(' ').join('.') : '';
    return parent.find(css_class + '[parentidtemplate=' + parent.attr('idtemplate') + '][connect_type=__cms_connect_type_PROGRAMM_ADDM]');
};

/**
 * IN THE DOM!!!
 *
 *
 * @param {$} parent // __fast_edit_data.parent
 * @param {int|string} idtemplate
 */
mbcms.visual_fast_edit.get_templates = function (parent, idtemplate)
{
    idtemplate = typeof idtemplate !== 'undefined' ? '[idtemplate=' + idtemplate + ']' : '';
    return parent.find(idtemplate + '[parentidtemplate=' + parent.attr('idtemplate') + '][connect_type=__cms_connect_type_TEMPLATE]');
};

/**
 *
 * @param {data} data //__fast_edit_data
 * @param {bool} current
 * @returns {undefined}
 */
mbcms.visual_fast_edit.get_targets = function (data, current)
{
    data = data || this.get_current_data();

    //noinspection JSValidateTypes
    if (data == 'undefined' || !data)
    {
        return;
    }

    if (current && typeof current !== 'undefined')
    {
        return data.this;
    }

    if (data.this.attr('connect_type') == '__cms_connect_type_OUTPUT')
    {
        if ($.trim(data.current_class) === '')
            return data.this;
        return this.get_outs(data.parent, data.current_class);
    }
    else if (data.this.attr('connect_type') == '__cms_connect_type_TEMPLATE')
    {
        if (data.this.parent().prop('id') == 'MBCMS_CONTROLL_TAB_WINDOW')
            return data.this;

        return this.get_templates(data.parent, data.this.attr('idtemplate'));
    }
    else if (data.this.attr('connect_type') == '__cms_connect_type_PROGRAMM_ADDM' && typeof data.primary_key !== 'undefined')
    {
        if ($.trim(data.current_class) === '')
            return data.this;
        return this.get_addm(data.parent, data.current_class);
    }
};

mbcms.visual_fast_edit.get_parentidtemplate_childrens = function (data, dop_selector)
{
    dop_selector = dop_selector == undefined ? '' : dop_selector;
    return data.this.find('[parentidtemplate=' + data.idTemplate + ']' + dop_selector);
};

mbcms.visual_fast_edit.get_current_data = function ()
{
    return this.__current_data;
};

mbcms.visual_fast_edit.get_panel = function ()
{
    return mbcms.visual_fast_edit.__panel;
};

/**
 *
 * @param {type} data // fast_edit_data
 * @param {type} callback
 * @param {type} not_ctrl если not_ctrl = true то не учитывать нажатый контрл
 * @returns {undefined}
 */
mbcms.visual_fast_edit.create = function (data, callback, not_ctrl)
{
    if (IS_CTRL && not_ctrl != true)
    {
        // выбирать шаблон!
        if (!data.this.attr('idtemplate'))
        {
            mbcms.visual_fast_edit.create(option.get_fast_edit_data(data.this.parents('[idtemplate]:first')));
            return;
        }
    }


    if (!data.__active && this.resolution)
    {

        var $trg = data.this;
        if (data.__produc_hidden == 'true')
        {
            this.__fade($trg, true);
        }

        data = option.get_fast_edit_data($trg.length > 0 ? $trg : $(data.unical_selector));
        this.__load_data(data, callback);
    }
};

mbcms.visual_fast_edit.__fade = function (targets, show, time)
{
    if (show)
    {
        targets.show().css({zIndex: 4});
    }
    else
    {

        targets.each(function ()
        {
            $(this).find('#hide_this').remove();
            $('<div id="hide_this" />')
                .css({
                    display: 'block',
                    position: 'absolute',
                    right: -10,
                    top: -10,
                    width: 15,
                    height: 15,
                    background: 'red',
                    border: '1px solid orange',
                    borderRadius: '50%',
                    textAlign: 'center',
                    lineHeight: '15px',
                    cursor: 'pointer',
                })
                .text('X')
                .click(function ()
                {
                    $(this).parent().hide();
                    return false;
                })
                .appendTo($(this))
            ;
        });

        setTimeout(function ()
        {
            targets.each(function ()
            {
                if ($(this).find('.mbcms_fly_element-fly').length == 0 && !$(this).hasClass('mbcms_fly_element-fly'))
                {
                    $(this).hide();
                }
            });
        }, time);
    }
};

mbcms.visual_fast_edit.__load_data = function (data, callback)
{
    this.__options_data = {};
    var self = this;
    var list = data.list;
    data.media_screen_size = mbcms.visual_fast_edit.adaptation.get_size();

    this.load_options(data, function (msg)
    {
        self.__current_data = data;

        self.__options_data = get_req(msg);

        self.destroy();
        mbcms.controll_window.get().stop(true).animate({paddingBottom: 500}, 'slow');

        self.__list = list;
        self.__fast_edit_data = data;
        self.__create_panel();
        self.__create_title(data.__user_cms_out_title || data.template_title, data);
        self.__create_icons();
        var w = self.__panel.children().length * 45;
        self.__panel.css({
            width: w,
            marginLeft: w / -2
        });
        self.create_global_axis(data);

        data.__active = true;

        self.__panel.appendTo('body');

        if (IS_CTRL)
        {
            mbcms.visual_fast_edit.__set_active($('.mbcms_visual_fast_edit_ico.ico.add_btn:first:NOT(.active)'), true);
        }

        if (is_callable(callback))
            callback.call(callback);
    });
};

/**
 *
 * @param {type} data
 * @param {type} callback /////// function(msg) {data = $.parseJSON(msg);}
 * @returns {undefined}
 */
mbcms.visual_fast_edit.load_options = function (data, callback)
{
    var media_screen_size = mbcms.visual_fast_edit.adaptation.get_size();
    mbcms.ajax(
        {

            data: {
                class: 'MBCMS\\template->load_styles',
                idTemplate: data.idTemplate,
                current_class: data.current_class,
                media_screen_size: media_screen_size,
                mini: true
            },
            success: function (msg)
            {
                if (typeof callback == 'function')
                {
                    callback.call(callback, msg);
                }
            }
        });

};

/**
 *
 * @param {type} data
 * @param {type} key
 * @param take_jquery_css
 * @returns
 */
mbcms.visual_fast_edit.get_options = function (data, key, take_jquery_css)
{
    take_jquery_css = take_jquery_css == undefined ? true : take_jquery_css;

    if (typeof data === 'undefined' || typeof key === 'undefined')
    {
        return isset(this, '__options_data') ? this.__options_data : {};
    }

    if (!isset(data['CMSData']))
    {
        var dataR = this.__options_data;
    }
    else
    {
        dataR = data;
        take_jquery_css = false;
    }


    key = key.replace(/-/g, '_');
    if (isset(dataR, 'styles', key))
    {
        return dataR['styles'][key];
    }
    else if (isset(dataR, 'CMSData', key))
    {
        return dataR['CMSData'][key];
    }
    else if (take_jquery_css)
    {
        return this.get_targets(data, true).css(key.replace(/_/g, '-'));
    }

};

/**
 *
 * @param {type} data
 * @param {type} out_index
 * @returns
 */
mbcms.visual_fast_edit.get_output = function (data, out_index)
{
    var outputs = this.get_options(data, 'outputs');
    return isset(outputs, out_index) ? outputs[out_index] : outputs;
};

/**
 *
 * @param {type} data
 * @param {type} out_index
 * @param {type} property
 */
mbcms.visual_fast_edit.get_output_data = function (data, out_index, property)
{
    var output = this.get_output(data, out_index);

    if (isset(output, 'data', property))
        return output.data[property];

    return isset(output, 'data') ? output.data : {};
};

mbcms.visual_fast_edit.__create_global_shadow = function (data)
{
    data.this.addClass('mbcms_fly_element-fly');
};

mbcms.visual_fast_edit.__remove_global_shadow = function (data)
{
    data.this.removeClass('mbcms_fly_element-fly');
};

/**
 *
 * @param {type} data // fast_edit_data
 * @param one
 * @returns {undefined}
 */
mbcms.visual_fast_edit.create_global_axis = function (data, one)
{
    var trg = this.get_targets(data, one);
    if (isset(trg, 'each'))
    {
        trg.each(function ()
        {
            if ($(this).css('position') === 'static')
                $(this).css('position', 'relative');

            $('<div />').addClass('mbcms_global_axis_for-item left').appendTo($(this));
            $('<div />').addClass('mbcms_global_axis_for-item right').appendTo($(this));
            $('<div />').addClass('mbcms_global_axis_for-item top').appendTo($(this));
            $('<div />').addClass('mbcms_global_axis_for-item bottom').appendTo($(this));
        });
    }


    this.__create_global_shadow(data); //!!SHD
};

/**
 *
 * @param {type} data
 * @returns {undefined}
 */
mbcms.visual_fast_edit.remove_global_axis = function (data)
{
    var trg = this.get_targets(data);
    this.__remove_global_shadow(data); //!!SHD
    if (isset(trg, 'children'))
        trg.children('.mbcms_global_axis_for-item').remove();
};

mbcms.visual_fast_edit.is_panel = function ()
{
    return $('#mbcms_visual_fast_edit-panel').length > 0;
};

mbcms.visual_fast_edit.__create_title = function (name)
{
    //noinspection JSValidateTypes
    if (typeof this.__title != undefined && isset(this, '__title', 'remove'))
    {
        this.__title.remove();
    }

    this.__title = $('<div />')
        .css(
            {
                position: 'fixed',
                bottom: 1,
                width: '100%',
                height: 25,
                background: '#000',
                fontSize: '20px',
                color: 'orange',
                textAlign: 'center',
                lineHeight: '25px',
                cursor: 'pointer',
            })
        .appendTo('body')
        .attr('title', 'Нажмите чтобы отобразилась структура страницы')
        .text(name)
        .click(function ()
        {
            mbcms.visual_fast_edit.__create_structure_list();

            return false;
        })
    ;
};

mbcms.visual_fast_edit.__create_structure_list = function ()
{

    var body = $('<div />');

    mbcms.controll_window.get()
        .find('[connect_type]')
        .each(function ()
        {
            var text = '';
            var cc = $(this).parents('[fast_edit_class]').length;
            var level_text = '';

            var self = $(this);
            text += level_text + ($(this).attr('__user_cms_out_title') || $(this).attr('template_title')) + ' ( ' + $(this).attr('class') + ' )';
            text += ' --> ' + $(this).attr('echo_module_class');

            var item = $('<div />')
                    .html(text)
                    .appendTo(body)
                    .attr({
                        __cms_output_index: self.attr('__cms_output_index'),
                        idtemplate: self.attr('idtemplate'),
                        parentidtemplate: self.attr('parentidtemplate'),
                        __my_level: cc,
                    })
                    .click(function ()
                    {
                        var data = option.get_fast_edit_data(self);
                        mbcms.visual_fast_edit.create(data);
                        $('.hoverme').removeClass('hoverme');
                        $(this).addClass('hoverme');
                        return false;
                    })
                    .dblclick(function ()
                    {
                        site.messages.factory('visual_fast_edit_structure_page').remove();
                    })
                    .css({
                        cursor: 'pointer',
                        marginLeft: cc * 5
                    })
                ;

            var parent_index = self.attr('__user_cms_parent_output_index');
            if (parent_index && parent_index != 'destroy' && parent_index != undefined)
            {
                item.attr({__user_cms_parent_output_index: self.attr('__user_cms_parent_output_index')});
            }
        })
    ;

    $('<button />')
        .text('Сохранить структуру')
        .click(function ()
        {
            var content = site.messages.factory('visual_fast_edit_structure_page').get_content();

            content
                .find('[idtemplate]')
                .each(function ()
                {
                    var $this_template = $(this);

                    mbcms.output.update_array($(this).attr('idtemplate'), mbcms.visual_fast_edit.__get_new_positions_data(content), function ()
                    {
                        var indexis = [];

                        content
                            .find('[parentidtemplate=' + $this_template.attr('idtemplate') + ']')
                            .each(function ()
                            {
                                indexis.push($(this).attr('__cms_output_index'));
                            });

                        mbcms.output.resort($this_template.attr('idtemplate'), indexis, function ()
                        {
                            __delay(self, 'selfpregenerated', 500, function ()
                            {
                                mbcms.template.autogenerate_static($('[idtemplate]:first').attr('idtemplate'), function ()
                                {
                                    location.reload();
                                });
                            });
                        });
                    });
                })
            ;

            return false;
        })
        .css({
            marginTop: '20px',
            cursor: 'pointer'
        })
        .appendTo(body)
    ;

    site.messages.factory('visual_fast_edit_structure_page').create().append_content(body);


    body
        .find('[__my_level]')
        .each(function ()
        {
            var parent_index = $(this).attr('__user_cms_parent_output_index');
            $(this).appendTo(body.find('[__cms_output_index="' + parent_index + '"]'));
        });

};

mbcms.visual_fast_edit.__get_new_positions_data = function (content)
{
    var data = {};

    content
        .find('[__cms_output_index]')
        .each(function ()
        {
            var index = $(this).attr('__cms_output_index');
            data[index] = {__user_cms_parent_output_index: $(this).attr('__user_cms_parent_output_index') || ''};
        })
    ;

    return data;
};

mbcms.visual_fast_edit.__create_panel = function ()
{
    var self = this;

    self.__panel = $('<div />')
        .prop('id', 'mbcms_visual_fast_edit-panel')
        .css(
            {
                position: 'fixed',
                width: 600,
                height: 45,
                left: '50%',
                bottom: '35px',
                margin: '0 0 0 -300px',
                borderRadius: '5px',
                border: '1px solid #888',
                boxShadow: '0 -2px 10px rgba(0,0,0,0.4) inset',
                zIndex: 10,
                textAlign: 'center'
            })
        .addClass('pixel_gray_50 mbcms_visual_fast_edit_panel')
    ;

    $('<button />')
        .appendTo(self.__panel)
        .text('close')
        .click(function ()
        {
            self.destroy();
        })
        .css({
            position: 'absolute',
            right: -100,
            top: 5
        })
        .addClass('mbcms-bootstrap btn')
    ;


};

mbcms.visual_fast_edit.__create_icons = function ()
{
    var ico_css =
        {
            width: 14,
            height: 14,
            borderRadius: 4,
            border: '1px solid #fff',
            padding: '10px',
            margin: '6px 3px',
            display: 'inline-block',
            backgroundSize: '26px'
        };

    this.__create_icons_plg(ico_css);
};

mbcms.visual_fast_edit.destroy = function ()
{
    var data = this.__fast_edit_data;
    if (isset(data, '__produc_hidden'))
    {
        if (data.__produc_hidden == 'true')
        {
            this.__fade(data.this, false, 10000);
        }
    }


    this.__destroy_icos(function ()
    {
        if (isset(this, '__panel', 'remove'))
        {
            mbcms.template.autogenerate_static();
            this.remove_global_axis(this.__fast_edit_data);
            this.__panel.remove();
            mbcms.visual_fast_edit.__create_title('Структура');
            this.__panel = undefined;
            this.__fast_edit_data.__active = false;
            mbcms.controll_window.get().stop(true).delay(2000).animate({paddingBottom: 0}, 'slow');
        }
    });


    this.__icos = [];
};

mbcms.visual_fast_edit.__show_hide__and_save_form = function (ico, show)
{
    if (!show)
    {
        var form = ico.data('form');

        ico.removeClass('active');
        if (isset(ico.data('form'), 'hide'))
        {
            ico.data('form').remove();
        }
        mbcms.visual_fast_edit.saver.submit(form);

        mbcms.visual_fast_edit.fly_edit.unselect_trg();
        var evt = new event.visual_fast_edit.close_form().call();
        evt.form = form;
    }
    else
    {
        this.load_form(ico);
        ico.addClass('active');
        ico.data('form').show();
    }
};

mbcms.visual_fast_edit.__destroy_ico = function (ico)
{
    if (ico.hasClass('active'))
    {
        this.__show_hide__and_save_form(ico, false);
    }
};

mbcms.visual_fast_edit.__destroy_icos = function (callback)
{
    for (var i in this.__icos)
    {
        this.__destroy_ico(this.__icos[i]);
    }

    if (is_callable(callback))
    {
        callback.call(this);
    }
};

/**
 *
 * @param {type} ico jquery
 * @param {type} set_active
 * @returns {undefined}
 */
mbcms.visual_fast_edit.__set_active = function (ico, set_active)
{
    ico = ico || $('.mbcms_visual_fast_edit_ico.active');

    if (ico.hasClass('active') || set_active == false)
    {
        this.__show_hide__and_save_form(ico, false);
        return;
    }

    for (var i in this.__icos)
    {
        if (this.__icos[i].hasClass('active'))
        {
            this.__show_hide__and_save_form(this.__icos[i], false);
        }
    }

    if (typeof ico.data('first_create') == 'undefined' && ico.data('class') != undefined)
    {
        this.load_form(ico);
        ico.data('first_create', true);
    }
    else if (ico.data('first_create') != undefined)
    {
        this.__show_hide__and_save_form(ico, true);
    }

    ico.addClass('active');

};

/**
 *
 * @param {type} ico
 * @returns {undefined}
 */
mbcms.visual_fast_edit.load_form = function (ico)
{
    var data = mbcms.visual_fast_edit.get_current_data();

    mbcms.ajax({
        method: 'GET',
        data: {
            class: ico.data('class') + '->' + ico.data('method'),
            idTemplate: data.idTemplate,
            pidTemplate: data.parentidtemplate,
            current_class: data.current_class,
            parent_class: data.parent_class,
            __cms_output_index: data.__cms_output_index,
            media_screen_size: mbcms.visual_fast_edit.adaptation.get_size(),
            __mini: 'true',
        },
        success: function (msg)
        {
            var jquery = $(msg);
            $('body').append(jquery);
            ico.data('form', jquery);

            var cp = new mbcms.visual_fast_edit.fly_edit(jquery);
            cp.init();
        }
    });
};

mbcms.visual_fast_edit.rgb_to_hex = function (val)
{
    var rgb = {};
    val.replace(/(\d*)[,)]+/g, function (a, b)
    {
        var nval = parseInt(b).toString(16);
        nval = nval.length === 1 ? nval + nval : nval;

        if (!isset(rgb, 'r'))
            rgb.r = nval;
        else if (!isset(rgb, 'g'))
            rgb.g = nval;
        else if (!isset(rgb, 'b'))
            rgb.b = nval;
    });

    var color = '#';
    for (var i in rgb)
    {
        color += rgb[i];
    }

    return color;
};

mbcms.visual_fast_edit.activate_blocks_gradient = function (active)
{
    var jq = $('.__active_visual_blocks_gradient');

    if (jq.length == 0 || active == true)
    {
        mbcms.controll_window.get()
            .find('[connect_type]:NOT(.__active_visual_blocks_gradient)')
            .addClass('__active_visual_blocks_gradient')
        ;
    }
    else if (active == false || jq.length != 0)
    {
        mbcms.controll_window.get()
            .find('[connect_type].__active_visual_blocks_gradient')
            .removeClass('__active_visual_blocks_gradient')
        ;
    }
};

mbcms.visual_fast_edit.fast_list = function ($module, data, list)
{
    option.register_fast_edit_list(data.fast_edit_class, list);
    this.init_activator($module);
};

mbcms.visual_fast_edit.init_activator = function ($module)
{
    if ($module.parents('[fixed_padlock=fixed]').length == 0)
    {
        $module
            .click(function (e)
            {
                mbcms.visual_fast_edit.range_modules(e, $(this));
                e.stopPropagation();
            })
        ;
    }

};

/**
 *
 * @param {type} option_class MBCMS\out
 * @param {type} css_class ico
 * @param {type} method  "get_form" => MBCMS\out->get_form
 * @returns {undefined}
 */
mbcms.visual_fast_edit.register_forms = function (option_class, css_class, method)
{
    this.__forms[option_class] = {
        css: css_class,
        method: method,
        class: option_class
    };
};

mbcms.visual_fast_edit.__sorting = function ()
{

    function is_move(hoverme, button)
    {

        var trg = false;
        var attr = hoverme.attr('__user_cms_parent_output_index');
        if (attr == parseInt(attr))
        {
            if (button == KEY_UP)
            {
                trg = hoverme.prevAll('[__cms_output_index][__user_cms_parent_output_index=' + attr + ']:first');
            }
            else
            {
                trg = hoverme.nextAll('[__cms_output_index][__user_cms_parent_output_index=' + attr + ']:first');
            }
        }
        else
        {
            if (button == KEY_UP)
            {
                trg = hoverme.prevAll('[__cms_output_index]:not([__user_cms_parent_output_index]):first');
            }
            else
            {
                trg = hoverme.nextAll('[__cms_output_index]:not([__user_cms_parent_output_index]):first');
            }
        }


        return trg;
    }

    $(document)
        .keyup(function (e)
        {
            if (site.messages.factory('visual_fast_edit_structure_page').is_active())
            {
                var hoverme = $('.hoverme[__cms_output_index]:first');

                switch (e.keyCode)
                {
                    case KEY_UP:

                        var trg = is_move(hoverme, KEY_UP);

                        if (trg)
                        {
                            hoverme.after(trg);
                        }

                        break;
                    case KEY_DOWN:

                        var trg = is_move(hoverme, KEY_DOWN);
                        if (trg)
                        {
                            hoverme.before(trg);
                        }

                        break;
                    case KEY_LEFT:

                        var attr = hoverme.attr('__user_cms_parent_output_index');
                        if (attr == parseInt(attr))
                        {
                            var first_parent = hoverme.parent();
                            var parent_index = hoverme.parent().attr('__user_cms_parent_output_index') || false;
                            first_parent.after(hoverme);
                            if (parent_index)
                            {
                                hoverme.attr('__user_cms_parent_output_index', parent_index);
                            }
                            else
                            {
                                hoverme.removeAttr('__user_cms_parent_output_index');
                            }
                            hoverme.attr('__my_level', first_parent.attr('__my_level'));
                            hoverme.css({marginLeft: hoverme.attr('__my_level') * 5});
                        }

                        // вынос на уровень выше
                        break;
                    case KEY_RIGHT:

                        var trg = is_move(hoverme, KEY_UP);
                        hoverme.appendTo(trg);
                        hoverme.attr('__my_level', trg.attr('__my_level'));
                        hoverme.css({marginLeft: hoverme.attr('__my_level') * 5});
                        hoverme.attr('__user_cms_parent_output_index', trg.attr('__cms_output_index'));

                        break;
                }

                return false;
            }

        });
};


new event.site.load().listen(function ()
{
    mbcms.visual_fast_edit.__create_title('Структура');

    mbcms.visual_fast_edit.__sorting();
});