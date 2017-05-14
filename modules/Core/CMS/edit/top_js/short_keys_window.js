event.shortkey = function ()
{
    this.__key = 'event.shortkey'; // уникальный идентификатор, прсто название класса!
};
event.shortkey.prototype = Object.create(event.prototype);

event.shortkey.mousewheel = function ()
{
    this.__key = 'event.shortkey.mousewheel'; // уникальный идентификатор, прсто название класса!

    /**
     * Это jQuery событие мышки
     */
    this.e = null;

    /**
     * Движение верх/низ true|false
     */
    this.mwheelup = true;

    /**
     * Сила воздействия на 1 единицу применения
     */
    this.zoom = 1;

    /**
     * Опция которая отрабатывает событие
     */
    this.trg = null;

    /**
     * Просто название для определения текущего ивента
     */
    this.event_name = 'mousewheel';

};
event.shortkey.mousewheel.prototype = Object.create(event.prototype);

event.shortkey.keydown = function ()
{
    this.__key = 'event.shortkey.keydown'; // уникальный идентификатор, прсто название класса!

    /**
     * Событие будет вызываться дважды. Нужно чтобы слушатели могли определить синхронный это запуск или асинхронный
     */
    this.async = true;
    this.e = null;
    this.trg = null;
    this.clear = null;

    this.set_async_false = function ()
    {
        this.async = false;
        return this;
    };

    this.set_data = function (e, trg, clear)
    {
        this.e = e;
        this.trg = trg;
        this.clear = clear;

        return this;
    };

    /**
     * Что вернет событие нажатия, true всплытие, false запрет всплытия
     */
    this.return = true;
};
event.shortkey.keydown.prototype = Object.create(event.prototype);

(function ()
{

    var short = function (e)
    {
        if (mbcms.visual_fast_edit.fly_edit.get_trg() || mbcms.visual_fast_edit.fly_edit.blocked() || $(':focus').length > 0)
        {
            return;
        }

        for (var i in window)
        {
            if (i == 'webkitStorageInfo' || i == 'webkitIndexedDB')
            {
                continue;
            }

            if (e.keyCode == window[i] && i == i.toUpperCase())
            {
                if (i != 'KEY_ALT' && i != 'KEY_CTRL' && i != 'KEY_SHIFT')
                {
                    var nots = ':not(.KEY_ALT):not(.KEY_SHIFT):not(.KEY_CTRL)';
                    nots = e.shiftKey ? '.KEY_SHIFT' + nots.replace(':not(.KEY_SHIFT)', '') : nots;
                    nots = e.ctrlKey ? '.KEY_CTRL' + nots.replace(':not(.KEY_CTRL)', '') : nots;
                    nots = e.altKey ? '.KEY_ALT' + nots.replace(':not(.KEY_ALT)', '') : nots;

                    var selector = '.' + i + nots;
                    var trg = $(selector + ':first');

                    if (trg.length > 0)
                    {
                        mbcms.visual_fast_edit.__set_active(trg);
                    }
                }
            }
        }
    };

    $(document)
        .on('mousewheel', function (e)
        {
            var up = e.originalEvent.wheelDelta / 120 > 0 ? true : false;
            var trg = mbcms.visual_fast_edit.fly_edit.get_trg();

            var evt = new event.shortkey.mousewheel().call();
            evt.e = e;
            evt.trg = trg;
            evt.mwheelup = up;

            if (mbcms.visual_fast_edit.fly_edit.__trg) // тут тоже переделать на асинхронку!! просто все слушатели должны определяться при вызове скрипта!
            {
                return false;
            }
        })
        .keydown(function (e)
        {
            var evt = null;

            if (((e.keyCode === KEY_ENTER && e.ctrlKey && !e.shiftKey)))
            {
                mbcms.visual_fast_edit.__set_active($('.mbcms_visual_fast_edit_ico.active:first'));
            }
            else if (mbcms.visual_fast_edit.fly_edit.get_trg())
            {
                var trg = mbcms.visual_fast_edit.fly_edit.get_trg();

                if (isset(trg, '__timer'))
                {
                    clearTimeout(trg.__timer);
                }

                var clear = trg.__timer ? false : true;

                trg.__timer = setTimeout(function ()
                {
                    trg.__timer = null;
                }, 1000);

                if (e.keyCode == KEY_UP)
                {
                    evt = new event.shortkey.mousewheel().call();
                    evt.e = e;
                    evt.trg = trg;
                    evt.zoom = 2;
                }
                else if (e.keyCode == KEY_DOWN)
                {
                    evt = new event.shortkey.mousewheel().call();
                    evt.e = e;
                    evt.trg = trg;
                    evt.mwheelup = false;
                    evt.zoom = 2;
                }
                else if (e.keyCode == KEY_TAB && e.shiftKey)
                {
                    mbcms.visual_fast_edit.fly_edit.get_next(false);
                }
                else if (e.keyCode == KEY_TAB)
                {
                    mbcms.visual_fast_edit.fly_edit.get_next(true);
                }
                else if ((e.keyCode == KEY_F1
                    || e.keyCode == KEY_F2
                    || e.keyCode == KEY_F3
                    || e.keyCode == KEY_F4
                    || e.keyCode == KEY_F5
                    || e.keyCode == KEY_F6
                    || e.keyCode == KEY_F7
                    || e.keyCode == KEY_F8
                    || e.keyCode == KEY_F9
                    || e.keyCode == KEY_F10) && mbcms.visual_fast_edit.fly_edit.get_trg())
                {
                    mbcms.visual_fast_edit.fly_edit.select_group(e);
                    return false;
                }

                evt = new event.shortkey.keydown().set_async_false().set_data(e, trg, clear).call(false);

                return evt.return;
            }
        })
        .keyup(function (e)
        {
            short(e);

            if (e.keyCode === KEY_ESC)
            {
                if (mbcms.visual_fast_edit.fly_edit.get_trg())
                {
                    mbcms.visual_fast_edit.fly_edit.__this.deactivation_option();
                }
                else if ($('.mbcms_visual_fast_edit_ico.active:first').length > 0)
                {
                    mbcms.visual_fast_edit.__set_active($('.mbcms_visual_fast_edit_ico.active:first'));
                }
                else if (mbcms.visual_fast_edit.is_panel())
                {
                    mbcms.visual_fast_edit.destroy();
                }
                else if (!mbcms.visual_fast_edit.adaptation.__on)
                {
                    mbcms.controll_window.load();
                }
            }

            if ($(':focus').length > 0 || !mbcms.visual_fast_edit.resolution || mbcms.visual_fast_edit.fly_edit.get_trg())
            {
                return false;
            }

            if (e.keyCode === KEY_L)
            {
                mbcms.visual_fast_edit.adaptation.activator();
            }
            else if (e.keyCode === KEY_N && e.shiftKey)
            {
                var data = mbcms.visual_fast_edit.get_current_data();
                var idTemplate = isset(data, 'idTemplate') ? data.idTemplate : '';
                mbcms.template.add_new(idTemplate);
            }
            else if (e.keyCode === KEY_V)
            {
                mbcms.visual_fast_edit.activate_blocks_gradient();
            }
            else if (e.keyCode === KEY_N)
            {

                var data = mbcms.visual_fast_edit.get_current_data();
                var odata = {
                    __user_cms_class: 'C' + parseInt(Math.random() * 1000),
                    __user_cms_out_title: 'БР',
                    __user_cms_parent_output_index: data.__cms_output_index || null
                };

                mbcms.output.add(data.idTemplate, 'MBCMS\\out', odata,
                    function ()
                    {
                        mbcms.controll_window.load(function ()
                        {
                            mbcms.visual_fast_edit.create(data);
                        });
                    });
            }
        });


})();
