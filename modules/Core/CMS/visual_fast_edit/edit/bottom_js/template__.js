//
//
//
// mbcms.visual_fast_edit.template = function ()
// {
//
// };
//
// mbcms.visual_fast_edit.template.__copy = {};
// mbcms.visual_fast_edit.template.__static = false;
// mbcms.visual_fast_edit.template.__produc_hidden = false;
//
// mbcms.visual_fast_edit.template.create_icons = function (fast_edit_data, ico_css)
// {
//    this.__fast_edit_data = fast_edit_data;
//
//    var self = this;
//
//    self.__ico = $('<div />')
//            .addClass('mbcms_visual_fast_edit_ico ico ios_settings')
//            .css(ico_css)
//            .click(function ()
//            {
//                mbcms.visual_fast_edit.__set_active($(this));
//                return false;
//            })
//            ;
//
//    self.__ico.data('create', self.__create_tools);
//    self.__ico.data('destroy', self.__destroy_tools);
//    self.__ico.data('show', self.__show_tools);
//    self.__ico.data('this_context', self);
//    return self.__ico;
// };
//
// mbcms.visual_fast_edit.template.__show_tools = function (show)
// {
//    if (show)
//    {
//        this.__class_input.show();
//        this.__template_title.show();
//        this.__template_description.show();
//        this.__class_name.show();
//        this.__idtemplate.show();
//        this.__delete_btn.show();
//        this.__css_list.show();
//        this.__css_paste.show();
//        this.__css_rename.show();
//        this.__css_copy.show();
//        this.__css_del.show();
//        this.__css_clone.show();
//        this.__class_current_edit.show();
//        this.__parent_out_index.show();
//        this.__gen_static.show();
//        this.__produc_hidden_button.show();
//
//        this.__set_draggable(true);
//    }
//    else
//    {
//        this.__class_input.hide();
//        this.__template_title.hide();
//        this.__template_description.hide();
//        this.__class_name.hide();
//        this.__idtemplate.hide();
//        this.__delete_btn.hide();
//        this.__css_list.hide();
//        this.__css_paste.hide();
//        this.__css_rename.hide();
//        this.__css_copy.hide();
//        this.__css_del.hide();
//        this.__css_clone.hide();
//        this.__class_current_edit.hide();
//        this.__parent_out_index.hide();
//        this.__gen_static.hide();
//        this.__produc_hidden_button.hide();
//
//        this.__set_draggable(false);
//
//        this.__save();
//    }
// };
//
// mbcms.visual_fast_edit.template.__save = function ()
// {
//    var data = this.__fast_edit_data;
//
//    var old_class = mbcms.visual_fast_edit.get_options(data, 'settingsData');
//    old_class = isset(old_class, '__user_cms_class') ? old_class.__user_cms_class : '';
//
//    var savedata = {};
//
//    var classname = $.trim(this.__class_input.val()).replace(new RegExp(' ', 'g'), '-');
//    var title = $('#template_title_data_input').val();
//    var description = $('#template_description_data_input').val();
//
//    if ($.trim(classname) !== '')
//    {
//        savedata.__user_cms_class = mbcms.visual_fast_edit.__trim(classname);
//        savedata.__user_cms_parent_output_index = mbcms.visual_fast_edit.__trim(this.__parent_out_index.val());
//        savedata.__user_cms_dop_css_classes = mbcms.visual_fast_edit.__trim(this.__class_current_edit.val());
//        savedata.__static = this.__static;
//        savedata.__produc_hidden = this.__produc_hidden;
//
//        mbcms.template.save_settings(data.idTemplate, savedata, function ()
//        {
//            savedata = {};
//            savedata.title = title;
//            savedata.description = description;
//            mbcms.visual_fast_edit.get_targets(data).attr('template_title', title);
//
//            mbcms.ajax(
//                    {
//                        data:
//                                {
//                                    class: 'MBCMS\\template->save_meta',
//                                    idTemplate: data.idTemplate,
//                                    info: savedata
//                                },
//                        success: function ()
//                        {
//                            if (old_class != classname)
//                            {
//                                mbcms.visual_fast_edit.destroy();
//                                mbcms.controll_window.load();
//                            }
//                        }
//                    });
//        });
//
//
//    }
// };
//
// mbcms.visual_fast_edit.template.__set_draggable = function (drag)
// {
//    var data = this.__fast_edit_data;
//    var pid = data.this.attr('parentidtemplate');
//    var self = this;
//    var selector = '[parentidtemplate=' + pid + ']:NOT([idtemplate])';
//
//
//    var dp = function (a, b)
//    {
//        $(this).append(b.draggable);
//        var __sort = {};
//        $(this).children('[parentidtemplate=' + pid + ']').each(function ()
//        {
//            var __index = $(this).attr('__cms_output_index');
//            __sort[__index] = $(this);
//        });
//
//        self.__parent_out_index.val($(this).attr('__cms_output_index'));
//
//        return false;
//    };
//
//    if (drag)
//    {
//        data.parent.find(selector).droppable({
//            drop: dp,
//            hoverClass: '__mbcms_outputs-hover-class'
//        }).disableSelection();
//        data.this.draggable({containment: 'window', refreshPositions: true, revert: true, revertDuration: 0}); // сделать еще сортбл такой же! в адд
//    }
//    else
//    {
//        data.parent.find(selector).droppable('destroy');
//        if (isset(data.this, 'draggable', 'destroy'))
//            data.this.draggable('destroy');
//    }
// };
//
// mbcms.visual_fast_edit.template.__destroy_tools = function ()
// {
//    this.__class_input.remove();
//    this.__template_title.remove();
//    this.__template_description.remove();
//    this.__class_name.remove();
//    this.__idtemplate.remove();
//    this.__delete_btn.remove();
//    this.__css_list.remove();
//    this.__css_paste.remove();
//    this.__css_rename.remove();
//    this.__css_copy.remove();
//    this.__css_del.remove();
//    this.__css_clone.remove();
//    this.__class_current_edit.remove();
//    this.__parent_out_index.remove();
//    this.__gen_static.remove();
//    this.__produc_hidden_button.remove();
// };
//
// mbcms.visual_fast_edit.template.__create_tools = function ()
// {
//    var self = this;
//    var data = this.__fast_edit_data;
//    this.__tools = true;
//    var settingsData = mbcms.visual_fast_edit.get_options(data, 'settingsData');
//
//    this.__static = isset(mbcms.visual_fast_edit.get_options(data, 'settingsData'), '__static') ? mbcms.visual_fast_edit.get_options(data, 'settingsData')['__static'] : 'false';
//    this.__produc_hidden = isset(mbcms.visual_fast_edit.get_options(data, 'settingsData'), '__produc_hidden') ? mbcms.visual_fast_edit.get_options(data, 'settingsData')['__produc_hidden'] : 'false';
//
//    this.__set_draggable(true);
//
//    this.__class_input = $('<input type="text" />')
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '80%',
//                        left: '10%',
//                        width: 200
//                    })
//            .addClass('mbcms-bootstrap')
//            .val(isset(settingsData, '__user_cms_class') ? settingsData.__user_cms_class : 'this')
//            .prop('id', 'template_class_data_input')
//            .appendTo('body')
//            .change(function ()
//            {
//                if (typeof this.__time != 'undefined')
//                {
//                    clearTimeout(this.__time);
//                }
//
//                this.__time = setTimeout(function ()
//                {
//                    var $old = $(this).attr('__old');
//                    mbcms.visual_fast_edit.get_targets(data).removeClass($old).addClass($(this).val()).attr('__old', $(this).val());
//                }, 500);
//            })
//            .attr('__old', mbcms.visual_fast_edit.get_targets(data, true).attr('__user_cms_dop_css_classes'))
//            ;
//
//    this.__class_current_edit = $('<textarea placeholder="классы стилей" />')
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '80%',
//                        left: '30%',
//                        width: 180
//                    })
//            .addClass('mbcms-bootstrap')
//            .val(mbcms.visual_fast_edit.get_targets(data, true).attr('__user_cms_dop_css_classes'))
//            .appendTo('body')
//            .keyup(function ()
//            {
//                if (typeof this.__time != 'undefined')
//                {
//                    clearTimeout(this.__time);
//                }
//
//                var $this = $(this);
//                this.__time = setTimeout(function ()
//                {
//                    var $old = $this.attr('__old');
//                    data.this.removeClass($old).addClass($this.val());
//                    $this.attr('__old', $this.val());
//                }, 500);
//            })
//            .attr('__old', mbcms.visual_fast_edit.get_targets(data, true).attr('__user_cms_dop_css_classes'))
//            ;
//
//    self.__template_title = $('<input type="text" />')
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '70%',
//                        left: '10%',
//                        width: 200
//                    })
//            .addClass('mbcms-bootstrap')
//            .prop('id', 'template_title_data_input')
//            .hide()
//            .appendTo('body')
//            ;
//
//    self.__template_description = $('<textarea />')
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '70%',
//                        left: '35%',
//                        width: 200
//                    })
//            .addClass('mbcms-bootstrap')
//            .prop('id', 'template_description_data_input')
//            .hide()
//            .appendTo('body')
//            ;
//
//    self.__class_name = $('<input type="text" />')
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '75%',
//                        left: '10%',
//                        width: 200
//                    })
//            .addClass('mbcms-bootstrap')
//            .appendTo('body')
//            .prop('readonly', true)
//            .val(data.fast_edit_class)
//            ;
//
//    self.__idtemplate = $('<input type="text" />')
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '65%',
//                        left: '10%',
//                        width: 100
//                    })
//            .addClass('mbcms-bootstrap')
//            .appendTo('body')
//            .prop('readonly', true)
//            .val(data.idTemplate)
//            ;
//
//    self.__delete_btn = $('<button/>')
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '85%',
//                        left: '10%',
//                        width: 100
//                    })
//            .addClass('mbcms-bootstrap btn')
//            .appendTo('body')
//            .click(function (e)
//            {
//                e.stopPropagation();
//                if (confirm('Delete this template?'))
//                {
//                    $.ajax(
//                            {
//                                url: '/ajax',
//                                data: {class: 'MBCMS\\template->delete', idTemplate: $(this).attr('idtemplate')},
//                                success: function ()
//                                {
//                                    mbcms.controll_window.get().find('[idtemplate=' + data.idTemplate + ']').remove();
//                                    mbcms.visual_fast_edit.destroy();
//                                }
//                            });
//                }
//            })
//            .attr('idtemplate', data.idTemplate)
//            .text('Delete')
//            ;
//
//    mbcms.ajax(
//            {
//                data:
//                        {
//                            class: 'MBCMS\\template->get_meta',
//                            idTemplate: data.idTemplate
//                        },
//                success: function (msg)
//                {
//                    var req = $.parseJSON(msg);
//
//                    $('#template_description_data_input').val(req.description).show();
//                    $('#template_title_data_input').val(req.title).show();
//
//                }
//            });
//
//    self.__css_list = $('<div />')
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '70%',
//                        left: '60%',
//                        width: '30%',
//                        height: '16%',
//                        overflowY: 'auto',
//                        overflowX: 'none',
//                    })
//            .addClass('mbcms-bootstrap pixel_gray_50')
//            .appendTo('body')
//            ;
//
//    self.__css_copy = $('<button/>')
//            .text('Copy')
//            .click(function (e)
//            {
//                e.stopPropagation();
//                self.__copy.idTemplate = data.idTemplate;
//                self.__copy.files = [];
//
//                self.__css_list
//                        .find('[active]')
//                        .each(function ()
//                        {
//                            self.__copy.files.push($(this).text());
//                        });
//            })
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '65%',
//                        left: '60%',
//                    })
//            .addClass('mbcms-bootstrap btn')
//            .appendTo('body')
//            ;
//
//    self.__css_paste = $('<button/>')
//            .text('Paste')
//            .click(function (e)
//            {
//                e.stopPropagation();
//                if (self.__copy.idTemplate != data.idTemplate)
//                    mbcms.template.copy_css_list(self.__copy.idTemplate, data.idTemplate, self.__copy.files, function ()
//                    {
//                        mbcms.dinamic_js_css_loader.load('', data.idTemplate);
//                    });
//            })
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '65%',
//                        left: '80%',
//                    })
//            .addClass('mbcms-bootstrap btn')
//            .appendTo('body')
//            ;
//
//    self.__css_rename = $('<button/>')
//            .text('Raname')
//            .click(function (e)
//            {
//                e.stopPropagation();
//                var name = '';
//                self.__css_list
//                        .find('[active]:first')
//                        .each(function ()
//                        {
//                            name = $(this).text();
//                        });
//
//                mbcms.visual_fast_edit.fly_form.create(
//                        [
//                            {$: $('<input type="text" />').text(name), key: 'new_name', alias: 'Новое имя', value: name, req: true}
//                        ],
//                        function (values)
//                        {
//                            mbcms.template.rename_css(data.idTemplate, name, values.new_name, function ()
//                            {
//                                self.__css_list.find('[active]:first').text(values.new_name);
// //                                mbcms.dinamic_js_css_loader.delete_styles(name);
//                                mbcms.dinamic_js_css_loader.load('', data.idTemplate);
//                            });
//                        }
//                );
//            })
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '65%',
//                        left: '90%',
//                    })
//            .addClass('mbcms-bootstrap btn')
//            .appendTo('body')
//            ;
//
//    self.__css_del = $('<button/>')
//            .text('Del Css')
//            .click(function (e)
//            {
//                e.stopPropagation();
//                var list = [];
//                self.__css_list
//                        .find('[active]')
//                        .each(function ()
//                        {
//                            list.push($(this).text());
//                        });
//
//                mbcms.ajax(
//                        {
//                            data:
//                                    {
//                                        class: 'MBCMS\\template->delete_css_list',
//                                        idTemplate: data.idTemplate,
//                                        list: list
//                                    },
//                            success: function ()
//                            {
//                                self.__css_list
//                                        .find('[active]')
//                                        .each(function ()
//                                        {
//                                            $(this).remove();
//                                            mbcms.dinamic_js_css_loader.load('', data.idTemplate);
//                                        });
//                            }
//                        });
//            })
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '65%',
//                        left: '70%',
//                    })
//            .addClass('mbcms-bootstrap btn')
//            .appendTo('body')
//            ;
//
//    self.__css_clone = $('<button/>')
//            .text('Clone Css')
//            .click(function (e)
//            {
//                e.stopPropagation();
//                var list = [];
//                self.__css_list
//                        .find('[active]')
//                        .each(function ()
//                        {
//                            list.push($(this).text());
//                        });
//
//                mbcms.ajax(
//                        {
//                            data:
//                                    {
//                                        class: 'MBCMS\\template->clone_css_list',
//                                        idTemplate: data.idTemplate,
//                                        list: list
//                                    },
//                            success: function ()
//                            {
//                                mbcms.visual_fast_edit.destroy();
//                                mbcms.visual_fast_edit.create(data, null, true);
//                                mbcms.dinamic_js_css_loader.load(data.idTemplate);
//                            }
//                        });
//            })
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '60%',
//                        left: '75%',
//                    })
//            .addClass('mbcms-bootstrap btn')
//            .appendTo('body')
//            ;
//
//    var pid = '';
//    if (isset(mbcms.visual_fast_edit.get_options(data, 'settingsData'), '__user_cms_parent_output_index'))
//        pid = mbcms.visual_fast_edit.get_options(data, 'settingsData')['__user_cms_parent_output_index'];
//
//    this.__parent_out_index = $('<input type="text" />')
//            .css(
//                    {
//                        position: 'fixed',
//                        top: '65%',
//                        left: '23%',
//                        width: 80
//                    })
//            .addClass('mbcms-bootstrap')
//            .val(pid)
//            .appendTo('body')
//            ;
//
//    this.__produc_hidden_button = $('<input type="checkbox" />')
//            .css({position: 'fixed', left: 20, bottom: 150, width: 30, height: 30, background: 'green'})
//            .click(function ()
//            {
//                self.__produc_hidden = $(this).prop('checked') ? 'true' : 'false';
//            })
//            .prop('checked', this.__produc_hidden == 'true' ? true : false)
//            ;
//
//    this.__gen_static = $('<input type="checkbox" />')
//            .css({position: 'fixed', left: 20, bottom: 100, width: 40, height: 40})
//            .appendTo('body')
//            .click(function ()
//            {
//
//                self.__static = $(this).prop('checked') ? 'true' : 'false';
//                mbcms.visual_fast_edit.get_targets(data).each(function ()
//                {
//                    $(this).attr('__static_view', self.__static);
//                });
//            })
//            .prop('checked', this.__static == 'true' ? true : false)
//            ;
//
//
//    mbcms.template.get_css_list(data.idTemplate, function (msg)
//    {
//        var req = msg ? $.parseJSON(msg) : null;
//
//        if (!isset(req, 'values'))
//            return;
//
//        for (var i = 0; i < req.values.length; i++)
//        {
// //            if (i % 3 == 0 && i != 0)
// //                $('<br/>').appendTo(self.__css_list);
//            $('<span/>')
//                    .appendTo(self.__css_list)
//                    .text(req.values[i])
//                    .css(
//                            {
//                                padding: '10px 20px', display: 'inline-block', cursor: 'pointer', color: '#fff', fontWeight: 'bold',
//                                fontSize: '13px'
//                            })
//                    .click(function (e)
//                    {
//                        e.stopPropagation();
//
//                        if (!$(this).attr('active'))
//                        {
//                            $(this).css({background: 'green'});
//                            $(this).attr('active', true);
//                        }
//                        else
//                        {
//                            $(this).css({background: ''});
//                            $(this).removeAttr('active');
//                        }
//                    })
//                    .each(function ()
//                    {
//                        var selector = $(this).text();
// //                        selector = selector.replace('..', '.');
//                        var l = data.this.find('.' + selector).length;
//                        if (l == 0)
//                            $(this).css({opacity: 0.6});
//                    })
//                    ;
//        }
//    });
//
// };
//
//
// mbcms.visual_fast_edit.template.__check_dinamic_childrens = function ()
// {
//    var data = this.__fast_edit_data;
//
//    var search = data.this.find('[idtemplate][fast_edit]:NOT([__static_view=true])');
//    mbcms.visual_fast_edit.template.__search = search;
//    return search.length > 0 ? true : false;
// };