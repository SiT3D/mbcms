

mbcms.visual_fast_edit.background = function ()
{

};


mbcms.visual_fast_edit.background.create_icons = function (fast_edit_data, ico_css)
{
    this.__fast_edit_data = fast_edit_data;

    var self = this;

    self.__ico = $('<div />')
            .addClass('mbcms_visual_fast_edit_ico ico background')
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

mbcms.visual_fast_edit.background.__show_tools = function (show)
{
    if (show)
    {
        this.__color_picker.show();
        this.__color_picker_remove.show();
        this.__repeat.show();
        this.__size.show();
        this.__position.show();
        this.__opacity.show();
        this.__btn.show();

        this.__create_controller();
    }
    else
    {
        this.__color_picker.hide();
        this.__color_picker_remove.hide();
        this.__repeat.hide();
        this.__size.hide();
        this.__position.hide();
        this.__opacity.hide();
        this.__btn.hide();

        this.__destroy_controller();

        this.__save();
    }
};

mbcms.visual_fast_edit.background.__save = function ()
{
    var data = this.__fast_edit_data;

    option.standart_template_settings.update_styles(data.idTemplate, data.parent_class, data.current_class,
            this.__get_settings(), null, this.__old_settings);
    this.__old_settings = this.__get_settings();
};

mbcms.visual_fast_edit.background.__get_settings = function ()
{
    var data = this.__fast_edit_data;

    this.__background_color = typeof (this.__background_color) == 'undefined'
            ? mbcms.visual_fast_edit.get_targets(data, true).css('background-color') : this.__background_color;
    this.__image_url = typeof (this.__image_url) == 'undefined'
            ? mbcms.visual_fast_edit.get_targets(data, true).css('background-image') : this.__image_url;

    return {
        background_image: this.__image_url,
        background_color: this.__background_color,
        background_size: mbcms.visual_fast_edit.get_targets(data, true).css('background-size'),
        background_repeat: mbcms.visual_fast_edit.get_targets(data, true).css('background-repeat'),
        opacity: mbcms.visual_fast_edit.get_targets(data, true).css('opacity'),
        background_position: mbcms.visual_fast_edit.get_targets(data, true).css('background-position')
    };
};

mbcms.visual_fast_edit.background.__destroy_tools = function ()
{
    this.__color_picker.remove();
    this.__color_picker_remove.remove();
    this.__repeat.remove();
    this.__size.remove();
    this.__position.remove();
    this.__opacity.remove();
    this.__btn.remove();
};

mbcms.visual_fast_edit.background.__destroy_controller = function ()
{
    this.__controller.remove();
};

mbcms.visual_fast_edit.background.__create_controller = function ()
{
    var data = this.__fast_edit_data;
    var trg = mbcms.visual_fast_edit.get_targets(data, true);
    var sizes = this.__get_bg_sizes();
    var pos = this.__get_bg_position();
    var self = this;

    this.__background_color = undefined;
    this.__image_url = undefined;

    this.__controller = $('<div />')
            .css
            (
                    {
                        poistion: 'absolute',
                        width: sizes.width,
                        height: sizes.height,
                        left: pos.left,
                        top: pos.top,
                        border: '1px solid #000'
                    }
            )
            .appendTo(trg)
            .resizable(
                    {
                        resize: function (a, u)
                        {
                            trg.css('background-size', u.size.width + 'px ' + u.size.height + 'px');
                            self.__size.val(trg.css('background-size'));
                        }
                    })
            .draggable(
                    {
                        drag: function (a, u)
                        {
                            trg.css('background-position', u.position.left + 'px ' + u.position.top + 'px');
                            self.__position.val(trg.css('background-position'));
                        }
                    })
            ;
};

mbcms.visual_fast_edit.background.__get_bg_sizes = function ()
{
    var size = mbcms.visual_fast_edit.get_targets(this.__fast_edit_data, true).css('background-size');
    size = $.trim(size) === '' ? 'auto' : size;
    var sizes = size.split(' ');
    var ret = {};
    ret.width = sizes[0];
    ret.height = isset(sizes[1]) ? sizes[1] : sizes[0];
    return ret;
};

mbcms.visual_fast_edit.background.__get_bg_position = function ()
{
    var position = mbcms.visual_fast_edit.get_targets(this.__fast_edit_data, true).css('background-position');
    position = $.trim(position) === '' ? 'auto' : position;
    var positions = position.split(' ');
    var ret = {};
    ret.left = positions[0];
    ret.top = isset(positions[1]) ? positions[1] : positions[0];
    return ret;
};

mbcms.visual_fast_edit.background.__create_tools = function ()
{
    var color = this.__fast_edit_data.this.css('background-color');
//    var color = mbcms.visual_fast_edit.rgb_to_hex(val);
    var data = this.__fast_edit_data;
    this.__tools = true;
    var self = this;

    this.__color_picker = $('<input type="text" />')
            .css({position: 'fixed', top: '80%', left: '50%', width: 20, background: color, color: color, cursor: 'pointer'})
            .appendTo($('body'))
            .addClass('mbcms-bootstrap')
            .colorpicker
            (
                    {
                        parts: 'full',
                        alpha: true,
                        colorFormat: 'RGBA',
                        ok: function (a, b)
                        {
                            mbcms.visual_fast_edit.get_targets(data).css('background-color', b.formatted);
                            self.__background_color = b.formatted;
                            self.__color_picker.css({background: b.formatted, color: b.formatted});
                        },
                        select: function (a, b)
                        {
                            mbcms.visual_fast_edit.get_targets(data).css('background-color', b.formatted);
                            self.__background_color = b.formatted;
                            self.__color_picker.css({background: b.formatted, color: b.formatted});
                        }
                    }
            )
            .click(function ()
            {
                return false;
            })
            .val(color)
            ;


    this.__color_picker_remove = $('<button />')
            .text('clear')
            .css({position: 'fixed', top: '80%', left: '60%'})
            .addClass('mbcms-bootstrap btn')
            .click(function ()
            {
                mbcms.visual_fast_edit.get_targets(data).css('background-color', 'transparent');
                self.__background_color = 'destroy';

                return false;
            })
            .appendTo($('body'))
            ;


    this.__repeat = $('<input type="text" />')
            .css(
                    {
                        position: 'fixed',
                        left: '30%',
                        top: '60%'
                    })
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_targets(data, true).css('background-repeat'))
            .change(function ()
            {
                mbcms.visual_fast_edit.get_targets(data).css('background-repeat', $(this).val());
            })
            ;

    this.__size = $('<input type="text" />')
            .css(
                    {
                        position: 'fixed',
                        left: '30%',
                        top: '50%'
                    })
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_targets(data, true).css('background-size'))
            .change(function ()
            {
                mbcms.visual_fast_edit.get_targets(data).css('background-size', $(this).val());
            })
            ;

    this.__position = $('<input type="text"/>')
            .css(
                    {
                        position: 'fixed',
                        left: '30%',
                        top: '55%'
                    })
            .addClass('mbcms-bootstrap')
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_targets(data, true).css('background-position'))
            .change(function ()
            {
                mbcms.visual_fast_edit.get_targets(data).css('background-position', $(this).val());
            })
            ;

    this.__opacity = $('<input />')
            .css(
                    {
                        position: 'fixed',
                        left: '30%',
                        top: '65%'
                    })
            .addClass('mbcms-bootstrap')
            .prop('type', 'range')
            .attr('min', 0)
            .attr('max', 1)
            .attr('step', 0.1)
            .appendTo('body')
            .val(mbcms.visual_fast_edit.get_targets(data, true).css('opacity'))
            .change(function ()
            {
                mbcms.visual_fast_edit.get_targets(data).css('opacity', $(this).val());
            })
            ;

    this.__btn = $('<button />')
            .css
            (
                    {
                        position: 'fixed',
                        left: '30%',
                        top: '70%'
                    }
            )
            .addClass('mbcms-bootstrap btn')
            .text('Images')
            .click(function ()
            {
            })
            .appendTo('body')
            ;

    this.__create_controller();

    this.__old_settings = this.__get_settings();
};