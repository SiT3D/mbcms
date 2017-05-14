/**
 * Created by user on 22.02.2017.
 */


event.messages_remove = function ()
{
    this.message_name = null;
    this.__key = 'event.messages_remove'; // уникальный идентификатор, прсто название класса!
    this.content = null;

    this.set_message_name = function (message_name)
    {
        this.message_name = message_name;
        return this;
    };
};
event.messages_remove.prototype = Object.create(event.prototype);

event.messages_create = function ()
{
    this.message_name = null;
    this.__key = 'event.messages_create'; // уникальный идентификатор, прсто название класса!
    this.content = null;

    this.set_message_name = function (message_name)
    {
        this.message_name = message_name;
        return this;
    };
};
event.messages_create.prototype = Object.create(event.prototype);

/**
 * site.messages.factory('registration_errors').create().append_content(content);
 * @param name
 * @returns {site}
 */
site.messages = function (name)
{
    site.messages.__forms[name] = this;
    this.__name = name;
    return this;
};

site.messages.__forms = {};
site.messages.prototype.__time = 400;
site.messages.prototype.__close_delay = 0;
site.messages.prototype.__locked = false;

site.messages.prototype.get_time = function ()
{
    return this.__time;
};

/**
 *
 * @param time_ms
 * @returns {site.messages}
 */
site.messages.prototype.close_delay = function (time_ms)
{
    this.__close_delay = time_ms;
    return this;
};

/**
 * Если передать true то нельзя будет закрыть элемент
 * @param value
 * @returns {site.messages}
 */
site.messages.prototype.locked = function (value)
{
    this.__locked = value;

    if (value)
    {
        this.__closer.hide();
    }
    else
    {
        this.__closer.show();
    }
    return this;
};

site.messages.prototype.css = function (css)
{
    this.__form.css(css);
};

site.messages.prototype.create = function (time)
{
    time = this.__time = time || this.__time;
    this.remove(time, true);
    var self = this;

    $('body').addClass('locked-scroll');

    this.__form = $('<div  />')
        .addClass('trud-forms-form')
        .click(function (e)
        {
            e.stopPropagation();
        })
        .css({borderRadius: '10px'})
    ;

    this.__closer = $('<div/>')
        .appendTo(this.__form)
        .text('x')
        .click(function ()
        {
            self.remove();
            return false;
        })
        .addClass('messages-hover-closer')
    ;

    this.__bg = $('<div />')
        .addClass('trud-forms-bg')
        .click(function ()
        {
            self.remove(time);
            return this;
        })
    ;

    $(document)
        .keyup(function (e)
        {
            if (e.keyCode == KEY_ESC)
            {
                self.remove(time);
            }
        });

    this.__form.appendTo(this.__bg);
    this.__bg.appendTo('body');

    this.__bg.hide();
    this.__bg.fadeIn(time);

    var evt = new event.messages_create().set_message_name(this.__name).call();
    evt.content = this.__content;

    return this;
};

/**
 *
 * @param time - miliseconds 1000 = 1 sec
 * @param create
 * @returns {site.messages}
 */
site.messages.prototype.remove = function (time, create)
{
    create = create || false;
    time = time || this.__time;
    var self = this;

    if (this.__locked && !create)
    {
        return;
    }

    if (isset(this.__form, 'remove'))
    {

        if (this.interval)
        {
            return;
        }

        if (!create)
        {

            if (this.__close_delay > 0)
            {
                this.n = parseInt(self.__close_delay / 1000);
                self.__closer.text(this.n);

                this.interval = setInterval(function ()
                {
                    self.n--;
                    self.__closer.text(self.n);

                    if (self.n <= 0)
                    {
                        clearInterval(self.interval);
                        self.interval = null;
                    }

                }, 1000);
            }


            __delay(this, 'delayfrommessagescloser', self.__close_delay, function ()
            {
                self.__bg.fadeOut(time, function ()
                {
                    if ($('.trud-forms-bg').length <= 1)
                    {
                        $('body').removeClass('locked-scroll');
                    }

                    var evt = new event.messages_remove().set_message_name(self.__name).call();
                    evt.content = self.__content;

                    $(this).remove();
                });

                delete this.__form;
                delete this.__bg;
                delete this.__content;
                delete this.__closer;

            });

        }
        else
        {
            self.__bg.fadeOut(time, function ()
            {
                if ($('.trud-forms-bg').length <= 1)
                {
                    $('body').removeClass('locked-scroll');
                }

                var evt = new event.messages_remove().set_message_name(self.__name).call();
                evt.content = self.__content;

                $(this).remove();
            });

            delete this.__form;
            delete this.__bg;
            delete this.__content;
            delete this.__closer;
        }

    }

    return this;
};

site.messages.prototype.is_active = function ()
{
    return isset(this, '__bg');
};

/**
 *
 * @param {jQuery} jq_content
 * @returns {site.messages.prototype}
 */
site.messages.prototype.append_content = function (jq_content)
{
    this.__content = jq_content;
    this.__form.append(jq_content);

    return this;
};

site.messages.prototype.get_content = function ()
{
    return this.__content;
};

/**
 *
 * @returns {jQuery}
 */
site.messages.prototype.get_form = function ()
{
    return this.__form;
};

/**
 * @param {string} name это уникальный идентификатор по которому можно обратиться к данному диалогу.
 * site.messages.factory('id') - вернет уже существующий диалог, или же создаст новый.
 * @returns {*}
 */
site.messages.factory = function (name)
{
    if (isset(this.__forms, name))
    {
        return this.__forms[name];
    }

    return new site.messages(name);
};
