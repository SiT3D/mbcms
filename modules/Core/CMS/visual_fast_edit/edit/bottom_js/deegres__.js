


mbcms.visual_fast_edit.deegres = function ()
{

};


mbcms.visual_fast_edit.deegres.create_icons = function (fast_edit_data, ico_css)
{
    this.__fast_edit_data = fast_edit_data;

    var self = this;

    self.__ico = $('<div />')
            .addClass('mbcms_visual_fast_edit_ico ico treangle_deegres')
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

mbcms.visual_fast_edit.deegres.__destroy_tools = function ()
{
    this.__body.remove();
};

mbcms.visual_fast_edit.deegres.__create_tools = function ()
{
    var data = this.__fast_edit_data;
    this.__init_transform_matrix();
    this.__tools = true;

    this.__body = $('<div />')
            .css
            (
                    {
                        position: 'fixed',
                        left: '50%',
                        top: '50%',
                        marginLeft: -100,
                        marginTop: -100,
                        width: 200,
                        height: 200,
                        border: '2px dashed #000',
                        boxShadow: '0 0 2px 3px #fff inset',
                    }
            )
            .appendTo('body')
            ;

    mbcms.visual_fast_edit.axis.add(this.__fast_edit_data, 'middle', 'transform-rotate',
            {
                v: [['deg', 'Deg']],
                value: this.__transform_values.rotation,
                change: function (value)
                {
                    mbcms.visual_fast_edit.get_targets(data).css('transform', 'rotate(' + value + ')');
                }
            }).appendTo(this.__body);

    this.__old_settings = this.__get_settings();
};

mbcms.visual_fast_edit.deegres.__init_transform_matrix = function ()
{
    this.__transform_values = {};
    var allTransform = mbcms.visual_fast_edit.get_options(this.__fast_edit_data, 'transform');
    var cosValues = allTransform.match(/([\d\.]*)[,\)]/g);
    if (isset(cosValues, '5'))
    {
        var a = cosValues[0].replace(/[,\)]/, '');
        var b = cosValues[1].replace(/[,\)]/, '');
        var c = cosValues[2].replace(/[,\)]/, '');
        var d = cosValues[3].replace(/[,\)]/, '');
        var tx = cosValues[4].replace(/[,\)]/, '');
        var ty = cosValues[5].replace(/[,\)]/, '');

        //// rotation
        var deegres = Math.round(Math.acos(a) * 180 / Math.PI);
        this.__transform_values.rotation = deegres + 'deg';
    }

};

mbcms.visual_fast_edit.deegres.__show_tools = function (show)
{
    if (show)
    {
        this.__body.show();
    }
    else
    {
        this.__body.hide();

        this.__save();
    }
};

mbcms.visual_fast_edit.deegres.__save = function ()
{
    var data = this.__fast_edit_data;
    option.standart_template_settings.update_styles(data.idTemplate, data.parent_class, data.current_class,
            this.__get_settings(), null, this.__old_settings);

    this.__old_settings = this.__get_settings();
};

mbcms.visual_fast_edit.deegres.__get_settings = function ()
{
    var data = this.__fast_edit_data;

    return {
        transform: mbcms.visual_fast_edit.get_targets(data, true).css('transform'),
        _moz_transform: mbcms.visual_fast_edit.get_targets(data, true).css('transform'),
        _webkit_transform: mbcms.visual_fast_edit.get_targets(data, true).css('transform'),
        _o_transform: mbcms.visual_fast_edit.get_targets(data, true).css('transform'),
        _ms_transform: mbcms.visual_fast_edit.get_targets(data, true).css('transform'),
    };
};
