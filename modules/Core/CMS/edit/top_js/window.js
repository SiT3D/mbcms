
/**
 * 
 * @param {string} unical_id
 * @returns {mbcms|Boolean}
 */
mbcms.window = function (unical_id)
{
    if (typeof unical_id === 'undefined')
        return unical_id;

    var $old_window = mbcms.window.getWindow(unical_id);

    if (typeof $old_window !== 'undefined')
    {
        $old_window.destroy();
    }

    this.$ = $('<div />');
    this.$
            .addClass('mbcms_admin_window')
            .prop('id', unical_id)
            .css({width: mbcms.window.__position_offset})
            .data('window', this);

    this.unical_id = unical_id;
    this.__N = mbcms.window.__stack.push(this) - 1;
    this.$.appendTo('body');

    $(this.$).perfectScrollbar();
    this.__init_unclose_click();
    this.__init_unclose_hover();
    this.__init_opacity_hover();
    this.setColor('#fff', '#000');

    this.__open_listners = [];
    this.__close_listners = [];
    this.__destroy_listners = [];

    this.open();

    return this;
};


mbcms.window.__stack = [];
mbcms.window.prototype.unical_id;
mbcms.window.prototype.__width = 200;
mbcms.window.__position_offset = 10;
mbcms.window.__lastContent = undefined;
mbcms.window.__N = -1;
mbcms.window.last_close;
mbcms.window.prototype.undestroy = false;
mbcms.window.prototype.__title;
mbcms.window.prototype.__color;
mbcms.window.prototype.__close_listners = [];
mbcms.window.prototype.__open_listners = [];
mbcms.window.prototype.__destroy_listners = [];
mbcms.window.__min_opacity = 0.3;

mbcms.window.each = function (callback)
{
    if (typeof callback === 'function')
    {
        for (var i in mbcms.window.__stack)
        {
            callback.call(mbcms.window.__stack[i]);
        }
    }
};

mbcms.window.getWindow = function (unical_id)
{
    for (var i in mbcms.window.__stack)
    {
        if (mbcms.window.__stack[i].unical_id === unical_id)
        {
            return mbcms.window.__stack[i];
        }
    }

    return undefined;
};

mbcms.window.getWindowN = function (n)
{
    return mbcms.window.__stack[n];
};

mbcms.window.__sort_positions = function ()
{
    var deep = 0;
    var lastWindow = null;
    var n = 0;
    mbcms.window.each(function ()
    {
        var lastWidth = lastWindow !== null ? parseInt(lastWindow.$.css('width')) + parseInt(lastWindow.$.css('padding-left')) : 0;
        lastWindow = this;
        this.__N = n;
        n++;
        deep += lastWidth;
        this.$.css({left: deep});
    });
};

/**
 * 
 * @param {type} callback если отсутствует, то открывает окно, иначе подпись на открытие
 * @returns {undefined}
 */
mbcms.window.prototype.open = function (callback)
{
    var $this = this;

    if (typeof callback == 'function')
    {
        this.__open_listners.push(callback);
    }
    else
    {
        for (var i in this.__open_listners)
        {
            this.__open_listners[i].call(this.__open_listners[i]);
        }

        mbcms.window.each(function ()
        {
            if ($this.unical_id === this.unical_id)
            {
                this.$.css({width: this.__width, opacity: 1});
                this.$.removeAttr('close');
                this.$.children('*').show();
                this.__setTitle(false);
            }
            else
            {
                this.close();
            }
        });

        mbcms.window.__sort_positions();

    }

};

/**
 * 
 * @param {type} callback если отсутствует, то закрывает окно, иначе подпись на закрытие
 * @returns {undefined}
 */
mbcms.window.prototype.close = function (callback)
{
    if (typeof callback == 'function')
    {
        this.__close_listners.push(callback);
    }
    else
    {
        for (var i in this.__close_listners)
        {
            this.__close_listners[i].call(this.__close_listners[i]);
        }

        this.$.css({width: 0, opacity: mbcms.window.__min_opacity, overflow: 'hidden'});
        this.$.children('*').hide();
        this.$.attr('close', true);
        mbcms.window.last_close = this;
        this.__setTitle();

    }

};

mbcms.window.prototype.__init_unclose_click = function ()
{
    var $this = this;
    this.$.click(function ()
    {
        if ($this.$.attr('close'))
        {
            $this.open();
            return false;
        }
    });
};

mbcms.window.prototype.__init_unclose_hover = function ()
{
    var $this = this;
    this.$.hover(function ()
    {
        if ($this.$.attr('close'))
        {
            // только осталось еще сделать так же схождение
            // и убрать баг когда выбираешь, оно маленькое остается
            var new_width = mbcms.window.__position_offset * 4;
            new_width = new_width > $this.__width ? $this.__width : new_width;
            if ($this.__div_title)
                $this.__div_title.css({paddingLeft: 25});
            $this.$.stop(true).animate({width: new_width, opacity: 1},
                    {
                        step: function ()
                        {
                            if ($this.$.attr('close'))
                            {
                                mbcms.window.__sort_positions();
                            }
                        },
                        duration: 'fast'
                    });
        }
    }, function ()
    {
        typeof $this.openHoverTimer !== 'undefined' ? clearTimeout($this.openHoverTimer) : null;

        if ($this.__div_title)
            $this.__div_title.css({paddingLeft: 6});
        if ($this.$.attr('close'))
        {
            $this.$.stop(true).animate({width: 0, opacity: mbcms.window.__min_opacity},
                    {
                        step: function ()
                        {
                            if ($this.$.attr('close'))
                            {
                                mbcms.window.__sort_positions();
                            }
                        },
                        duration: 'fast'
                    });
        }
    });
};

mbcms.window.prototype.__init_opacity_hover = function ()
{
    this.$.hover(function ()
    {
        if (typeof $(this).attr('close') == 'undefined')
            $(this).stop(true).animate({opacity: 1}, 'fast');
    }, function ()
    {
        if (typeof $(this).attr('close') == 'undefined')
            $(this).stop(true).delay(500).animate({opacity: mbcms.window.__min_opacity}, 'fast');
    });
};

/**
 * 
 * @param {type} callback вызов если нет параметра, иначе запись в listner
 * @returns {undefined}
 */
mbcms.window.prototype.destroy = function (callback)
{
    if (typeof callback == 'function')
    {
        this.__destroy_listners.push(callback);
    }
    else
    {

        if (this.undestroy)
        {
            this.close();
            mbcms.window.__sort_positions();
            return;
        }
        else
        {
            for (var i in this.__destroy_listners)
            {
                this.__destroy_listners[i].call(this.__destroy_listners[i]);
            }
        }

        this.$.remove();
        for (var i in mbcms.window.__stack)
        {
            if (this.unical_id === mbcms.window.__stack[i].unical_id)
            {
                mbcms.window.__stack.splice(i, 1);
                delete this;
                mbcms.window.__stack[mbcms.window.__stack.length - 1].open();
                break;
            }
        }

    }
};

mbcms.window.prototype.setWidth = function (width)
{
    var min = 50;
    __width = width < min ? min : width;
    this.$.css({width: width});
    this.__width = width;
};

/**
 * 
 * @param {type} title
 * @param {type} bgcolor
 * @param {type} textcolor
 * @returns {undefined}
 */
mbcms.window.prototype.setTitle = function (title, bgcolor, textcolor)
{
    this.__title = title;
    this.setColor(bgcolor, textcolor);
};

mbcms.window.prototype.__setTitle = function (create)
{
    create = typeof create !== 'undefined' ? create : true;

    if (this.__div_title)
        this.__div_title.remove();

    if (create)
    {
        var text = typeof this.__title !== 'undefined' ? this.__title.split('') : [];
        this.__div_title = $('<div />')
                .css(
                        {
                            width: 15,
                            paddingTop: 40,
                            paddingLeft: 6,
                            paddingRight: 25,
                            fontSize: 22,
                            background: this.__color.bg,
                            color: this.__color.text,
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            height: '100%'
                        })
                .html(text.join('<br/>'))
                .attr('title', this.unical_id)
                ;
        this.$.append(this.__div_title);
    }
};

/**
 * 
 * @param {type} backgroundColor // red, green, #fff
 * @param {type} textColor // red, green, #fff
 * @returns {undefined}
 */
mbcms.window.prototype.setColor = function (backgroundColor, textColor)
{
    this.__color = {bg: backgroundColor, text: textColor};
};

/**
 * 
 * @param {type} callbackWithJQuery
 * @returns {undefined}
 */
mbcms.window.prototype.setContent = function (callbackWithJQuery)
{
    if (typeof callbackWithJQuery === 'function' || typeof this.__lastContent === 'function')
    {
        if (typeof this.__lastContent === 'undefined')
        {
            this.__lastContent = callbackWithJQuery;
        }

        this.$.empty();
        if (typeof callbackWithJQuery !== 'undefined' && callbackWithJQuery.consta)
        {
            this.$.append(callbackWithJQuery); // при вставке проверять наверное что это jquery объект.
        }
        else if (typeof this.__lastContent === 'function')
        {
            this.$.append(this.__lastContent());
        }
    }
};

// active, setParent, close|destroy, setWidth, setContent - что отображает, берем аяксом, либо верстаем в js
// получение окна по id 
// перезагрузка окна еще нужна??, и может быть сворачивание окна??