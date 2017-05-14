event.site.form = function ()
{
    this.__key = 'event.site.form'; // уникальный идентификатор, прсто название класса!
};
event.site.form.prototype = Object.create(event.prototype);

/**
 * Отправка данных на сервер из формы и их возвращение
 *
 * @returns {event.site.form.success}
 */
event.site.form.success = function ()
{
    this.__key = 'event.site.form.success'; // уникальный идентификатор, прсто название класса!
    this.form = null;
    this.msg = null;
    this.action = null;
    this.form_id = null;
    this.action_method = null;

    /**
     * Полученный объект
     * @type {null}
     * @private
     */
    this.__req = null;

    this.print_errors = function ()
    {
        this.get_req();
        this.__req.errors = this.__req.errors || false;

        if (this.__req.errors)
        {
            var text = '';

            for (var i in this.__req.errors)
            {
                var er = isset(this.__req.errors, i, '0') ? (this.__req.errors[i][0] ? this.__req.errors[i][0] : i) : '';
                text += er ? er + "\n" + '<br>' : '';
            }

            site.messages.factory(site.form.message_name).create().append_content(text);
        }
    };

    this.is_errors = function ()
    {
        var bol = false;

        this.get_req();
        this.__req.errors = this.__req.errors || false;

        if (this.__req.errors)
        {

            for (var i in this.__req.errors)
            {
                if (this.__req.errors[i].length > 0)
                {
                    bol = true;
                }
            }
        }

        return bol;
    };

    this.get_req = function ()
    {
        return this.__req = this.__req || get_req(this.msg);
    };

    this.redirect = function ()
    {
        this.get_req();
        this.__req.__redirect = this.__req.__redirect || '';
        if (this.__req.__redirect)
        {
            location.href = this.__req.__redirect;
        }
        else
        {
            location.reload();
        }
    };

    this.print_upload_errors = function ()
    {
        this.get_req();
        this.__req.upload_errors = this.__req.upload_errors || false;

        if (this.__req.upload_errors)
        {
            var text = '';

            var ext = this.__req.upload_errors['ext'];
            var size = this.__req.upload_errors['size'];

            for (var i in ext)
            {
                var formats = ext[i].join(',');
                text += 'Неверный формат файла "' + i + '" Допустимые форматы: [' + formats + ']';
            }

            for (var i in size)
            {
                var current = size[i][0];
                var max = size[i][1];
                text += 'Размер файла: "' + i + '" [' + current + 'кб] привышает допустимый [' + max + 'кб].';
            }


            if (text)
            {
                site.messages.factory(site.form.message_name).create().append_content(text);
                return true;
            }
        }

        return false;

    };
};
event.site.form.success.prototype = Object.create(event.prototype);


event.site.form.before_send = function ()
{
    this.__key = 'event.site.form.before_send'; // уникальный идентификатор, прсто название класса!
    this.data = null;
    this.__abort = false;
    this.form_id = null;
    this.action_method = null;

    this.abort = function ()
    {
        this.__abort = true;
        return this;
    };

};
event.site.form.before_send.prototype = Object.create(event.prototype);

site.form = function ()
{
    this.__send = function (jq_btn)
    {
        var form = jq_btn.parents('form:first');
        var data = form.serializeArray();

        var before = new event.site.form.before_send();
        before.form_id = form.attr('id');
        before.action = form.find('[name=class]').attr('value');
        before.action_method = isset(before.action.split('->'), '1') ? before.action.split('->')[1] : null;
        before.data = data;
        before.call(false);

        if (before.__abort)
        {
            return;
        }


        function call(msg)
        {
            var evt = new event.site.form.success().call();
            evt.form = form;
            evt.msg = msg;
            evt.form_id = form.attr('id');
            evt.action = form.find('[name=class]').attr('value');
            evt.action_method = isset(evt.action.split('->'), '1') ? evt.action.split('->')[1] : null;
        }

        if (form.find('[type=file]').length > 0)
        {
            site.ajaxf(
                {
                    success: function (msg)
                    {
                        call(msg);
                    },
                    error: function (msg)
                    {
                        call(msg);
                    }
                }, form);
        }
        else
        {
            site.ajax(
                {
                    data: data,
                    method: form.attr('method'),
                    success: function (msg)
                    {
                        call(msg);
                    },
                    error: function (msg)
                    {
                        call(msg);
                    }
                });
        }


        return this;
    };
};

/**
 * Имя для вывода сообщений формы. Если подставить его, то оно будет заменяться.
 * @type {string}
 */
site.form.message_name = 'form_errors';

site.form.prototype.init = function (jq_form)
{
    var self = this;

    jq_form = jq_form || $('.mbcms-form:not([not_ajax])');

    jq_form
        .submit(function ()
        {
            return false;
        })
        .find('[type=submit]')
        .click(function ()
        {
            self.__send($(this));
            return false;
        })
    ;

    $('.mbcms-form').find('button')
        .click(function ()
        {
            return false;
        });

    return this;
};

/**
 *
 * @returns {site.form}
 */
site.form.factory = function ()
{
    return this.__this = this.__this || new site.form;
};

site.form.init = function (jq_form)
{
    site.form.factory().init(jq_form);
    $('[chosen]').chosen({disable_search_threshold: 10});
};

new event.site.load().listen(function ()
{
    site.form.init();
});