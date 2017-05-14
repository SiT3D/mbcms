


mbcms.visual_fast_edit.clone = function ()
{

};

mbcms.visual_fast_edit.clone.create_icons = function (fast_edit_data, ico_css)
{

    var self = this;
    this.__fast_edit_data = fast_edit_data;

    self.__ico = $('<div />')
            .addClass('mbcms_visual_fast_edit_ico ico clone')
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

mbcms.visual_fast_edit.clone.__destroy_tools = function ()
{
    for (var i in this.__elements)
    {
        this.__elements[i].remove();
    }
};

mbcms.visual_fast_edit.clone.__create_tools = function ()
{
    this.__elements = {};
    var self = this;
    var data = this.__fast_edit_data;

    this.__elements.input = $('<input />')
            .attr('type', 'number')
            .change(function ()
            {
                // тут пересоздание
                self.__create_opacity_clones();
            })
            .appendTo('body')
            .css
            (
                    {
                        position: 'fixed',
                        left: '40%',
                        bottom: 100,
                        width: 100,
                    }
            )
            .addClass('mbcms-bootstrap')
            ;

    this.__elements.btn = $('<button />')
            .text('Clone')
            .click(function ()
            {
                // add struct clone
                self.__add_in_struct();
            })
            .appendTo('body')
            .css
            (
                    {
                        position: 'fixed',
                        left: '60%',
                        bottom: 100,
                    }
            )
            .addClass('mbcms-bootstrap btn')
            ;

    this.__elements.copy = $('<button />')
            .text('Copy')
            .click(function ()
            {
                self.__$copy = data.this;
            })
            .appendTo('body')
            .css
            (
                    {
                        position: 'fixed',
                        left: '30%',
                        bottom: 150,
                    }
            )
            .addClass('mbcms-bootstrap btn')
            ;

    this.__elements.paste = $('<button />')
            .text('Paste')
            .click(function ()
            {
                function __paste()
                {
                    if (self.__$copy.attr('connect_type') == '__cms_connect_type_TEMPLATE')
                        return self.__paste_template();
                    else
                        return self.__paste_output();
                }

                var clone = self.__$copy.clone(true);
                clone.find('[parentidtemplate=' + clone.attr('parentidtemplate') + ']').attr('parentidtemplate', data.idTemplate);
                clone.attr('parentidtemplate', data.idTemplate);

                if (data.this.attr('connect_type') == '__cms_connect_type_TEMPLATE')
                {
                    if (__paste() != false)
                        mbcms.visual_fast_edit.append_new_visual_block(clone, data.this);
                }
                else
                {
                    if (__paste() != false)
                        mbcms.visual_fast_edit.append_new_visual_block(clone, data.parent);
                }
            })
            .appendTo('body')
            .css
            (
                    {
                        position: 'fixed',
                        left: '50%',
                        bottom: 150,
                    }
            )
            .addClass('mbcms-bootstrap btn')
            ;
};

mbcms.visual_fast_edit.clone.__paste_template = function ()
{
    var self = this;
    var data = this.__fast_edit_data;

    mbcms.template.add(data.idTemplate, self.__$copy.attr('idtemplate'), function ()
    {
        mbcms.controll_window.load();
//        mbcms.template.reload_views(data.idTemplate);
    });
};

mbcms.visual_fast_edit.clone.__paste_output = function ()
{
    var self = this;
    var data = this.__fast_edit_data;
    var copy_idTemplate_data = option.get_fast_edit_data(self.__$copy.parents('[idtemplate]:first'));


    if (data.idTemplate == copy_idTemplate_data.idTemplate)
    {
        return false;
    }


    mbcms.visual_fast_edit.load_options(copy_idTemplate_data, function (msg)
    {
        var outputs = [];
        var req = get_req(msg);
        var index = self.__$copy.attr('__cms_output_index');
        var output = mbcms.visual_fast_edit.get_output(req, index);
        outputs.push(output);

        mbcms.visual_fast_edit.get_parentidtemplate_childrens(copy_idTemplate_data).each(function ()
        {
            var output = mbcms.visual_fast_edit.get_output(req, $(this).attr('__cms_output_index'));
            if (isset(output, 'data', '__user_cms_parent_output_index'))
                if (output.data.__user_cms_parent_output_index == index)
                {
                    outputs.push(output);
                }
        });

        mbcms.output.add_array(data.idTemplate, outputs);
    });

};

mbcms.visual_fast_edit.clone.__show_tools = function (show)
{
    if (show)
    {
        for (var i in this.__elements)
        {
            this.__elements[i].show();
        }

        this.__create_opacity_clones();
    }
    else
    {
        for (i in this.__elements)
        {
            this.__elements[i].hide();
        }
        this.__remove_clones();
    }
};

mbcms.visual_fast_edit.clone.__add_in_struct = function ()
{
    var data = this.__fast_edit_data;

    var count = isNaN(parseInt(this.__elements.input.val())) ? 0 : parseInt(this.__elements.input.val());

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
//                        mbcms.controll_window.load();
                        if (isset(req, 'new_idTemplate'))
                        {
                            for (var i in req.new_idTemplate)
                            {
                                mbcms.dinamic_js_css_loader.load('', req.new_idTemplate[i]);
                            }
                        }
                        mbcms.controll_window.load();
//                        mbcms.template.reload_views(parentIdTemplate);
                    }
                });
    }


};

mbcms.visual_fast_edit.clone.__remove_clones = function ()
{
    $('.mbcms_visual_fast_edit-opacity_clone').remove();
};

mbcms.visual_fast_edit.clone.__create_opacity_clones = function ()
{
    this.__remove_clones();

    var count = this.__elements.input.val();
    count = isNaN(parseInt(count)) ? 0 : parseInt(count);
    count = count < 0 ? 0 : count;

    for (var i = 0; i < count; i++)
    {
        var clone = this.__fast_edit_data.this.clone(true);
        clone
                .css({opacity: 0.4})
                .addClass('mbcms_visual_fast_edit-opacity_clone')
                ;

        mbcms.visual_fast_edit.append_new_visual_block(clone, this.__fast_edit_data.this.parent());
    }
};


