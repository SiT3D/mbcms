
mbcms.get_all_templates = function ()
{

};

mbcms.get_all_templates.__folders_php_class = 'MBCMS\\GetAllTemplates\\folder_actions';

mbcms.get_all_templates.init_window = function (window)
{
    this.__init_add_structure_btn(window);
    this.__init_add_css_btn(window);
    this.__init_add_folder_btn(window);
    this.__init_folder_go(window);
    this.__init_folder_rename(window);
    this.__init_folder_transfer(window);
    this.__init_folder_delete(window);
    this.init_templates(window);
};

mbcms.get_all_templates.init_templates = function (window)
{
    window.$
            .find('.boxes-right-ul')
            .each(function ()
            {
                mbcms.get_all_templates.init_template($(this), window);
            });
};

mbcms.get_all_templates.init_template = function ($templateModule, window)
{
    this.__init_template_delete($templateModule, window);
    this.__init_template_clone($templateModule, window);
    this.__init_template_transfer($templateModule, window);
    this.__init_drag($templateModule);
    this.__init_settings_panel_start($templateModule);
};

mbcms.get_all_templates.__init_add_structure_btn = function (window)
{
    window.$.find('#add_structure').click(function ()
    {
        $.ajax(
                {
                    url: '/ajax',
                    data: {class: 'MBCMS\\template->create_new_template', path: mbcms.get_all_templates.__get_curent_path(window)},
                    success: function ()
                    {
                        window.setContent();
                    }
                });

        return false;
    });
};

mbcms.get_all_templates.__init_add_folder_btn = function (window)
{
    window.$.find('#add_folder').click(function ()
    {

        var btn_action = function (e)
        {

            var ajax = false;

            if (e.type === 'keyup' && e.keyCode === KEY_ENTER && $.trim($(this).val()) !== '')
            {
                ajax = true;
            }
            else if (e.type === 'blur' && $.trim($(this).val()) !== '')
            {
                ajax = true;
            }

            if (ajax)
            {
                $.ajax(
                        {
                            url: '/ajax',
                            data: {class: mbcms.get_all_templates.__folders_php_class + '->add', name: $(this).val(),
                                parentPath: mbcms.get_all_templates.__get_curent_path(window)},
                            success: function ()
                            {
                                window.setContent();
                            }
                        });
            }

            if ($.trim($(this).val()) === '' || ajax)
                $(this).remove();
        };

        $('<input />')
                .appendTo(window.$)
                .css({position: 'absolute', top: $(this).position().top, left: $(this).position().left})
                .focus()
                .blur(btn_action)
                .keyup(btn_action)
                ;

    });
};


mbcms.get_all_templates.__init_add_css_btn = function (window)
{

    var create_panel = function ($btn)
    {
        var $panel = $('<div id="add_module_stylesheet_panel" />');
        $panel.css({background: '#fff', border: '1px solid #000', position: 'absolute', top: $btn.position().top, left: 0});

        $('<input type="text" />')
                .appendTo($panel)
                .attr('add_module_stylesheet__name', 'alias')
                .attr('placeholder', 'alias')
                .attr('add_module_stylesheet__valid_action', true);

        $('<input type="text" />')
                .appendTo($panel)
                .attr('add_module_stylesheet__valid_action', true)
                .attr('add_module_stylesheet__rule', 'not_empty')
                .attr('placeholder', 'namespace*')
                .attr('add_module_stylesheet__name', 'namespace');

        $('<input type="text" />')
                .appendTo($panel)
                .attr('add_module_stylesheet__rule', 'not_empty')
                .attr('add_module_stylesheet__name', 'class_name')
                .attr('placeholder', 'class_name*')
                .attr('add_module_stylesheet__valid_action', true);

        return $panel;

    };

    var valid = function ($panel)
    {
        var valid = true;

        $panel.find('[add_module_stylesheet__rule=not_empty]').each(function ()
        {
            if ($.trim($(this).val()) === '')
            {
                valid = false;
            }
        });

        return valid;
    };

    var get_values = function ($panel)
    {
        var values = {};
        values.namespace = $panel.find('[add_module_stylesheet__name=namespace]').val();
        values.class_name = $panel.find('[add_module_stylesheet__name=class_name]').val();
        values.alias = $panel.find('[add_module_stylesheet__name=alias]').val();

        return values;
    };

    var create_new_module = function ($panel)
    {
        var values = get_values($panel);
        values.class = 'MBCMS\\create_new_module';
        $.ajax(
                {
                    url: '/ajax',
                    data: values,
                    success: function ()
                    {
                        close_panel($panel);
                    }
                });
    };

    var action_init = function (window)
    {
        var $panel = window.$.find('#add_module_stylesheet_panel');
        $panel.find('[add_module_stylesheet__valid_action=true]').blur(function ()
        {
            if (valid($panel))
            {
                create_new_module($panel);
            }
        }).keyup(function (e)
        {
            if (e.keyCode === 13)
            {
                create_new_module($panel);
            }
            else if (e.keyCode === 27)
            {
                close_panel($panel);
            }
        });
    };

    var close_panel = function ($panel)
    {
        $panel.remove();
    };

    window.$.find('#add_stylesheet_btn').click(function ()
    {
        create_panel($(this)).appendTo(window.$);
        action_init(window);
    });

};

mbcms.get_all_templates.__init_folder_go = function (window)
{
    window.$.find('.template-folder .name, .template-folder .fimage').click(function ()
    {
        function __A(text, root)
        {
            var $a = $('<a class="templates-path_href" href="#">' + text + '</a>');
            if (root)
            {
                $a.addClass('root');
            }
            return $a;
        }

        var $path = window.$.find('#TEMPLATES_current_folder_path');
        if ($.trim($path.text()) === '')
        {
            $path.append(__A('ROOT', true));
        }
        $path.append(__A($(this).parent().attr('path')));
        mbcms.get_all_templates.__get_curent_path(window);
        window.setContent();

        return false;
    });

    window.$.find('.templates-path_href').click(function ()
    {
        $(this).nextAll('.templates-path_href').remove();
        if ($(this).is(':first-child'))
        {
            $(this).remove();
        }
        mbcms.get_all_templates.__get_curent_path(window);
        window.setContent();
        return false;
    }); //mousedown задержать чтобы скопировать путь, до нажатой ссылки
};

mbcms.get_all_templates.__get_curent_path = function (window)
{
    var result = '';
    window.$.find('#TEMPLATES_current_folder_path').children('.templates-path_href:NOT(.root)').each(function ()
    {
        result += $(this).text();
    });

    window.path = result;
    return result;
};

mbcms.get_all_templates.__init_folder_rename = function (window)
{
    window.$.find('.template-folder .ico.pen').click(function ()
    {
        var $pen = $(this);
        var btn_action = function (e)
        {
            var ajax = false;

            if (e.type === 'keyup' && e.keyCode === KEY_ENTER && $.trim($(this).val()) !== '')
            {
                ajax = true;
            }
            else if (e.type === 'blur' && $.trim($(this).val()) !== '')
            {
                ajax = true;
            }

            if (ajax)
            {
                $.ajax(
                        {
                            url: '/ajax',
                            data: {class: mbcms.get_all_templates.__folders_php_class+'->rename', name: $(this).val(),
                                id: $pen.attr('fid')},
                            success: function ()
                            {
                                window.setContent();
                            }
                        });
            }

            if ($.trim($(this).val()) === '' || ajax)
                $(this).remove();
        };

        $('<input />')
                .appendTo($pen.parent())
                .css({position: 'absolute', top: 3, left: 0})
                .focus()
                .blur(btn_action)
                .keyup(btn_action)
                ;

        return false;
    });

};

mbcms.get_all_templates.__init_folder_transfer = function (window)
{
    window.$.find('.template-folder .ico.transfer-document').click(function ()
    {
        var $ico = $(this);
        var btn_action = function (e)
        {
            var ajax = false;

            if (e.type === 'keyup' && e.keyCode === KEY_ENTER && $.trim($(this).val()) !== '')
            {
                ajax = true;
            }
            else if (e.type === 'blur' && $.trim($(this).val()) !== '')
            {
                ajax = true;
            }

            if (ajax)
            {
                var val = '';
                if ($.trim($(this).val()) === '/')
                {
                    val = '';
                }
                else
                {
                    val = $(this).val();
                }

                $.ajax(
                        {
                            url: '/ajax',
                            data: {class: mbcms.get_all_templates.__folders_php_class+'->transfer', path: val,
                                id: $ico.attr('fid')},
                            success: function ()
                            {
                                window.setContent();
                            }
                        });
            }

            if ($.trim($(this).val()) === '' || ajax)
                $(this).remove();
        };

        $('<input />')
                .appendTo($ico.parent())
                .css({position: 'absolute', top: 3, left: 0})
                .focus()
                .blur(btn_action)
                .keyup(btn_action)
                ;

        return false;
    });
};

mbcms.get_all_templates.__init_folder_delete = function (window)
{
    window.$.find('.template-folder .ico.del').click(function ()
    {
        var $ico = $(this);
        if (confirm('Del??'))
        {
            $.ajax(
                    {
                        url: '/ajax',
                        data: {class: mbcms.get_all_templates.__folders_php_class+'->del', id: $(this).attr('fid')},
                        success: function ()
                        {
                            $ico.parents('.template-folder:first').remove();
                        }
                    });
        }
        return false;
    });
};

mbcms.get_all_templates.__init_template_delete = function ($template)
{
    $template
            .find('#del_template')
            .click(function ()
            {
                var $hr = $(this);
                if (confirm())
                {
                    $.ajax(
                            {
                                url: '/ajax',
                                data: {class: 'MBCMS\\template->delete', idTemplate: $(this).attr('idtemplate')},
                                success: function ()
                                {
                                    $hr
                                            .parents('.boxes-right-ul:first')
                                            .remove();
                                }
                            });
                }
            });
};

mbcms.get_all_templates.__init_template_clone = function ($template, window)
{
//    $template
//            .find('#clone_template')
//            .click(function ()
//            {
//                $.ajax(
//                        {
//                            url: '/ajax',
//                            data: {class: 'MBCMS\\clone_template', idTemplate: $(this).attr('idtemplate')},
//                            success: function (msg)
//                            {
//                                var $cloneTemplate = $(msg);
//                                $template.after($cloneTemplate);
//                                mbcms.get_all_templates.init_template($cloneTemplate, window);
//                            }
//                        });
//            });
};

mbcms.get_all_templates.__init_template_transfer = function ($template)
{
    $template
            .find('#transfer_template')
            .click(function ()
            {
                var $ico = $(this);
                var btn_action = function (e)
                {
                    var ajax = false;

                    if (e.type === 'keyup' && e.keyCode === KEY_ENTER && $.trim($(this).val()) !== '')
                    {
                        ajax = true;
                    }
                    else if (e.type === 'blur' && $.trim($(this).val()) !== '')
                    {
                        ajax = true;
                    }

                    if (ajax)
                    {
                        var val = '';
                        if ($.trim($(this).val()) === '/')
                        {
                            val = '';
                        }
                        else
                        {
                            val = $(this).val();
                        }

                        $.ajax(
                                {
                                    url: '/ajax',
                                    data: {class: 'MBCMS\\template->transfer', path: val, id: $ico.attr('idtemplate')},
                                    success: function (msg)
                                    {
                                        if (msg === 'true')
                                            $ico.parents('.boxes-right-ul:first').remove();
                                    }
                                });
                    }

                    if ($.trim($(this).val()) === '' || ajax)
                        $(this).remove();
                };

                $('<input />')
                        .appendTo($ico.parent())
                        .css({position: 'absolute', top: 3, left: 100})
                        .focus()
                        .blur(btn_action)
                        .keyup(btn_action)
                        ;

                return false;
            });
};


mbcms.get_all_templates.__init_drag = function ($templateModule)
{
    $templateModule
            .find('#transfer_move')
            .draggable(
                    {
                        distance: 5,
                        helper: 'clone',
                        zIndex: 9999999
                    })
            ;
};

mbcms.get_all_templates.__init_settings_panel_start = function ($templateModule)
{
//    mbcms.settings_panel.init_idtemplate_element($templateModule.find('#edit_template'), 10);
};