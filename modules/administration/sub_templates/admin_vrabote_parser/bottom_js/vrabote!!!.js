function vrabote()
{
}

vrabote.prototype.__time_cat = 3000;
vrabote.prototype.__time_vac = 1000;
vrabote.prototype.__class = 'trud\\admin\\templates\\admin_vrabote_parser';
vrabote.prototype.__categories_array = [];
vrabote.prototype.__categories_alias_array = [];
vrabote.prototype.__vacancies_array = [];
vrabote.prototype.__current_category_alias = [];


vrabote.prototype.msg = function (msg, locked)
{
    locked = locked !== false;
    site.messages.factory('parservrabote').create().append_content(msg).locked(locked);
};

/**
 * Возвращает все ссылки на вакансии по категориям
 *
 * @returns {undefined}
 */
vrabote.prototype.get_all_categories = function (categories, categories_alias)
{
    var self = this;

    self.__categories_array = categories;
    self.__categories_alias_array = categories_alias;
    self.each_categories();
    this.msg('Начали');
};

/**
 * Запускает сбор информации, со страниц категорий, постранично
 *
 * @returns {undefined}
 */
vrabote.prototype.each_categories = function ()
{
    if (this.__categories_array.length <= 0)
    {
        this.msg('Готово', false);
        return;
    }

    var self = this;
    var url = this.__categories_array.shift();
    this.__current_category_alias = this.__categories_alias_array.shift();
    self.__next_page = null;

    console.log('url:' + url);

    this.get_category_page_vacansies(url, function (links)
    {
        self.__vacancies_array = links.vacancies;
        self.__next_page = links.next_page;
        self.each_vacansies();
    });
};


vrabote.prototype.each_vacansies = function ()
{
    var self = this;


    if (this.__vacancies_array.length <= 0)
    {
        if (this.__next_page && this.__next_page != undefined)
        {
            this.get_category_page_vacansies(this.__next_page, function (links)
            {
                self.__vacancies_array = links.vacancies;
                self.__next_page = links.next_page;
                self.each_vacansies();
            });
        }
        else
        {
            setTimeout(function ()
            {
                self.each_categories(self.__categories_array);
            }, self.__time_cat);
        }

        return;
    }

    var url = this.__vacancies_array.shift();
    this.msg('url:' + url + ' осталось: ' + this.__vacancies_array.length);

    setTimeout(function ()
    {
        self.read_and_write_vacancy(url);
    }, this.__time_vac);

};


vrabote.prototype.read_and_write_vacancy = function (url)
{
    var self = this;

    site.ajax(
        {
            data: {
                class: this.__class + '->write_vacancy',
                url: url,
                category: this.__current_category_alias
            },
            success: function (msg)
            {
                console.log(get_req(msg));
                self.each_vacansies();
            }
        });
};


/**
 *
 * Возвращает ссылки на вакансии, с указанной страницы категрии
 *
 * @param {type} url
 * @param {type} callback
 * @returns {undefined}
 */
vrabote.prototype.get_category_page_vacansies = function (url, callback)
{
    site.ajax(
        {
            data: {
                class: this.__class + '->parse_category',
                url: url
            },
            success: function (msg)
            {
                var req = get_req(msg);

                console.log(req);

                if (typeof callback == 'function')
                {
                    callback.call(callback, req);
                }
            }
        });
};



