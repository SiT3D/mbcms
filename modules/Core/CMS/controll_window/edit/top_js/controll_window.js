MBCMS_CONTROLL_TAB_WINDOW = 'MBCMS_CONTROLL_TAB_WINDOW';

event.controll_window = function ()
{
    this.__key = 'event.controll_window'; // уникальный идентификатор, прсто название класса!
};
event.controll_window.prototype = Object.create(event.prototype);

event.controll_window.load = function ()
{
    this.__key = 'event.controll_window.load'; // уникальный идентификатор, прсто название класса!
};
event.controll_window.load.prototype = Object.create(event.prototype);

mbcms.controll_window = function ()
{

};

mbcms.controll_window.__ready_callbacks = [];

mbcms.controll_window.get = function ()
{
    return $('#MBCMS_CONTROLL_TAB_WINDOW');
};

mbcms.controll_window.have_ids = {};

mbcms.controll_window.load = function (callback)
{
    var data = {
        class: 'MBCMS\\controll_window->ajax',
        adr: location.href
    };
    data[mbcms.ADMIN_STATUS] = true;

    var __params = location.search.substr(1, location.search.length);
    __params = __params.split('&');

    for (var i in __params)
    {
        var element = __params[i].split('=');
        data[$.trim(element[0])] = $.trim(element[1]);
    }

    $.ajax(
        {
            url: '/ajax',
            type: 'GET',
            data: data,
            success: function (msg)
            {
                mbcms.visual_fast_edit.destroy();
                mbcms.controll_window.get().remove();
                $('.xdebug-var-dump').remove();
                //load css
                var $controll_window = $(msg);
                $controll_window.appendTo('#admin_modules_content');

                mbcms.controll_window.get()
                    .find('[idtemplate]')
                    .each(function ()
                    {
                        var id = $(this).attr('idtemplate');
                        mbcms.dinamic_js_css_loader.load('', id);
                    });

                mbcms.controll_window.get()
                    .find('[connect_type=__cms_connect_type_OUTPUT][__user_cms_out_title="БР"]')
                    .each(function ()
                    {
                        $(this).addClass('btn');
                    });

                option.fast_edit_init_controll_window($controll_window);

                mbcms.visual_fast_edit.__fade($controll_window.find('[__produc_hidden=true]'), false, 0);


                var evt = new event.controll_window.load().call();

                if (typeof callback == 'function')
                    callback.call(callback);
            }
        });
};

mbcms.controll_window.__inits = function ($controll_window)
{
    $controll_window
        .appendTo('#admin_modules_content')
    ;
};

new event.site.load().listen(function ()
{
    mbcms.controll_window.load();
});


new event.controll_window.load().listen(function ()
{
    mbcms.controll_window.get()
        .find('a, input, button, [contenteditable=true]')
        .css({pointerEvents: 'none'})
    ;

    $('body')
        .find('a, input, button, [contenteditable=true]')
        .css({pointerEvents: 'none'})
    ;

});



