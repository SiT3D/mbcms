

mbcms.visual_fast_edit.plugin_metrics = function ()
{

};

/**
 * 
 * @param {type} variants // [['key', 'alias'], ['key2', 'alias2']]
 * @param {$} trg 
 * @param {function} callback // this = [key, alias]
 * @returns {undefined}
 */
mbcms.visual_fast_edit.plugin_metrics.prototype.create = function (variants, trg, callback)
{
    if (typeof trg.data('plugin_metrics_create') === 'undefined' || !trg.data('plugin_metrics_create'))
    {
        this.__callback = callback;
        this.__create_variants(variants, trg);
        trg.data('plugin_metrics_create', true);
    }
};

mbcms.visual_fast_edit.plugin_metrics.prototype.__set_current = function (metric)
{
    for (var i in this.__metrics)
    {
        this.__metrics[i].removeClass('active');
    }

    metric.addClass('active');
};

mbcms.visual_fast_edit.plugin_metrics.prototype.__create_variants = function (variants, trg)
{
    var x = trg.offset().left + parseInt(trg.css('width'));
    var y = trg.offset().top;

    var metric = trg.attr('metric_value');

    var self = this;
    this.__metrics = [];
    this.__last_width = 35;
    var animx = x;
    for (var i in variants)
    {
        animx += this.__last_width;
        this.__metrics[i] = $('<div />')
                .addClass('plugin_metrics-children')
                .text(variants[i][1])
                .attr('key', variants[i][0])
                .data('ret', variants[i])
                .appendTo('body')
                .click(function ()
                {
                    if (typeof self.__callback === 'function')
                    {
                        self.__callback.call($(this).data('ret'));
                    }

                    self.__set_current($(this));

                    return false;
                })
                .css({left: x, top: y})
                .animate({left: animx}, 250)
                ;

        this.__last_width
                = parseInt(this.__metrics[i].css('width'))
                + parseInt(this.__metrics[i].css('padding-left'))
                + parseInt(this.__metrics[i].css('padding-right'))
                + 5;

        if (metric == variants[i][0] || metric == variants[i][1])
        {
            this.__set_current(this.__metrics[i]);
        }
    }
};

mbcms.visual_fast_edit.plugin_metrics.prototype.destroy = function (trg)
{
    var x, self = this;
    for (var i in this.__metrics)
    {
        if (i == 0)
        {
            x = this.__metrics[i].css('left');
            if (this.__metrics.length <= 1)
            {
                this.__metrics[i].remove();
                trg.data('plugin_metrics_create', false);
            }
        }
        else
        {
            this.__metrics[i].animate({left: x}, 'fast', function ()
            {
                for (var i in self.__metrics)
                {
                    self.__metrics[i].remove();
                }
                trg.data('plugin_metrics_create', false);
            });
        }
    }
};

(function ()
{

    $.fn.plugin_metrics = function (value, dop)
    {
        var metric = typeof value !== 'undefined' ? value.replace(/\d*/g, '') : '';
        metric = typeof value !== 'undefined' ? metric.replace(/-/g, '') : '';
        value = typeof value === 'undefined' ? '' : value;

        dop = typeof dop !== 'object' ? {} : dop;
        dop = typeof dop === 'undefined' ? {} : dop;

        dop.v = typeof dop.v == 'undefined' ? [
            ['%', '%'],
            ['px', 'px'],
            ['auto', 'auto'],
            ['inherit', 'inherit'],
            ['destroy', 'clear'],
        ] : dop.v;

        var $this = this;
        this
                .val(value.replace(metric, ''))
                .addClass('plugin_metrics-trg')
                .attr('metric_value', metric)
                .focus(function ()
                {
                    if (typeof $this.data('plugin_metrics_create') === 'undefined' || !$this.data('plugin_metrics_create'))
                    {
                        var plg = new mbcms.visual_fast_edit.plugin_metrics();
                        plg.create(dop.v, $(this), function ()
                        {
                            $this.attr('metric_value', this[0]);
                            $this.focus();
                        });


                        $(this).data('plugin_metrics', plg);
                    }

                    $this.change();

                    if (isset($this, '__timer'))
                        clearTimeout($this.__timer);
                })
                .blur(function ()
                {
                    var plg = $this.data('plugin_metrics');
                    var $this2 = $this;
                    $this.__timer = setTimeout(function ()
                    {
                        if (isset(plg, 'destroy'))
                            plg.destroy($this2);
                    }, 500);
                })
                .bind('remove', function ()
                {
                    $this.data('plugin_metrics').destroy($this);
                })
                .keyup(function (e)
                {
                    if (e.keyCode === KEY_ESC)
                    {
                        setTimeout(function ()
                        {
                            $this.blur();
                        }, 50); // чтобы не срабатывала перезагрузка страницы
                    }
                })
                ;

        return this;
    };

})();

