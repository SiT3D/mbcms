function work_ua()
{
}

work_ua.__time = 2000;
work_ua.__counter = 0;

/**
 *
 * @param {string} url    work.ua
 * @param {function} callback
 * @returns {undefined}
 */
work_ua.prototype.start_company = function (url, callback)
{
    var user_id = user_picker.get_user_id();

    site.ajax(
        {

            data: {
                class: 'trud\\admin\\templates\\admin_work_parser->write_company',
                url: url,
                user_id: user_id
            },
            success: function (msg)
            {
                var req = get_req(msg);

                if (typeof callback == 'function')
                {
                    callback.call(callback, req);
                }
            }
        });
};


work_ua.print_errors = function (req)
{
    if (isset(req, 'errors'))
    {
        var text = '';
        if (req.errors.length > 0)
        {
            for (var i in req.errors)
            {
                if (req.errors[i])
                {
                    text += req.errors[i] + "\r";
                }
            }
        }

        site.messages.factory('message_length_work_links').create().append_content(text);

        return false;
    }

    return true;
};


/**
 *    ,
 *
 * @param {string} url    work.ua
 * @param {function} callback
 * @returns {undefined}
 */
work_ua.prototype.get_vac_links = function (url, callback)
{
    site.ajax(
        {
            data: {
                class: 'trud\\admin\\templates\\admin_work_parser->get_links',
                url: url
            },
            success: function (msg)
            {
                var req = get_req(msg);

                if (!work_ua.print_errors(req))
                {
                    return;
                }

                if (is_callable(callback))
                {
                    callback.call(callback, req);
                }
            }
        });
};

/**
 *
 * @param links
 * @param time
 * @param callback
 */
work_ua.prototype.start_vacancy_loader = function (links, time, callback)
{
    if (links == undefined)
    {
        return;
    }

    var link = links.shift();
    if (link == undefined)
    {
        if (typeof callback == 'function')
        {
            callback.call(callback);
        }
        return;
    }

    var user_id = user_picker.get_user_id();

    work_ua.__counter++;

    var self = this;
    this.start_vacancy_parse(link, user_id, time, function (req)
    {
        time = req.__have_vacancy == '1' ? 0 : work_ua.__time;

        if (isset(self, 'loaderInfo', 'remove'))
        {
            var time2 = time ? self.loaderInfo.get_time() : 3000;
            self.loaderInfo.remove(time2);
        }

        var count = links.length || 'Загрузка завершена';
        self.loaderInfo = site.messages.factory('message_length_work_links').create().append_content('Загрузка вакансий осталось: ' + count).locked(links.length);

        if (typeof req.a_employer_vacancy__table != 'undefined')
        {
            time = 0;
        }
        self.start_vacancy_loader(links, time, callback);
    });
};

/**
 *
 * @param {type} link
 * @param {type} user_id
 * @param {type} timeout
 * @param {type} callback
 * @returns {undefined}
 */
work_ua.prototype.start_vacancy_parse = function (link, user_id, timeout, callback)
{

    site.ajax(
        {
            data: {
                class: 'trud\\admin\\templates\\admin_work_parser->write_vacancy',
                url: link,
                user_id: user_id,
            },
            success: function (msg)
            {
                var req = get_req(msg);

                if (!work_ua.print_errors(req))
                {
                    return;
                }

                setTimeout(function ()
                {
                    if (typeof callback == 'function')
                    {
                        callback.call(callback, req);
                    }
                }, timeout);
            }
        });
};

work_ua.prototype.delete_old = function ()
{
    var soft_del = $('#delete_old_vacancies').prop('checked') ? 1 : 0;

    if (soft_del == 1)
    {
        $.ajax({
            data: {
                company_id: $('#companyid').val(),
                class: 'trud\\admin\\templates\\admin_work_parser->remove_old'
            },
            success: function ()
            {
                $('#delete_old_vacancies').prop('checked', false);
            }
        });
    }
};

new event.site.load().listen(function ()
{
    var get_all_vacancies_btn = $('#get_all_vacancy');

    get_all_vacancies_btn
        .click(function ()
        {
            var work = new work_ua();
            work
                .get_vac_links($('#work_ua_URL').val(), function (req)
                {
                    work_ua.__counter = 0;

                    work
                        .start_vacancy_loader(req.links, work_ua.__time, function ()
                        {
                            work.delete_old();
                        });
                });

            return false;
        });

    $('#create_company_btn')
        .click(function ()
        {
            var work = new work_ua();
            work
                .start_company($('#work_ua_URL').val(), function (req)
                {
                    if (!work_ua.print_errors(req))
                    {
                        return;
                    }

                    alert('Компания была создана');
                });

            return false;
        });

    user_picker.set_ajax_class('trud\\admin\\templates\\admin_work_parser\\user_picker->get');

    new event.user_picker.load().listen(function ()
    {
        var linksdb = this.req.linksdb;

        var input_with_url = $('#work_ua_URL');


        for (var i in this.req.linksdb)
        {
            input_with_url.val(this.req.linksdb[i] || '');
            break;
        }

        if (this.select.data('inited_this_script') == undefined)
        {
            this.select
                .change(function ()
                {
                    if (linksdb[$(this).val()] && linksdb[$(this).val()] != undefined)
                    {
                        input_with_url.val(linksdb[$(this).val()]);
                    }
                    else
                    {
                        input_with_url.val(linksdb[$(this).val('')]);
                    }
                })
                .data('inited_this_script', true)
            ;
        }

    });
});


