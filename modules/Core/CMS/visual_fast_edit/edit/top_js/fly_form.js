
/**
 * Форма с выборами вариантов, типа селекта
 *   
 * @returns {mbcms.visual_fast_edit.fly_form}
 */
mbcms.visual_fast_edit.fly_form = function () {
};

/**
 * 
 * @param {array} options // [{$: jquery, key: string, alias: string, value: mixed, req: bool}]
 * @param {function} okcallback // function({form_values})
 * @returns {undefined}
 */
mbcms.visual_fast_edit.fly_form.create = function (options, okcallback)
{
    var self = this;
    this.__okcallback = okcallback;

    if (isset(this, '__form', 'remove'))
    {
        this.__form.remove();
    }

    this.__form = $('<div />')
            .css(
                    {
                        width: 270,
                        height: 120,
                        overflow: 'auto',
                        position: 'fixed',
                        left: '50%',
                        top: '50%',
                        marginLeft: -150,
                        marginTop: -100,
                        padding: 30,
                        background: '#fff',
                        border: '3px solid orange',
                        borderRadius: 10,
                        zIndex: 11
                    })
            .appendTo('body')
            ;

    var OK = $('<button />')
            .text('OK')
            .click(function ()
            {
                var ret = self.__get_values();
                if (ret)
                {
                    if (typeof self.__okcallback === 'function')
                    {
                        self.__okcallback(ret);
                    }

                    self.__form.remove();
                }
                return false;
            })
            .css({position: 'absolute', right: 10, bottom: 10, width: 80})
            .appendTo(this.__form)
            ;

    $('<button />')
            .text('CANCEL')
            .click(function ()
            {
                self.__form.remove();
                return false;
            })
            .css({position: 'absolute', right: 10, bottom: 40, width: 80})
            .appendTo(this.__form)
            ;

    for (var i in options)
    {
        var option = options[i];

        option.$.addClass('fly-form-options');
        option.$.appendTo(this.__form);
        if (i == 0)
            option.$.focus();
        option.$.attr('key', option.key);
        if (option.req)
            option.$.attr('req', 'true');

        option.$.keyup(function (e)
        {
            if (e.keyCode == KEY_ENTER)
                OK.click();
        });
    }
};

mbcms.visual_fast_edit.fly_form.__get_values = function ()
{
    var ret = {};

    this.__form
            .find('.fly-form-options')
            .each(function ()
            {
                if ($(this).attr('req') === 'true' && $.trim($(this).val()) == '')
                {
                    ret = false;
                    return false;
                }

                var key = $(this).attr('key');
                var value = $(this).val();
                ret[key] = value;
            })
            ;

    return ret;
};