


mbcms.visual_fast_edit.__out_options = function ()
{

};


mbcms.visual_fast_edit.__out_options.create_icons = function (fast_edit_data, ico_css)
{
    this.__fast_edit_data = fast_edit_data;

    var self = this;


    self.__ico = $('<div />')
            .addClass('mbcms_visual_fast_edit_ico ico database-key-ico')
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

mbcms.visual_fast_edit.__out_options.__destroy_tools = function ()
{
    this.__text.remove();
};

mbcms.visual_fast_edit.__out_options.__create_tools = function ()
{
    var data = this.__fast_edit_data;
    this.__tools = true;
    var outputs = mbcms.visual_fast_edit.get_options(this.__fast_edit_data, 'outputs');

    this.__text = $('<textarea />')
            .css({position: 'fixed', top: '60%', left: '15%', width: '70%'})
            .addClass('mbcms-bootstrap')
            .prop('id', '__text_db_key_out')
            .val(isset(outputs, data.out_index, 'data', '__db_text') ? outputs[data.out_index].data.__db_text : '')
            .appendTo('body')
            ;
};

mbcms.visual_fast_edit.__out_options.__show_tools = function (show)
{
    if (show)
    {
        this.__text.show();
    }
    else
    {
        this.__text.hide();

        this.__save();
    }
};

mbcms.visual_fast_edit.__out_options.__save = function ()
{
    var data = this.__fast_edit_data;

    var savedata = {__db_text: this.__text.val()};

    mbcms.output.update(data.idTemplate, data.out_index, savedata);
};