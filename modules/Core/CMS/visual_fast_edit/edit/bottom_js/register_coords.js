mbcms.visual_fast_edit.register_coords = function ()
{
};

mbcms.visual_fast_edit.register_coords.__regions = {};
mbcms.visual_fast_edit.register_coords.__zoom = 100; // px green

mbcms.visual_fast_edit.register_coords.get_elements = function (x, y)
{
    x = this.__gzoom(x);
    y = this.__gzoom(y);

    if (isset(this.__regions, x, y))
    {
        return this.__regions[x][y];
    }
    else
    {
        return [];
    }
};

mbcms.visual_fast_edit.register_coords.__gzoom = function (value)
{
    return parseInt(value / this.__zoom);
};

mbcms.visual_fast_edit.register_coords.init = function (dop)
{
    this.__regions = {};
    dop = typeof dop === 'undefined' ? {} : dop;
    dop.find_selector = typeof dop.find_selector === 'undefined' ? '[connect_type]' : dop.find_selector;
    dop.$trg = typeof dop.$trg === 'undefined' ? mbcms.controll_window.get() : dop.$trg;
    this.__zoom = typeof dop.zoom === 'undefined' ? this.__zoom : dop.zoom;

    var st = $('#admin_modules_content').scrollTop();
    var sl = $('#admin_modules_content').scrollLeft();
    var self = this;

    dop.$trg
        .find(dop.find_selector)
        .each(function ()
        {
            if ($(this).parents('[fixed_padlock=fixed]').length != 0)
            {
                return;
            }

            var regions = self.__regions;
            var $this = $(this);
            var offset = $this.offset();

            var x1 = self.__gzoom(offset.left + sl);
            var x2 = self.__gzoom(offset.left + parseInt($this.css('width')) + sl);
            var y1 = self.__gzoom(offset.top + st);
            var y2 = self.__gzoom(offset.top + parseInt($this.css('height')) + st);

            for (var i = x1; i <= x2; i++)
            {
                if (!isset(regions, i))
                {
                    regions[i] = {};
                }

                for (var ii = y1; ii <= y2; ii++)
                {
                    if (!isset(regions, i, ii))
                    {
                        regions[i][ii] = [];
                    }

                    regions[i][ii].push($(this));
                }

            }

        })
    ;
};

new event.controll_window.load().listen(function ()
{
    if (typeof this.__first_time !== 'undefined')
    {
        return;
    }

    this.__first_time = true;

    if (this.__timer != undefined)
    {
        clearInterval(this.__timer);
    }

    mbcms.visual_fast_edit.register_coords.init();

    this.__timer = setInterval(function ()
    {
        mbcms.visual_fast_edit.register_coords.init();
    }, 5000);

});

