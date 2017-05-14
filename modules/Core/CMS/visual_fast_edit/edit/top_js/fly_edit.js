/**
 * очему то не работает если оставить в visualfastedit ((((
 */
event.visual_fast_edit.init = function ()
{
    this.__key = 'event.visual_fast_edit.init';

    /**
     * Контейнер с формой
     */
    this.jq_container = null;
};
event.visual_fast_edit.init.prototype = Object.create(event.prototype);

event.visual_fast_edit.start_drag = function ()
{
    this.__key = 'event.visual_fast_edit.start_drag';
    this.trg = null;
};
event.visual_fast_edit.start_drag.prototype = Object.create(event.prototype);

event.visual_fast_edit.close_form = function ()
{
    this.__key = 'event.visual_fast_edit.close_form'; // уникальный идентификатор, прсто название класса!
    this.form = null;
};
event.visual_fast_edit.close_form.prototype = Object.create(event.prototype);


mbcms.visual_fast_edit.fly_edit = function (jq_container)
{
    this.__container = jq_container;
    mbcms.visual_fast_edit.fly_edit.__this = this;

    var evt = new event.visual_fast_edit.init().call();
    evt.jq_container = jq_container;


    mbcms.visual_fast_edit.fly_edit.__current_exempl = this;

    this.activation_option($('[__ov]:first'), jq_container, true);
};

mbcms.visual_fast_edit.fly_edit.prototype.__container = null;
mbcms.visual_fast_edit.fly_edit.prototype.__mousedown = false;
mbcms.visual_fast_edit.fly_edit.prototype.__drag = false;
mbcms.visual_fast_edit.fly_edit.prototype.__drag_start_pos = [];
mbcms.visual_fast_edit.fly_edit.__current_exempl = null;
mbcms.visual_fast_edit.fly_edit.__trg = null;
mbcms.visual_fast_edit.fly_edit.__this = null;
mbcms.visual_fast_edit.fly_edit.__options = [];

/**
 *
 * @param {type} blocked
 * @returns {bool}
 */
mbcms.visual_fast_edit.fly_edit.blocked = function (blocked)
{
    if (blocked == undefined)
    {
        return $('#visual_fast_edit-fly_edit-blocked').length ? true : false;
    }

    if (blocked && $('#visual_fast_edit-fly_edit-blocked').length == 0)
    {
        $('<div/>')
            .css({
                position: 'fixed',
                width: '100%',
                height: '100%',
                top: 0,
                left: 0,
                background: '#000',
                opacity: 0.3,
                zIndex: 11
            })
            .appendTo('body')
            .prop('id', 'visual_fast_edit-fly_edit-blocked')
            .mousedown(function (e)
            {
                this.__x = e.clientX;
                this.__y = e.clientY;

                mbcms.visual_fast_edit.fly_edit.__current_exempl.__set_start_xy(e);
                mbcms.visual_fast_edit.fly_edit.__current_exempl.__mousedown = true;

                return false;
            })
            .mouseup(function (e)
            {
                if (this.__x == e.clientX && this.__y == e.clientY)
                {
                    mbcms.visual_fast_edit.fly_edit.blocked(false);
                }

                mbcms.visual_fast_edit.fly_edit.__current_exempl.__mousedown = false;
            })
            .mousemove(function (e)
            {
                mbcms.visual_fast_edit.fly_edit.__current_exempl.drag(e);
            })
        ;
    }
    else
    {
        $('#visual_fast_edit-fly_edit-blocked').remove();
        var $this = mbcms.visual_fast_edit.fly_edit.__this;
        $this.activation_option($this.__drag, $this.__container, false);
        mbcms.visual_fast_edit.fly_edit.__current_exempl.destroy_drag();
    }
};

mbcms.visual_fast_edit.fly_edit.prototype.star_drag = function (e)
{
    if (this.__mousedown && !this.__drag)
    {
        this.__set_start_xy(e);

        var evt = new event.visual_fast_edit.start_drag().call();
        evt.trg = mbcms.visual_fast_edit.fly_edit.__trg;

        this.__drag = mbcms.visual_fast_edit.fly_edit.__trg;
    }
};

mbcms.visual_fast_edit.fly_edit.prototype.drag = function (e)
{
    if (this.__drag && this.__mousedown)
    {
        var up = true;
        var zoom = this.__calc_zoom(e);

        if (typeof this.__zz_start == 'undefined')
        {
            var container = this.__container;
            this.__drag.attr('not_invisible', true);
            this.__zoomer(container, 100, 0.01);

            this.__zz_start = true;
        }

        var evt = new event.shortkey.mousewheel().call();
        evt.e = e;
        evt.trg = this.__drag;
        evt.mwheelup = up;
        evt.zoom = zoom;
    }

    if (this.__zz_start)
    {
        this.__zz_start = undefined;
    }
};

mbcms.visual_fast_edit.fly_edit.prototype.__calc_zoom = function (e)
{
    var startX = this.__drag_start_pos[0];
    var startY = this.__drag_start_pos[1];

    if (startX == undefined || startY == undefined)
    {
        mbcms.visual_fast_edit.fly_edit.prototype.__set_start_xy(e);
        startX = this.__drag_start_pos[0];
        startY = this.__drag_start_pos[1];
    }

    var __x = startX - e.clientX;
    var __y = startY - e.clientY;

    var ret = 0;

    if (Math.abs(__x) > Math.abs(__y))
    {
        ret = __x;
    }
    else
    {
        ret = __y;
    }

    this.__set_start_xy(e);

    return e.ctrlKey ? ret : -ret;
};

mbcms.visual_fast_edit.fly_edit.prototype.__set_start_xy = function (e)
{
    this.__drag_start_pos[0] = e.clientX;
    this.__drag_start_pos[1] = e.clientY;
};

mbcms.visual_fast_edit.fly_edit.prototype.destroy_drag = function ()
{
    this.__drag = false;
};

/**
 *
 * @param {$} jq_trgOption __OV
 * @param {$} jq_container родительская форма, где лежат опции, вернее даже родитель форм
 * @param {$} destroy
 * @returns {undefined}
 */
mbcms.visual_fast_edit.fly_edit.prototype.activation_option = function (jq_trgOption, jq_container, destroy)
{
    jq_container = jq_container || this.__container;

    if (!jq_trgOption || jq_trgOption == undefined)
    {
        return;
    }

    this.deactivation_option(jq_container);

    jq_trgOption.attr('not_invisible', true);
    mbcms.visual_fast_edit.fly_edit.__trg = jq_trgOption;

    this.__zoomer(jq_container, 100);

    __delay(this, 'sdfgsdf0899sdf', 100, function ()
    {
        $(':focus').blur();
        jq_trgOption.focus();
    });

    this.__list(jq_trgOption);
    jq_trgOption.stop(true).animate({
        opacity: 1,
        zoom: 1.3
    }, 'fast');
    var label = mbcms.visual_fast_edit.fly_edit.__get_label(jq_trgOption);
    label.stop(true).animate({
        opacity: 1,
        zoom: 1.3
    }, 'fast').attr('not_invisible', true);
    jq_trgOption.css({
        outline: '2px solid orange',
        zIndex: 3
    });

    if (destroy != false)
    {
        mbcms.visual_fast_edit.fly_edit.blocked(false);
    }
};


mbcms.visual_fast_edit.fly_edit.prototype.__list = function (jq_trgOption)
{
    if (jq_trgOption.attr('__mousedown') != undefined)
    {
        var options = jq_trgOption.children('option');

        if (options.length > 0)
        {
            var list = $('<div class="select_helper-fly-edit" />')
                    .css({
                        padding: '10px',
                        background: '#fff',
                        border: '1px solid #777',
                        position: 'absolute',
                        left: '90%',
                        top: '70%',
                        zIndex: 10,
                        width: jq_trgOption.width()
                    })
                ;

            var setter = function ()
            {
                list.empty();

                options.each(function ()
                {
                    var div = $('<div />').appendTo(list).text($(this).text())
                        .css({
                            fontSize: '15px',
                            color: '#222'
                        });

                    if (!$(this).text())
                    {
                        div.css({
                            height: 20,
                            background: '#ccc'
                        });
                    }

                    if ($(this).prop('selected'))
                    {
                        div.css({
                            background: '#555',
                            color: 'orange'
                        });
                    }
                });
            };

            setter();

            new event.visual_fast_edit.main_option.go().listen(function ()
            {
                setter();
            });

            jq_trgOption.after(list);
        }
    }
};

/**
 *
 * @param {type} jq_container
 * @param {type} delay
 * @param {type} zoom
 * @returns {undefined}
 */
mbcms.visual_fast_edit.fly_edit.prototype.__zoomer = function (jq_container, delay, zoom)
{
    delay = delay == undefined ? 5000 : delay;
    zoom = zoom == undefined ? 0.67 : zoom;

    __delay(this, '__timer', delay, function ()
    {
        jq_container
            .find('[__OV]:not([not_invisible]), [__OV__]:not([not_invisible])')
            .stop(true)
            .animate({
                opacity: 0.5,
                zoom: zoom
            }, 100);
    });
};

/**
 *
 * @param {type} jq_container
 * @returns {undefined}
 */
mbcms.visual_fast_edit.fly_edit.prototype.deactivation_option = function (jq_container)
{
    jq_container = jq_container || this.__container;

    if (isset(mbcms.visual_fast_edit.fly_edit.__trg, 'removeAttr'))
    {
        mbcms.visual_fast_edit.fly_edit.__trg.removeAttr('not_invisible');
        mbcms.visual_fast_edit.fly_edit.__trg.css({
            outline: '',
            zIndex: ''
        });
        mbcms.visual_fast_edit.fly_edit.__trg.blur();
        mbcms.visual_fast_edit.fly_edit.__trg.stop(true).animate({
            opacity: 0.4,
            zoom: 0.67
        }, 'slow');

        $('.select_helper-fly-edit').remove();
        var label = mbcms.visual_fast_edit.fly_edit.__get_label(mbcms.visual_fast_edit.fly_edit.__trg);
        label.stop(true).animate({
            opacity: 0.4,
            zoom: 0.67
        }, 'slow');
        label.removeAttr('not_invisible');
    }

    __delay(this, '__timer', 6000, function ()
    {
        jq_container.find('[__OV], [__OV__]').stop(true).animate({
            opacity: 1,
            zoom: 1
        }, 'fast');
    });

    mbcms.visual_fast_edit.fly_edit.__trg = null;
};

mbcms.visual_fast_edit.fly_edit.__get_label = function (trg)
{
    var label = trg.parents('[__fe]:first').prevAll('[__ov__]:first');
    return label;
};

/**
 *
 * @param {$.e} e
 * @returns {undefined}
 */
mbcms.visual_fast_edit.fly_edit.select_group = function (e)
{
    function pick(number)
    {
        var index = number - 1;
        var trg = $('[__ov__]:eq(' + index + ')').nextAll('.control-group:first').find('[__ov]:first');
        mbcms.visual_fast_edit.fly_edit.__this.activation_option(trg);
    }

    if (typeof e == 'object')
    {
        switch (e.keyCode)
        {
            case KEY_F1:
                pick(1);
                break;
            case KEY_F2:
                pick(2);
                break;
            case KEY_F3:
                pick(3);
                break;
            case KEY_F4:
                pick(4);
                break;
            case KEY_F5:
                pick(5);
                break;
            case KEY_F6:
                pick(6);
                break;
            case KEY_F7:
                pick(7);
                break;
            case KEY_F8:
                pick(8);
                break;
            case KEY_F9:
                pick(9);
                break;
            case KEY_F10:
                pick(10);
                break;
        }
    }

};

mbcms.visual_fast_edit.fly_edit.prototype.init = function ()
{
    var self = this;
    var static = mbcms.visual_fast_edit.fly_edit;

    static.__options = [];

    this.__container
        .find('[__OV]')
        .hover(function ()
        {
            var $this = $(this);
            self.activation_option($this, self.__container);
        }, function ()
        {
        })
        .mousedown(function (e)
        {
            self.__mousedown = true;
            self.star_drag(e);

            if ($(this).attr('__MOUSEDOWN') == undefined)
            {
                return false;
            }
        })
        .mouseup(function ()
        {
            self.destroy_drag();
            self.__mousedown = false;
        })
        .each(function ()
        {
            var id = static.__options.push($(this));
            $(this).data('__form_id', id - 1);
        })
    ;

    var i = 1;

    var self2 = mbcms.visual_fast_edit.fly_edit;

    this.__container
        .find('[__ov__]')
        .each(function ()
        {
            var ch = $(this).children();
            ch.text(ch.text() + ' ( F' + i + ' )');

            $(this).data('iii', i);

            $(this).hover(function ()
            {
                self2.select_group($(this).data('iii'));
            }, function ()
            {
            });

            i++;
        })
    ;
};

/**
 *
 * @param {type} next  = true or prev
 * @returns {undefined}
 */
mbcms.visual_fast_edit.fly_edit.get_next = function (next)
{
    next = next == undefined ? true : next;

    var id = this.get_trg().data('__form_id');
    id = next ? id + 1 : id - 1;
    var trg = isset(this, '__options', id) ? this.__options[id] : null;

    if (trg)
    {
        this.__this.deactivation_option(mbcms.visual_fast_edit.fly_edit.get_trg());
        this.__this.activation_option(trg, this.__this.__container);
    }
}
;

mbcms.visual_fast_edit.fly_edit.get_trg = function ()
{
    return this.__trg;
};

mbcms.visual_fast_edit.fly_edit.unselect_trg = function ()
{
    this.__trg = null;
};

/**
 *
 * @param {type} e jquery event
 * @returns {String}
 */
mbcms.visual_fast_edit.fly_edit.get_current_char = function (e)
{
    return String.fromCharCode(e.keyCode); // в дальнейшем это своя логика + язык
};















