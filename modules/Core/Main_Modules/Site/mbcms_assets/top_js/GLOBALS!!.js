function admin_globals()
{
}

$_GLOBALS = {};

KEY_ENTER = 13;
KEY_DEL = 46;
KEY_SHIFT = 16;
KEY_CTRL = 17;
KEY_ALT = 18;
KEY_ESC = 27;
KEY_F1 = 112;
KEY_F2 = 113;
KEY_F3 = 114;
KEY_F4 = 115;
KEY_F5 = 116;
KEY_F6 = 117;
KEY_F7 = 118;
KEY_F8 = 119;
KEY_F9 = 120;
KEY_F10 = 121;
KEY_F11 = 122;
KEY_F12 = 123;

KEY_A = 65;
KEY_B = 66;
KEY_C = 67;
KEY_D = 68;
KEY_E = 69;
KEY_F = 70;
KEY_G = 71;
KEY_H = 72;
KEY_I = 73;
KEY_J = 74;
KEY_K = 75;
KEY_L = 76;
KEY_M = 77;
KEY_N = 78;
KEY_O = 79;
KEY_P = 80;
KEY_Q = 81;
KEY_R = 82;
KEY_S = 83;
KEY_T = 84;
KEY_U = 85;
KEY_V = 86;
KEY_W = 87;
KEY_X = 88;
KEY_Y = 89;
KEY_Z = 90;

KEY_WIN_LEFT = 91;
KEY_WIN_RIGHT = 92;

KEY_NUMS_0 = 96;
KEY_NUMS_1 = 97;
KEY_NUMS_2 = 98;
KEY_NUMS_3 = 99;
KEY_NUMS_4 = 100;
KEY_NUMS_5 = 101;
KEY_NUMS_6 = 102;
KEY_NUMS_7 = 103;
KEY_NUMS_8 = 104;
KEY_NUMS_9 = 105;

KEY_STAR = 106;
KEY_PLUS = 107;
KEY_MINUS = 109;
KEY_DOT = 110;
KEY_SLASH = 111;
KEY_NUMLOCK = 144;
KEY_SCROLLLOCK = 145;
KEY_SCROLLLOCK = 145;
KEY_PRINTSCR = 154;
KEY_BACKSPACE = 8;

/**
 *
 * @type Number ;
 */
KEY_S1 = 186;
/**
 *
 * @type Number =
 */
KEY_S2 = 187;
/**
 *
 * @type Number ,
 */
KEY_S3 = 188;
/**
 *
 * @type Number -
 */
KEY_S4 = 189;
/**
 *
 * @type Number .
 */
KEY_S5 = 190;
/**
 *
 * @type Number /
 */
KEY_S6 = 191;
KEY_TILDA = 192;


KEY_SQL = 219;
KEY_SQR = 221;
KEY_BACKSLASH = 220;
KEY_APPOSTROF = 222;

IS_SHIFT = false;
IS_CTRL = false;
IS_ALT = false;

KEY_TAB = 9;
KEY_1 = 49;
KEY_2 = 50;
KEY_3 = 51;
KEY_4 = 52;
KEY_5 = 53;
KEY_6 = 54;
KEY_7 = 55;
KEY_8 = 56;
KEY_9 = 57;
KEY_0 = 48;
KEY_UP = 38;
KEY_LEFT = 37;
KEY_RIGHT = 39;
KEY_DOWN = 40;
KEY_SPACE = 32;


/**
 *
 * @param {type} obj
 * следом параметры через запятую, значения которые необходимо искать по вложености
 * @returns {Boolean}
 */
isset = function (obj)
{
    var i, max_i;
    if (obj === undefined || obj === null)
    {
        return false;
    }

    for (i = 1, max_i = arguments.length; i < max_i; i++)
    {
        if (obj[arguments[i]] === undefined)
        {
            return false;
        }

        obj = obj[arguments[i]];
    }
    return true;
};

/**
 * Метод преобразует полученный JSON от сервера в объект.
 * Если пришел не json и это дев версия продукта или live то, будет выведена ошибка в консоль, с текстом от сервера, и произойдет alert()
 * @param msg
 * @returns {*}
 */
get_req = function (msg)
{
    var __req = null;

    try
    {
        if (typeof msg == 'object')
        {
            __req = msg;

            if (isset(msg, 'responseText') && !site.is_static_templates())
            {
                alert('Возможная ошибка на сервере');
                console.log(msg.responseText);
            }
        }
        else
        {
            try
            {
                __req = __req || $.parseJSON(msg);
            }
            catch (e)
            {
                if (!site.is_static_templates())
                {
                    alert('Ошибка на сервере');
                    console.log(msg);
                }
            }
        }
    }
    catch (e)
    {
        if (!site.is_static_templates())
        {
            console.log(e);
        }
    }

    __req = __req || {};

    return __req;
};

/**
 *
 * @param {type} string // padding-left = paddingLeft, etc
 * @returns {undefined}
 */
convert_css_in_key = function (string)
{
    return string.replace(/\-(.)/, function (a, b)
    {
        return b.toUpperCase();
    });
};

(function ()
{
    $(document)
        .keydown(function (e)
        {
            if (e.shiftKey)
                IS_SHIFT = true;
            if (e.ctrlKey)
                IS_CTRL = true;
            if (e.altKey)
                IS_ALT = true;
        })
        .keyup(function (e)
        {
            if (!e.shiftKey)
                IS_SHIFT = false;
            if (!e.ctrlKey)
                IS_CTRL = false;
            if (!e.altKey)
                IS_ALT = false;
        })
    ;
})();

if (typeof mbcms !== 'undefined')
{
    /**
     *
     * @param {type} ajaxData /// example {type: 'POST', data: {}, success: function(){}}
     * @returns {undefined}
     */
    mbcms.ajax = function (ajaxData, adm_status)
    {
        adm_status = typeof adm_status == 'undefined' ? true : adm_status;

        ajaxData.url = '/ajax';

        if (adm_status)
        {
            if (ajaxData['data'] instanceof FormData)
            {
                ajaxData['data'].append(mbcms.ADMIN_STATUS, true);
            }
            else
            {
                ajaxData['data'][mbcms.ADMIN_STATUS] = true;
            }
        }

        $.ajax(ajaxData);
    };

    mbcms.stopPropagation = function (html)
    {
        if (typeof $(html).data('mbcms_stopPropagation') == 'undefined')
        {
            $(html)
                .click(function (e)
                {
                    e.stopPropagation();
                    e.preventDefault();
                })
                .mousedown(function (e)
                {
                    e.preventDefault();
                })
                .mouseup(function (e)
                {
                    e.preventDefault();
                })
                .data('mbcms_stopPropagation', true)
                .removeAttr('onclick')
            ;

            return false;
        }
    };
}

/**
 * Проверяет что это функция
 *
 * @param {type} variable
 * @returns {boolean}
 */
function is_callable(variable)
{
    return typeof variable == 'function';
}

function is_nan(val)
{
    return /(\d+|\d+\.\d+|\.\d+)([eE][-+]?\d+)?/.test(val);
}

/**
 *
 * @param {type} obj
 * @param {type} key
 * @param {type} time
 * @param {type} callback
 * @returns {undefined}
 */
function __delay(obj, key, time, callback)
{
    if (isset(obj, key))
    {
        clearTimeout(obj[key]);
    }

    obj[key] = setTimeout(callback, time);
}
;


(function ($)
{
    $.fn.getAttributes = function ()
    {
        var attributes = {};

        if (this.length)
        {
            $.each(this[0].attributes, function (index, attr)
            {
                attributes[attr.name] = attr.value;
            });
        }

        return attributes;
    };
})(jQuery);
