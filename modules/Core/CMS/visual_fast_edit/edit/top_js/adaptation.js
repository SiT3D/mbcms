
mbcms.visual_fast_edit.adaptation = function ()
{
};

mbcms.visual_fast_edit.adaptation.atype = null;
mbcms.visual_fast_edit.adaptation.__on = false;


mbcms.visual_fast_edit.adaptation.get_size = function ()
{
    if ($('#__mbcms__media_screen-adaptation_frame').length != 0)
    {
        return 'max-width: ' + parseInt(parseInt($('#__mbcms__media_screen-adaptation_frame').css('width')) + 1) + 'px';
    }

    return 'standart_screen_size_fulscreen';
};

mbcms.visual_fast_edit.adaptation.activator = function (on)
{
    this.__on = !this.__on;
    this.__on = on != undefined ? on : this.__on;

    if (this.__on)
    {
        var activator = $('<div/>')
                .css({position: 'fixed', left: 0, top: 25,
                    width: 50, height: 20, background: '#000', zIndex: 12, opacity: 0.2, color: '#fff', padding: '5px'})
                .appendTo('body')
                .prop('id', 'mbcms_adapatation_active')
                .text('Screen')
                .plugin_select(
                        {
                            v:
                                    [
                                        ['300', 'phone'],
                                        ['500', 'phone_land'],
                                        ['800', 'phone_big'],
                                    ],
                            callback: function (values)
                            {
                                var value = values[0];

                                if (value == 'null')
                                {
                                    mbcms.controll_window.load();
                                    activator.text('Screen');

                                    return;
                                }

                                var w = values[0];

                                mbcms.controll_window.load(function ()
                                {
                                    var content = $('html');
                                    mbcms.visual_fast_edit.adaptation.__frame_admin(content, w);
                                });

                                activator.text(w + 'px');
                            }
                        })
                ;
    }
    else
    {
        $('#mbcms_adapatation_active').remove();
        $('body').css({width: '100%'});
        mbcms.controll_window.load();
    }
};

mbcms.visual_fast_edit.adaptation.__frame_admin = function (content, width)
{
    var controll_window = mbcms.controll_window.get();
    var clone = content.clone(true);
    controll_window.empty();
    var frame = $('<iframe id="__mbcms__media_screen-adaptation_frame" />')
            .appendTo(controll_window).css({width: width, height: $('body').height() - 50, margin: '30px auto', display: 'block'});

    var html = frame.contents().find('html');
    html.find('head:first').remove();
    html.find('body:first').remove();

    clone.find('head').appendTo(html);
    clone.find('body').appendTo(html);
    html.find('#mbcms_adapatation_active').remove();
};

