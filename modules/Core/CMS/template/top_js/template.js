event.template = function ()
{
    this.__key = 'event.template'; // уникальный идентификатор, прсто название класса!
};
event.template.prototype = Object.create(event.prototype);

event.template.get_view = function ()
{
    this.__key = 'event.template.get_view'; // уникальный идентификатор, прсто название класса!
    this.idTemplate = null;
    this.template = null;
};
event.template.get_view.prototype = Object.create(event.prototype);

mbcms.template = function ()
{
};

/**
 *
 * @param {type} idTemplate
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.add_new = function (idTemplate, callback)
{

    mbcms.visual_fast_edit.fly_form.create(
        [
            {
                $: $('<div/>').attr('id', 'result_template_add').css({marginBottom: 20}).text('название шаблона: t'),
                key: 'namer',
            },
            {
                $: $('<input/>')
                    .attr('placeholder', 'idtemplate')
                    .keyup(function ()
                    {
                        $('#result_template_add').text('название шаблона: t' + $(this).val());
                    }),
                key: 'idtemplate',
                req: true
            },
        ],
        function (values)
        {
            mbcms.ajax(
                {
                    data: {
                        class: 'MBCMS\\template->add_new',
                        idTemplate: idTemplate,
                        new_idTemplate: values.idtemplate
                    },
                    success: function (msg)
                    {
                        var req = get_req(msg);
                        mbcms.controll_window.load();
                        mbcms.dinamic_js_css_loader.load('', msg);

                        if (typeof callback == 'function')
                        {
                            callback.call(callback, req);
                        }

                    }
                });
        });

};

/**
 *
 * @param {type} idTemplate
 * @param {type} settingsData
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.save_settings = function (idTemplate, settingsData, callback)
{
    mbcms.ajax(
        {
            data: {
                class: 'MBCMS\\template->update_settings',
                idTemplate: idTemplate,
                settings: settingsData,
            },
            success: function (msg)
            {
                if (typeof callback == 'function')
                    callback.call(callback, msg);

            }
        });
};

/**
 * Генерирует или убивает статичный шаблон, берет из настроек шаблона значение
 *
 * @param {string} idTemplate
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.autogenerate_static = function (idTemplate, callback)
{

    idTemplate = idTemplate || $('[idtemplate]:first').attr('idtemplate');

    mbcms.ajax(
        {
            data: {
                class: 'MBCMS\\template->autogenerate_static',
                idTemplate: idTemplate,
            },
            success: function (msg)
            {
                if (typeof callback == 'function')
                    callback.call(callback, msg);
            },
            error: function (msg)
            {
                console.log(msg);
            }
        });
};

/**
 * Генерирует или убивает цепочку, из ребенок -> родитель, статичные шаблоны.
 *
 * @param {jQuery} jquery
 * @param {string} parent_static_set_status
 * @returns {undefined}
 */
mbcms.template.autogenerate_static_width_parents = function (jquery, parent_static_set_status)
{
    var idTemplate = jquery.attr('idtemplate');
    var current_static_status = jquery.attr('__static_view');


    if ((current_static_status == 'false' || current_static_status == undefined) && parent_static_set_status == undefined && idTemplate != undefined)
    {
        // статус для родителей, от первого ребенка
        parent_static_set_status = 'false';
    }

    this.__autogenerate_static_width_parents_go(jquery, parent_static_set_status);

};

mbcms.template.__autogenerate_static_width_parents_go = function (jquery, parent_static_set_status)
{

    mbcms.template.autogenerate_static(jquery.attr('idtemplate'), function ()
    {
        var parent = jquery.parents('[idtemplate]:first');
        if (parent.length > 0)
        {
            mbcms.template.autogenerate_static_width_parents(parent, parent_static_set_status);
        }
    });
};

/**
 *
 * @param {string} idTemplate_parent
 * @param {string} idTemplate_addTemplate
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.add = function (idTemplate_parent, idTemplate_addTemplate, callback)
{
    mbcms.ajax(
        {
            data: {
                class: 'MBCMS\\template->add',
                idTemplate: idTemplate_parent,
                children_idTemplate: idTemplate_addTemplate
            },
            success: function (msg)
            {
                if (typeof callback == 'function')
                    callback.call(callback, msg);
            }
        });
};

/**
 *
 * @param {string} idTemplate
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.get_css_list = function (idTemplate, callback)
{
    mbcms.ajax(
        {
            data: {
                class: 'MBCMS\\template->get_css_list',
                idTemplate: idTemplate
            },
            success: function (msg)
            {
                if (typeof callback == 'function')
                    callback.call(callback, msg);
            }
        });
};

mbcms.template.copy_css_list = function (idTemplate, idTemplate_paste, list, callback)
{
    mbcms.ajax(
        {
            data: {
                class: 'MBCMS\\template->copy_css_list',
                idTemplate_paste: idTemplate_paste,
                idTemplate: idTemplate,
                list: list
            },
            success: function (msg)
            {
                if (typeof callback == 'function')
                    callback.call(callback, msg);

            }
        });
};

/**
 *
 * @param {string} idTemplate
 * @param {function} callback /// function($jQuery){}
 * @returns {undefined}
 */
mbcms.template.get_view = function (idTemplate, callback)
{
    mbcms.ajax(
        {
            data: {
                class: 'MBCMS\\controll_window->ajax',
                idTemplate: idTemplate
            },
            success: function (msg)
            {
                var template = $(msg);

                // найти все шаблоны, заменить их, и запустить событие, чтобы остальные могли проинитить их
                if (typeof callback == 'function')
                    callback.call(callback, template);

                var evt = new event.template.get_view().call();
                evt.idTemplate = idTemplate;
                evt.template = template;

            }
        });
};


/**
 *
 * @param {type} path
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.create_new_template = function (path, callback)
{
    $.ajax(
        {
            url: '/ajax',
            data: {
                class: 'MBCMS\\template->create_new_template',
                path: path
            },
            success: function ()
            {
                if (is_callable(callback))
                    callback.call(callback, path);
            }
        });
};

/**
 *
 * @param {type} idTemplate
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.reload_views = function (idTemplate, callback)
{
    this.get_view(idTemplate, function (template)
    {
        template = template.children(); // избавиться от обертки!
        mbcms.controll_window.get()
            .find('[idtemplate=' + idTemplate + ']')
            .each(function ()
            {
                var clone = template.clone(true);
                $(this).after(clone);
                $(this).remove();
            })
        ;

        option.fast_edit_init_controll_window($('body'));

        if (typeof callback == 'function')
            callback.call(callback, template);
    });
};

/**
 *
 * @param {type} parentIdTemplate
 * @param {type} idTemplate
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.remove = function (parentIdTemplate, idTemplate, callback)
{
    mbcms.ajax(
        {
            data: {
                class: 'MBCMS\\template->remove',
                parentidtemplate: parentIdTemplate,
                idTemplate: idTemplate,
            },
            success: function (msg)
            {
                var req = get_req(msg);

                if (typeof callback == 'function')
                    callback.call(callback, req);
            }
        });
};

/**
 *
 * @param {type} count
 * @param {type} path
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.get_all = function (count, path, callback)
{
    mbcms.ajax(
        {
            data: {
                class: 'MBCMS\\get_all_templates',
                count: count,
                path: path,
            },
            success: function (html)
            {
                html = $(html);

                if (typeof callback == 'function')
                    callback.call(callback, html);
            }
        });
};


/**
 *
 * @param {type} idTemplate
 * @param {type} name
 * @param {type} new_name
 * @param callback
 * @returns {undefined}
 */
mbcms.template.rename_css = function (idTemplate, name, new_name, callback)
{
    mbcms.ajax(
        {
            data: {
                class: 'MBCMS\\template->rename_css',
                idTemplate: idTemplate,
                name: name,
                new_name: new_name,
            },
            success: function ()
            {
                if (typeof callback == 'function')
                    callback.call(callback);
            }
        });
};


/////////////////////// inits get all templates //////////////////////////////////
//
//
//

/**
 *
 * @param {jQuery} html
 * @param {function} callback
 * @returns {undefined} function(html) {};
 */
mbcms.template.init_folder_click = function (html, callback)
{
    html
        .find('.template-folder img, .template-folder > span')
        .click(function ()
        {
            var path = $(this).parent().attr('fullPath');
            mbcms.template.get_all('', path, function (html)
            {
                if (is_callable(callback))
                {
                    callback.call(callback, html);
                }
            });

            return false;
        })
    ;
};

/**
 *
 * @param {jQuery} html
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.init_path_click = function (html, callback)
{
    html
        .find('#TEMPLATES_current_folder_path a')
        .click(function ()
        {
            var path = '';
            var path_items = $(this).prevAll('a:NOT(.root)');
            path_items.each(function ()
            {
                path = $(this).text() + path;
            });

            if (!$(this).hasClass('root'))
                path += $(this).text();


            mbcms.template.get_all('', path, function (html)
            {
                if (is_callable(callback))
                {
                    callback.call(callback, html);
                }
            });

            return false;
        })
    ;
};

mbcms.template.__get_path = function (html)
{
    var path = '';

    html.find('#TEMPLATES_current_folder_path a:NOT(.root)').each(function ()
    {
        path += $(this).text();
    });

    return path;
};

/**
 *
 * @param {jQuery} html
 * @param {function} callback
 * @returns {undefined}
 */
mbcms.template.init_create_template = function (html, callback)
{
    html
        .find('#add_structure')
        .click(function ()
        {
            var path = mbcms.template.__get_path(html);

            mbcms.template.create_new_template(path, callback);
            return false;
        })
    ;
};

/**
 *
 * @param {jQuery} html
 * @param {function} callback
 * @returns {undefined} function (path) {}
 */
mbcms.template.init_create_folder = function (html, callback)
{

    html
        .find('#add_folder')
        .click(function ()
        {
            mbcms.visual_fast_edit.fly_form.create(
                [
                    {
                        $: $('<input />'),
                        key: 'name',
                        req: true
                    },
                ],
                function ($vals)
                {
                    var path = mbcms.template.__get_path(html);
                    mbcms.ajax(
                        {
                            data: {
                                class: 'MBCMS\\GetAllTemplates\\folder_actions->add',
                                name: $vals.name,
                                path: path
                            },
                            success: function ()
                            {
                                if (is_callable(callback))
                                    callback.call(callback, path);
                            }
                        });
                });

            return false;
        })
    ;
};

/**
 *
 * @param {jQuery} html
 * @returns {undefined}
 */
mbcms.template.init_delete_folder = function (html)
{
    html
        .find('.template-folder .ico.del')
        .click(function ()
        {
            var $this = $(this);
            if (confirm())
            {
                $.ajax(
                    {
                        url: '/ajax',
                        data: {
                            class: 'MBCMS\\GetAllTemplates\\folder_actions->del',
                            fullPath: $(this).attr('fullPath')
                        },
                        success: function ()
                        {
                            $this.parents('.template-folder:first').remove();
                        }
                    });
            }

            return false;
        })
    ;
};

/**
 *
 * @param {jQuery} html
 */
mbcms.template.init_delete_template = function (html)
{

    var templates = html.find('.boxes-right-ul');

    templates.each(function ()
    {
        var $this = $(this);
        $(this)
            .find('#del_template')
            .click(function ()
            {
                if (confirm())
                {
                    mbcms.visual_fast_edit.destroy();

                    $.ajax(
                        {
                            url: '/ajax',
                            data: {
                                class: 'MBCMS\\template->delete',
                                idTemplate: $(this).attr('idtemplate')
                            },
                            success: function ()
                            {
                                $this.remove();
                            }
                        });
                }

                return false;
            })
        ;
    });

};

mbcms.template.init_transfer_folder = function (html)
{
    html
        .find('.template-folder .ico.transfer-document')
        .click(function ()
        {
            var $this = $(this);
            mbcms.visual_fast_edit.fly_form.create(
                [
                    {
                        $: $('<input />'),
                        key: 'path'
                    }
                ],
                function (values)
                {
                    $.ajax(
                        {
                            url: '/ajax',
                            data: {
                                class: 'MBCMS\\GetAllTemplates\\folder_actions->transfer',
                                newPath: values.path,
                                oldFullPath: $this.attr('fullPath')
                            },
                            success: function (msg)
                            {
                                if (msg != 'false')
                                    $this.parents('.template-folder:first').remove();
                            }
                        });
                }
            );

            return false;
        })
    ;
};

mbcms.template.init_transfer_template = function (html)
{

    var templates = html.find('.boxes-right-ul');

    templates.each(function ()
    {
        $(this)
            .find('#transfer_template')
            .click(function ()
            {
                var $this = $(this);
                mbcms.visual_fast_edit.fly_form.create(
                    [
                        {
                            $: $('<input />'),
                            key: 'path'
                        }
                    ],
                    function (values)
                    {
                        $.ajax(
                            {
                                url: '/ajax',
                                data: {
                                    class: 'MBCMS\\template->transfer',
                                    path: values.path,
                                    id: $this.attr('idtemplate')
                                },
                                success: function (msg)
                                {
                                    var req = get_req(msg);
                                    if (req.transfer)
                                        $this.parents('.boxes-right-ul:first').remove();
                                }
                            });
                    }
                );
            })
        ;
    });
};

/**
 *
 * @param {jQuery} html
 * @returns {undefined}
 */
mbcms.template.init_folder_rename = function (html)
{
    html
        .find('.ico.pen')
        .click(function ()
        {
            var $this = $(this);
            mbcms.visual_fast_edit.fly_form.create(
                [
                    {
                        $: $('<input />').text($(this).parents('.template-folder:first').find('.name:first').text()),
                        key: 'name',
                        req: true
                    }
                ],
                function (values)
                {
                    $.ajax(
                        {
                            url: '/ajax',
                            data: {
                                class: 'MBCMS\\GetAllTemplates\\folder_actions->rename',
                                name: values.name,
                                fullPath: $this.attr('fullPath')
                            },
                            success: function ()
                            {
                                if ($.trim(values.name) != '')
                                {
                                    $this.parents('.template-folder:first').find('.name:first').text(values.name);
                                    var path = $this.parents('.template-folder:first').attr('path');
                                    var fullPath = (path + '/' + values.name).replace('//', '/');
                                    $this.parents('.template-folder:first').attr('fullPath', fullPath);
                                }
                            }
                        });
                });
            return false;
        })
    ;
};

//
//
//
/////////////////////// inits get all templates //////////////////////////////////