event.visual_fast_edit.main_option = function ()
{
    this.__key = 'event.visual_fast_edit.main_option'; // уникальный идентификатор, прсто название класса!
};
event.visual_fast_edit.main_option.prototype = Object.create(event.prototype);

/**
 * Событие изменения значения в опции
 *
 * @returns {event.visual_fast_edit.main_option.go}
 */
event.visual_fast_edit.main_option.go = function ()
{
    this.__key = 'event.visual_fast_edit.main_option.go'; // уникальный идентификатор, прсто название класса!
    this.e = null;
};
event.visual_fast_edit.main_option.go.prototype = Object.create(event.prototype);


mbcms.visual_fast_edit.main_option = function ()
{

};

mbcms.visual_fast_edit.main_option.__current_metric_find_str = '';

mbcms.visual_fast_edit.main_option.__is_metric = function (metric)
{
    if (metric == 'px')
        return false;
    if (metric == 'em')
        return false;
    if (metric == '%')
        return false;
    if (metric == '')
        return false;

    return true;
};

mbcms.visual_fast_edit.main_option.__is_number = function (char)
{
    if (char == 'backspace')
    {
        return true;
    }
    return /\d/.test(char);
};

mbcms.visual_fast_edit.main_option.set_metric = function (char, clear)
{
    if (clear)
    {
        this.__current_metric_find_str = '';
    }


    if (char)
    {
        this.__current_metric_find_str += char;
    }
};

/**
 *
 * @param {type} trg - metric bar
 * @returns {undefined}
 */
mbcms.visual_fast_edit.main_option.find_metric_option = function (trg)
{

    var new_option = trg.children('option:contains("' + this.__current_metric_find_str.toLowerCase() + '"):first');

    if (new_option.length > 0)
    {
        trg
            .children()
            .prop('selected', false)
        ;

        new_option.prop('selected', true);
    }
};

/**
 *
 * @param {type} char
 * @param {type} trg
 * @param {type} clearup
 * @param {type} event
 * @param {type} zoom
 * @returns {Boolean}
 */
mbcms.visual_fast_edit.main_option.__set_value_number = function (char, trg, clearup, event, zoom)
{
    //noinspection JSValidateTypes
    if (event == 'mousewheel')
    {
        if (!is_nan(trg.val()))
        {
            trg.val(0);
        }

        var n = clearup ? 1 : -1;
        var val = parseFloat(trg.val()) + (n * zoom);
        val = parseInt(val * 100) / 100;
        trg.val(val);

        this.dynamic_go(trg);

        return false;
    }
    else
    {
        if (this.__is_number(char))
        {
            if (char == 'backspace')
            {
                clearup = true;
                char = '';
            }

            trg.val(clearup ? char : trg.val() + char);
        }
        else if (!this.__is_number(char))
        {
            this.set_metric(char, clearup);
            this.find_metric_option(trg.next());
        }

        return false;
    }
};

/**
 *
 * @param {mixed} e - jq_event or string  value for find
 * @param {type} trg
 * @param {type} clearup
 * @param {type} event
 * @param {number} zoom
 * @returns {Boolean}
 */
mbcms.visual_fast_edit.main_option.set_value = function (e, trg, clearup, event, zoom)
{
    var char = typeof e == 'object' ? mbcms.visual_fast_edit.fly_edit.get_current_char(e) : e;

    if (e.keyCode == KEY_BACKSPACE)
    {
        char = 'backspace';
    }

    zoom = zoom || 1;

    if (isset(trg, 'attr'))
    {
        zoom = zoom * parseFloat(trg.attr('step'));
    }

    if (isset(trg, 'prop'))
    {
        if (trg.prop('id') == 'value_with_metric_v')
        {
            if (trg.prop('type') == 'number')
            {
                var ret = this.__set_value_number(char, trg, clearup, event, zoom);
                this.dynamic_go(trg);
                return ret;
            }
            else
            {
                setTimeout(function ()
                {
                    mbcms.visual_fast_edit.main_option.dynamic_go(trg);
                }, 200);

                return true;
            }
        }
        else if (trg.prop('id') == 'value_with_metric_m')
        {
            //noinspection JSValidateTypes
            if (event == 'mousewheel')
            {
                var __sel = trg.children(':selected');

                trg
                    .children()
                    .prop('selected', false)
                ;

                if (clearup)
                {
                    __sel.prev().prop('selected', true);
                }
                else
                {
                    __sel.next().prop('selected', true);
                }

                this.dynamic_go(trg);

                return false;
            }
            else
            {
                this.set_metric(char, clearup);
                this.find_metric_option(trg);
            }
        }

        this.dynamic_go(trg);
    }
};

mbcms.visual_fast_edit.main_option.dynamic_go = function (trg)
{
    if (trg.prop('id') == 'value_with_metric_v' || trg.prop('id') == 'value_with_metric_m')
    {
        var key = trg.attr('__ov').replace(new RegExp('_', 'g'), '-');
        var trgjq = mbcms.visual_fast_edit.get_targets();

        if (key == '--text') //так же класс и допкласс
        {
            trgjq = mbcms.visual_fast_edit.get_targets(null, true);
            trgjq.html(this.__get_complex_value(trg)); // html в дальнейшем!
        }
        else if (isset(trgjq, 'css'))
        {
            trgjq.css(key, this.__get_complex_value(trg));
        }

        new event.visual_fast_edit.main_option.go().call();
    }
};

mbcms.visual_fast_edit.main_option.__get_complex_value = function (trg)
{
    var value, metric;

    if (trg.prop('id') == 'value_with_metric_v')
    {
        value = trg.val();
        metric = trg.next().children(':selected').text();
    }
    else if (trg.prop('id') == 'value_with_metric_m')
    {
        value = trg.prev().val();
        metric = trg.children(':selected').text();
    }

    if (this.__is_metric(metric))
    {
        if (metric == 'destroy')
        {
            return '';
        }

        return metric;
    }
    else
    {
        return value + metric;
    }
};

new event.site.load().listen(function ()
{
    new event.shortkey.keydown().listen(function ()
    {
        if (this.async === false)
        {
            if (this.e.keyCode == KEY_ESC || this.e.keyCode == KEY_CTRL)
            {
                return false;
            }

            this.return = mbcms.visual_fast_edit.main_option.set_value(this.e, this.trg, this.clear, this);
            this.return = this.return || false;
        }
    });


    new event.shortkey.mousewheel().listen(function ()
    {
        event.shortkey.mousewheel.return = mbcms.visual_fast_edit.main_option.set_value(this.e, this.trg, this.mwheelup, this.event_name, this.zoom);
    });

    new event.visual_fast_edit.start_drag().listen(function ()
    {
        if (isset(this.trg, 'prop'))
        {
            if (this.trg.prop('id') == 'value_with_metric_v' && this.trg.prop('type') == 'number')
            {
                mbcms.visual_fast_edit.fly_edit.blocked(true);
            }
        }
    });

    new event.visual_fast_edit.init().listen(function ()
    {
        this.jq_container.find('[__mousedown]').change(function ()
        {
            mbcms.visual_fast_edit.main_option.dynamic_go($(this));
        });
    });


    new event.visual_fast_edit.init().listen(function ()
    {
        var $this = this;
        ck_editor.init(this.jq_container);
        var txar = $this.jq_container.find('textarea');
        var id = txar.attr('id');

        if (txar.length)
        {
            setTimeout(function ()
            {
                try
                {
                    CKEDITOR.instances[id]
                        .on('change', function ()
                        {
                            mbcms.visual_fast_edit.main_option.dynamic_go(txar);
                        });
                }
                catch (e)
                {

                }

            }, 500)
        }


        var data = mbcms.visual_fast_edit.get_current_data();
        var key = $this.jq_container
            .find('[colorpicker]').attr('__ov');

        key = key ? key.replace('_', '-') : '';

        if (key)
        {
            $this.jq_container
                .find('[colorpicker]')
                .css({
                    width: 20,
                    background: data.this.css(key),
                    color: data.this.css(key),
                    cursor: 'pointer'
                })
                .colorpicker
                (
                    {
                        parts: 'full',
                        alpha: true,
                        colorFormat: 'RGBA',
                        ok: function (a, b)
                        {
                            mbcms.visual_fast_edit.get_targets(data).css(key, b.formatted);
                            $this.jq_container
                                .find('[colorpicker]')
                                .css({
                                    background: b.formatted,
                                    color: b.formatted
                                });
                        },
                        select: function (a, b)
                        {
                            mbcms.visual_fast_edit.get_targets(data).css(key, b.formatted);
                            $this.jq_container
                                .find('[colorpicker]')
                                .css({
                                    background: b.formatted,
                                    color: b.formatted
                                });
                        }
                    }
                )
            ;
        }


    });


});
