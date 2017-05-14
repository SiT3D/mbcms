


mbcms.visual_fast_edit.view = function ()
{

};


mbcms.visual_fast_edit.view.create_icons = function (fast_edit_data, ico_css)
{
    this.__fast_edit_data = fast_edit_data;

    var self = this;

    self.__ico = $('<div />')
            .addClass('mbcms_visual_fast_edit_ico ico view')
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

mbcms.visual_fast_edit.view.__destroy_tools = function ()
{
    for (var i in this.__elements)
    {
        this.__elements[i].remove();
    }
};

mbcms.visual_fast_edit.view.__create_tools = function ()
{
    this.__tools = true;
    var data = this.__fast_edit_data;

    this.__elements = {};

    this.__elements.__view = $('<button/>')
            .text('View')
            .addClass('btn mbcms-bootstrap')
            .css({position: 'fixed', bottom: 90, right: '35%'})
            .appendTo('body')
            .click(function ()
            {
                var uri = '/' + location.host + '/ajax?class=MBCMS\\template->redirect_preview&idTemplate=' + data.idTemplate;
                window.open(uri, '_blank');
            })
            ;

    this.__elements.__edit = $('<button/>')
            .text('Edit')
            .addClass('btn mbcms-bootstrap')
            .css({position: 'fixed', bottom: 90, right: '30%'})
            .appendTo('body')
            .click(function ()
            {
                var uri = '/' + location.host + '/ajax?class=MBCMS\\template->redirect_preview&idTemplate=' + data.idTemplate + '&edit=true';
                window.open(uri, '_blank');
            })
            ;
};

mbcms.visual_fast_edit.view.__show_tools = function (show)
{
    if (show)
    {
        for (var i in this.__elements)
        {
            this.__elements[i].show();
        }
    }
    else
    {
        for (var i in this.__elements)
        {
            this.__elements[i].hide();
        }
    }
};


