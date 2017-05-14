
mbcms.visual_fast_edit.add_struct = function ()
{
};


mbcms.visual_fast_edit.add_struct.create_icons = function (fast_edit_data, ico_css)
{
    this.__fast_edit_data = fast_edit_data;

    var self = this;

    self.__ico = $('<div />')
            .addClass('mbcms_visual_fast_edit_ico ico add_btn')
            .css(ico_css)
            .click(function ()
            {
                mbcms.visual_fast_edit.__set_active($(this));
                return false;
            })
            ;

    self.__ico.data('create', self.__create_tools);
    self.__ico.data('destroy', self.__destroy_tools);
    self.__ico.data('show', self.__show_tools);
    self.__ico.data('this_context', self);
    return self.__ico;
};

mbcms.visual_fast_edit.add_struct.__show_tools = function (show)
{
    if (show)
    {
        this.__left_list.show();
        this.__add_new_template_btn.show();
        this.__all_templates.show();
        this.__right_list.show();
        this.__left_list_templates.show();
        this.__mini_btn.show();
    } else
    {
        this.__left_list.hide();
        this.__add_new_template_btn.hide();
        this.__all_templates.hide();
        this.__right_list.hide();
        this.__left_list_templates.hide();
        this.__mini_btn.hide();

        this.__save();
    }
};

mbcms.visual_fast_edit.add_struct.__save = function ()
{
    var data = this.__fast_edit_data;

    if (this.__be_sortable)
    {
        mbcms.ajax(
                {
                    data:
                            {
                                class: 'MBCMS\\output->resort',
                                idTemplate: this.__fast_edit_data.idTemplate,
                                indexis: this.__new_indexis
                            }
                });
    }

    if (this.__be_sortable_templates)
    {
        console.log({
            class: 'MBCMS\\template->resort',
            idTemplate: this.__fast_edit_data.idTemplate,
            indexis: this.__new_indexis_templates
        });

        mbcms.ajax(
                {
                    data:
                            {
                                class: 'MBCMS\\template->resort',
                                idTemplate: this.__fast_edit_data.idTemplate,
                                indexis: this.__new_indexis_templates
                            }
                });
    }

    if (this.__add_new)
        mbcms.controll_window.load(function ()
        {
            mbcms.visual_fast_edit.create(data, function ()
            {
                mbcms.visual_fast_edit.__set_active($('.mbcms_visual_fast_edit_ico.ico.add_btn'));
            });
        });
};

mbcms.visual_fast_edit.add_struct.__destroy_tools = function ()
{
    this.__left_list.remove();
    this.__add_new_template_btn.remove();
    this.__all_templates.remove();
    if (isset(this, '__right_list', 'remove'))
        this.__right_list.remove();
    this.__left_list_templates.remove();
    this.__mini_btn.remove();
};



mbcms.visual_fast_edit.add_struct.__refresh_out_list = function ()
{
    var data = this.__fast_edit_data;
    var self = this;
    this.__left_list.empty();

    var outs = mbcms.visual_fast_edit.get_outs($(data.current_class));

    outs.each(function ()
    {
        var $this = $(this);
        self.__create_list_item($this.attr('__user_cms_out_title'), $this.attr('css_class'), $this, $this.attr('__cms_output_index'), self.__left_list);
    });

    this.__set_sortable(this.__left_list, '__be_sortable', '__new_indexis');

};

mbcms.visual_fast_edit.add_struct.__refresh_temp_list = function ()
{
    var data = this.__fast_edit_data;
    var self = this;
    this.__left_list_templates.empty();

    var templates = mbcms.visual_fast_edit.get_templates($(data.unical_selector));

    templates.each(function ()
    {
        var $this = $(this);
        self.__create_list_item($this.attr('template_title'), $this.attr('css_class'), $this, $this.attr('idtemplate'), self.__left_list_templates);
    });

    this.__set_sortable(this.__left_list_templates, '__be_sortable_templates', '__new_indexis_templates');
};

mbcms.visual_fast_edit.add_struct.__create_list_item = function (title, module_class, trg, index, to_list)
{
    $('<div />')
            .appendTo(to_list)
            .text(title + ' ( ' + module_class + ' )')
            .css({cursor: 'pointer', height: 20, overflow: 'hidden'})
            .attr('index', index)
            .data('trg', trg)
            .hover(function ()
            {
                $(this).css({color: 'orange'});
                mbcms.visual_fast_edit.create_global_axis(option.get_fast_edit_data(trg), true);
            }, function ()
            {
                mbcms.visual_fast_edit.remove_global_axis(option.get_fast_edit_data(trg));
                $(this).css({color: 'white'});
            })
            .click(function ()
            {
                mbcms.visual_fast_edit.create(option.get_fast_edit_data(trg));
                return false;
            })
            ;
};

mbcms.visual_fast_edit.add_struct.__set_sortable = function (sortable_list, __be_sortable, __new_indexis)
{
    var data = this.__fast_edit_data;
    var self = this;

    sortable_list.sortable(
            {
                update: function (event, ui)
                {
                    self[__be_sortable] = true;
                    self[__new_indexis] = [];
                    sortable_list
                            .children()
                            .each(function ()
                            {
                                var index = $(this).attr('index');
                                if (index != undefined)
                                    self[__new_indexis].push(index);
                            });

                    var trg = ui.item.data('trg');
                    var next_index = ui.item.next().attr('index');
                    if (typeof next_index !== 'undefined')
                    {
                        mbcms.visual_fast_edit.get_outs($(data.unical_selector)).each(function ()
                        {
                            if ($(this).attr('__cms_output_index') == next_index)
                            {
                                if ($(this).parent().attr('__cms_output_index') == trg.parent().attr('__cms_output_index'))
                                    $(this).before(trg);
                            }
                        });

                        mbcms.visual_fast_edit.get_templates(data.this).each(function ()
                        {
                            if ($(this).attr('__cms_template_index') == next_index)
                            {
                                if ($(this).parent().attr('__cms_output_index') == trg.parent().attr('__cms_output_index'))
                                    $(this).before(trg);
                            }
                        });
                    } else
                    {
                        trg.appendTo(data.this);
                    }
                }
            });
    sortable_list.disableSelection();
};

mbcms.visual_fast_edit.add_struct.__create_tools = function ()
{
    var data = this.__fast_edit_data;
    this.__tools = true;

    var self = this;
    this.__add_new = false;
    this.__be_sortable = false;
    this.__be_sortable_templates = false;
    this.__new_indexis = {};
    this.__new_indexis_templates = [];



    this.__left_list = $('<div />')
            .addClass('standart-window-style hover l t')
            .appendTo('body')
            ;

    this.__refresh_out_list();

    this.__mini_btn = $('<button />')
            .text('Generate image')
            .click(function ()
            {
                var t = 500;
                mbcms.controll_window.get()
                        .find('[idtemplate]')
                        .each(function ()
                        {
                            setTimeout(function ()
                            {
                                mbcms.mini_generator.generate($(this).attr('idtemplate'));
                            }, t);
                            t += 2000;
                        });
                return false;
            })
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .css
            (
                    {
                        position: 'fixed',
                        left: '60%',
                        top: '70%',
                        display: 'none'
                    }
            )
            .addClass('mbcms-bootstrap btn')
            ;

    this.__right_list = $('<div />')
            .addClass('standart-window-style hover r t')
            .appendTo('body')
            ;

    mbcms.ajax(
            {
                data:
                        {
                            class: 'MBCMS\\get_all_modules_window',
                            filter_type: mbcms.get_all_modules_window.FILTER_TYPE_OUTPUT
                        },
                success: function (msg)
                {
                    $(msg)
                            .appendTo(mbcms.visual_fast_edit.add_struct.__right_list)
                            .find('.module-in-folder')
                            .css({cursor: 'pointer'})
                            .click(function ()
                            {
                                var $this = $(this);

                                self.__add_new = true;
                                var odata = {
                                    __user_cms_class: 'C' + parseInt(Math.random() * 1000),
                                    __user_cms_out_title: $(this).find('span').text(),
                                };
                                mbcms.output.add(data.idTemplate, $this.attr('name'), odata,
                                        function ()
                                        {
                                            mbcms.controll_window.load();
                                        });
                                return false;
                            })
                            ;
                }
            });


    this.__left_list_templates = $('<div />')
            .addClass('standart-window-style hover l b')
            .appendTo('body')
            ;


    this.__refresh_temp_list();

    this.__add_new_template_btn = $('<button />')
            .css(
                    {
                        position: 'fixed',
                        bottom: 90,
                        right: 10
                    })
            .appendTo('body')
            .addClass('mbcms-bootstrap btn')
            .click(function ()
            {
                mbcms.template.add_new(data.idTemplate, function ()
                {
                    self.__refresh_temp_list();
                });
                return false;
            })
            .text('Add New Template')
            ;


    self.__all_templates = $('<div/>')
            .addClass('standart-window-style hover r b')
            .appendTo('body');

    mbcms.template.get_all('', '', function (html)
    {
        self.__init_all_templates(html);
    });


    $('.standart-window-style').perfectScrollbar();

};

mbcms.visual_fast_edit.add_struct.__std_init_act = function (html)
{
    mbcms.visual_fast_edit.add_struct.__init_all_templates(html);
};

mbcms.visual_fast_edit.add_struct.__init_all_templates = function (html)
{
    var data = this.__fast_edit_data;
    var self = this;
    this.__all_templates.empty();
    // инициализуем нажатия на папки, переносы и прочее 
    html
            .find('.boxes-li')
            .click(function ()
            {
                var value = $(this).attr('idtemplate');
                mbcms.template.add(data.idTemplate, value, function ()
                {
                    mbcms.dinamic_js_css_loader.load('', value);
                    mbcms.controll_window.load();
                    mbcms.template.reload_views(data.idTemplate, function ()
                    {
                        self.__refresh_temp_list();
                    });
                });
            });

    mbcms.template.init_folder_click(html, self.__std_init_act);
    mbcms.template.init_path_click(html, self.__std_init_act);
    mbcms.template.init_create_template(html, function (path)
    {
        mbcms.template.get_all('', path, self.__std_init_act);
    });
    mbcms.template.init_create_folder(html, function (path)
    {
        mbcms.template.get_all('', path, self.__std_init_act);
    });
    mbcms.template.init_delete_folder(html);
    mbcms.template.init_delete_template(html);
    mbcms.template.init_transfer_folder(html);
    mbcms.template.init_transfer_template(html);
    mbcms.template.init_folder_rename(html);

    // Удаление + перенос + переименование папки

    this.__all_templates.append(html);
};



