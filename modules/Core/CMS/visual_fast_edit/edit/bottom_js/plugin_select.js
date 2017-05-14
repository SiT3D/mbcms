

mbcms.visual_fast_edit.plugin_select = function ()
{

};

mbcms.visual_fast_edit.plugin_select.prototype.__dop = {};

mbcms.visual_fast_edit.plugin_select.prototype.dop = function (dops)
{
    this.__dop = typeof dops !== 'object' ? {} : dops;
};

/**
 * 
 * @param {type} variants // [['key', 'alias'], ['key2', 'alias2']]
 * @param {$event} mouseData 
 * @param {function} callback
 * @param {$} trg 
 * @returns {undefined}
 */
mbcms.visual_fast_edit.plugin_select.prototype.create = function (variants, mouseData, callback, trg)
{
    if (isset(trg.data('plugin_select'), 'destroy'))
    {
        trg.data('plugin_select').destroy();
    }

    this.__trg = trg;
    this.__callback = callback;
    this.__create_body(mouseData);
    this.__create_variants(variants);
    trg.data('plugin_select', this);
};

mbcms.visual_fast_edit.plugin_select.prototype.__create_body = function (mouseData)
{
    var x = mouseData.clientX + 20, y = mouseData.clientY + 20;
    if (mouseData.clientX > parseInt($('body').css('width')) + $('body').scrollLeft() - 240)
        x = parseInt($('body').css('width')) + $('body').scrollLeft() - 300;

    if (mouseData.clientY > parseInt($('body').css('height')) + $('body').scrollTop() - 200)
        y = parseInt($('body').css('height')) + $('body').scrollTop() - 200;

    this.__body = $('<div />')
            .addClass('plugin_select-body')
            .css({top: y, left: x, position: 'fixed'})
            .appendTo('body')
            .data('plugin_select', this)
            ;
};

mbcms.visual_fast_edit.plugin_select.prototype.__create_variants = function (variants)
{
    variants = typeof variants === 'undefined' ? {} : variants;
    var self = this;
    var dop = this.__dop;

    for (var i in variants)
    {
        $('<div />')
                .addClass('plugin_select-children')
                .html(variants[i][1])
                .attr('key', variants[i][0])
                .data('ret', variants[i])
                .appendTo(this.__body)
                .click(function ()
                {
                    if (typeof self.__callback === 'function')
                    {
                        self.__callback.call($(self.__trg), $(this).data('ret'));
                    }

                    if (typeof dop.unhover === 'function')
                    {
                        dop.unhover.call($(self.__trg), $(this).data('ret'));
                    }

                    self.destroy();

                    return false;
                })
                .hover(function ()
                {
                    if (typeof dop.hover === 'function')
                    {
                        dop.hover.call($(self.__trg), $(this).data('ret'));
                    }
                }, function ()
                {
                    if (typeof dop.unhover === 'function')
                    {
                        dop.unhover.call($(self.__trg), $(this).data('ret'));
                    }
                })
                ;
    }

    $('<div />')
            .addClass('plugin_select-children')
            .text('Close')
            .attr('key', 'null')
            .data('ret', ['null', 'Close'])
            .prependTo(this.__body)
            .click(function ()
            {
                if (typeof self.__callback === 'function')
                {
                    self.__callback.call($(self.__trg), $(this).data('ret'));
                }

                self.destroy();

                return false;
            })
            .mouseup(function ()
            {
                return false;
            });
};

mbcms.visual_fast_edit.plugin_select.prototype.destroy = function ()
{
    if (isset(this, '__body', 'remove'))
        this.__body.remove();
    if (isset(this, '__trg', 'data'))
        this.__trg.data('plugin_select', null);
};

(function ()
{
    /**
     * 
     * @param {type} dop // {callback: function(value), v: [['key', 'alias']], dblclick: bool, hover: function, unhover: function}
     * @returns {$.fn}
     */
    $.fn.plugin_select = function (dop)
    {

        this
                .mousedown(function (e)
                {
                    this.__xy = {x: e.clientX, y: e.clientY};
                    this.__click = true;
                })
                .mouseup(function (e)
                {
                    if (!isset(this, '__xy'))
                        return false;

                    var mouse = Math.abs(this.__xy.x - e.clientX) == 0 && Math.abs(this.__xy.y - e.clientY) == 0;

                    if ((mouse && !isset(dop, 'dblclick')) || this.__dblclick)
                    {
                        var plg = new mbcms.visual_fast_edit.plugin_select();
                        $(this).data('plugin_select', plg);
                        plg.create(dop.v, e, dop.callback, $(this));
                        plg.dop(dop);
                    }
                    else if (mouse && isset(dop, 'dblclick') && dop.dblclick)
                    {
                        var self = this;
                        this.__dblclick = true;
                        setTimeout(function ()
                        {
                            self.__dblclick = false;
                        }, 350);
                    }

                });

        return this;
    };

//    $('body').plugin_select(
//            {
//                dblclick: true
//            });
})();