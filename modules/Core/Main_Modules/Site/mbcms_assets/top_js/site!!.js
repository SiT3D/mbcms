function site()
{
}

/**
 * is_static_templates
 * @type {undefined}
 */
site.configuration = undefined;

/**
 *
 * @param {type} ajaxOb
 * @returns {undefined}
 */
site.ajax = function (ajaxOb)
{
    ajaxOb.url = ajaxOb.url || '/ajax';
    ajaxOb.method = ajaxOb.method || 'POST';
    ajaxOb.success = ajaxOb.success || function(msg){get_req(msg)};

    $.ajax(ajaxOb);
};

/**
 * Для отправки файлов
 *
 * @param {type} ajaxOb
 * @param {$} form
 * @returns {undefined}
 */
site.ajaxf = function (ajaxOb, form)
{
    ajaxOb.url = ajaxOb.url || '/ajax';
    ajaxOb.method = 'POST';
    ajaxOb.processData = false;
    ajaxOb.contentType = false;
    ajaxOb.dataType = 'json';
    ajaxOb.data = new FormData(form.get(0));
    ajaxOb.success = ajaxOb.success || function(msg){get_req(msg)};

    $.ajax(ajaxOb);
};


site.is_static_templates = function ()
{
    try
    {
        if (mbcms != undefined)
        {
            return true;
        }
    }
    catch (e)
    {

    }


    if (site.configuration == undefined)
    {
        return false;
    }

    //noinspection RedundantIfStatementJS
    if (!site.configuration.is_static_templates || site.configuration.is_static_templates == 'live')
    {
        return false;
    }

    return true;
};

(function ()
{
    $('.bottom_js').each(function ()
    {
        var script = '';

        if ($(this).hasClass('script'))
        {
            script = $('<script/>').appendTo('html').attr('src', $.trim($(this).text()));
        }
        else
        {
            script = $('<script/>').appendTo('html').text($(this).text());
        }

        if ($(this).hasClass('remove'))
        {
            script.remove();
        }

        $(this).remove();
    });

    $('.top_js').each(function ()
    {

        var script = '';

        if ($(this).hasClass('script'))
        {
            script = $('<script/>').appendTo('head').attr('src', $.trim($(this).text()));
        }
        else
        {
            script = $('<script/>').appendTo('head').text($(this).text());
        }



        if ($(this).hasClass('remove'))
        {
            script.remove();
        }

        $(this).remove();
    });

})();
