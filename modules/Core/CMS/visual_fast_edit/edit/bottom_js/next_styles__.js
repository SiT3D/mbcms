
mbcms.visual_fast_edit.next_styles = function ()
{

};


mbcms.visual_fast_edit.next_styles.create_icons = function (fast_edit_data, ico_css)
{
    this.__fast_edit_data = fast_edit_data;

    var self = this;

    self.__ico = $('<div />')
            .addClass('mbcms_visual_fast_edit_ico ico pen')
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

mbcms.visual_fast_edit.next_styles.__destroy_tools = function ()
{
    for (var i in this.__els)
    {
        this.__els[i].remove();
    }
};

mbcms.visual_fast_edit.next_styles.__create_tools = function ()
{
    this.__tools = true;

    this.__els = {};

    var data = this.__fast_edit_data;

    this.__els.__box_shadow = $('<input type="text" />')
            .css({position: 'fixed', bottom: 90, left: 100})
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_options(data, 'box_shadow'))
            .attr('placeholder', 'Тени')
            ;

    this.__els.__text_shadow = $('<input type="text" />')
            .css({position: 'fixed', bottom: 90, left: 350})
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_options(data, 'text_shadow'))
            .attr('placeholder', 'Тени текста')
            ;

    this.__els.__outline = $('<input type="text" />')
            .css({position: 'fixed', bottom: 130, left: 100})
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_options(data, 'outline'))
            .attr('placeholder', 'outline')
            ;

    this.__els.__cursor = $('<input type="text" />')
            .css({position: 'fixed', bottom: 130, left: 350})
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_options(data, 'cursor'))
            .attr('placeholder', '__cursor')
            ;

    this.__els.__box_sizing = $('<input type="text" />')
            .css({position: 'fixed', bottom: 90, left: 600})
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_options(data, 'box_sizing'))
            .attr('placeholder', '__box_sizing content-box')
            ;

    this.__els.__text_decoration = $('<input type="text" />')
            .css({position: 'fixed', bottom: 130, left: 600})
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_options(data, 'text_decoration'))
            .attr('placeholder', 'text_decoration')
            ;

    this.__old_settings = this.__get_settings();

};

mbcms.visual_fast_edit.next_styles.__show_tools = function (show)
{
    if (show)
    {
        for (var i in this.__els)
        {
            this.__els[i].show();
        }
    }
    else
    {
        for (var i in this.__els)
        {
            this.__els[i].hide();
        }

        this.__save();
    }
};

mbcms.visual_fast_edit.next_styles.__save = function ()
{

    var data = this.__fast_edit_data;

    console.log(data.parent_class);
    console.log(data.current_class);

    option.standart_template_settings.update_styles(data.idTemplate, data.parent_class, data.current_class,
            this.__get_settings(), null, this.__old_settings);

    this.__old_settings = this.__get_settings();
};


mbcms.visual_fast_edit.next_styles.__get_settings = function ()
{
    return {
        box_shadow: this.__els.__box_shadow.val(),
        text_shadow: this.__els.__text_shadow.val(),
        outline: this.__els.__outline.val(),
        cursor: this.__els.__cursor.val(),
        box_sizing: this.__els.__box_sizing.val(),
        text_decoration: this.__els.__text_decoration.val(),
    };
};

