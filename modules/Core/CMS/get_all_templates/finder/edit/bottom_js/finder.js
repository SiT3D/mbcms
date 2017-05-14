/* global mbcms */

(function ()
{
    
    var rus = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'];
    var eng = ['f', ',', 'd', 'u', 'l', 't', '`', ';', 'p', 'b', 'q', 'r', 'k', 'v', 'y', 'j', 'g', 'h', 'c', 'n', 'e', 'a', '\\\[', 'w', 'x', 'i', 'o', '\\\]', 's', 'm', '\\\'', '\\\.', 'z'];

    function autotranslate(text, toLang)
    {
        for (var i = 0; i < rus.length; i++)
        {
            var preg;
            if (toLang === 'eng')
            {
                preg = new RegExp(rus[i], 'ig');
                text = text.replace(preg, eng[i]);
            }
            else
            {
                preg = new RegExp(eng[i], 'ig');
                text = text.replace(preg, rus[i]);
            }
        }

        return text;
    }
    
    function autoWordSpecial(filterValue)
    {
        filterValue = filterValue.replace(/\$/g, ';');
        filterValue = filterValue.replace(/\[/g, '\\\[');
        filterValue = filterValue.replace(/\]/g, '\\\]');
        filterValue = filterValue.replace(/\{/g, '\\\{');
        filterValue = filterValue.replace(/\}/g, '\\\}');
        filterValue = filterValue.replace(/!/g, '|');
        filterValue = filterValue.replace(/;/g, '.*');
        return filterValue;
    }
    
    function pregFilter(filterValue, text)
    {
        filterValue = autoWordSpecial(filterValue);
        var preg = new RegExp(filterValue, 'ig');
        var result = preg.test(text);
        if (!result)
        {
            filterValue = autotranslate(filterValue, 'rus');
            preg = new RegExp(filterValue, 'ig');
            result = preg.test(text);

//            if (!result) // надо немного ни так походу, тут походу нужно искать еще раз \{ такие штуки и менять их \\\{
//            {
//                filterValue = autotranslate(filterValue, 'eng');
//                preg = new RegExp(filterValue, 'ig');
//                result = preg.test(text);
//            }
        }

        return result;
    }

    function pickOptions(filterValue, $module_take)
    {
        $module_take.find('.boxes-right-ul').each(function ()
        {
            var $hide = true;
            var value = $(this).text();
            if (typeof value === 'string')
            {
                if (pregFilter(filterValue, value))
                {
                    $hide = false;
                }
            }

            if ($hide)
            {
                $(this).hide();
            }
            else
            {
                $(this).show();
            }
        });
        
        $module_take.find('.template-folder').each(function ()
        {
            var $hide = true;
            var value = $(this).text();
            if (typeof value === 'string')
            {
                if (pregFilter(filterValue, value))
                {
                    $hide = false;
                }
            }

            if ($hide)
            {
                $(this).hide();
            }
            else
            {
                $(this).show();
            }
        });
    }

    mbcms.admin.tab.addListner(mbcms.admin.tab.MBCMS_ALL_ALL_TABS_INIT, function (info)
    {
        info.module.find('.template-finder').each(function ()
        {
            $(this).change(function ()
            {
                pickOptions($(this).val(), info.module);
            });

            var $this = $(this);
            $(this).next('.clear-templates-finder:first').click(function ()
            {
                $this.val('');
                $this.change();
            });
        });
    });

    $(document).keyup(function (e)
    {
        if ($('.template-finder:focus').length === 1)
        {
            var $main = $('.template-finder:first');
            if (typeof this.timeout !== 'undefined')
            {
                clearTimeout(this.timeout);
            }

            this.timeout = setTimeout(function ()
            {
                $main.change();
            }, 1000);

            if (e.keyCode === 13)
            {
                clearTimeout(this.timeout);
            }
        }
    }).keydown(function ()
    {
        if ($(':focus').length === 0)
        {
            var $fr = $('.template-finder:first');
            $fr.focus();
            $fr.val('');
        }
    });
})();


