/**
 * Created by user on 24.04.2017.
 */


function pseudo_cron_start()
{
}

pseudo_cron_start.start = function ()
{
    pseudo_cron_start.msg('Поехали. Считаем города');
    pseudo_cron_start.cities();
};

pseudo_cron_start.cities = function ()
{
    site.ajax({
        url: '/rabota/bycity/?fc=1',
        success: function ()
        {
            pseudo_cron_start.msg('Считаем профессии', 'Посчитали количество по городам (общее)');
            pseudo_cron_start.prof_count();
        }
    });
};

pseudo_cron_start.prof_count = function ()
{
    site.ajax({
        data: {class: 'trud\\admin\\pseudo_cron->by_professions'},
        success: function ()
        {
            pseudo_cron_start.msg('Считаем синонимы', 'Посчитали профессии');
            pseudo_cron_start.synonyms_count();
        }
    });
};

pseudo_cron_start.synonyms_count = function ()
{
    site.ajax({
        data: {class: 'trud\\admin\\pseudo_cron->ajax_recount_one'},
        success: function ()
        {
            pseudo_cron_start.msg('Получаем города для синонимов', 'Посчитали синонимы');
            pseudo_cron_start.get_cities();
        }
    });
};

pseudo_cron_start.get_cities = function ()
{
    site.ajax({
        data: {class: 'trud\\admin\\pseudo_cron->get_cities'},
        success: function (msg)
        {
            pseudo_cron_start.__cities = get_req(msg).cities;
            pseudo_cron_start.msg('Считаем синонимы');
            pseudo_cron_start.cities_synonyms(0);
        }
    });
};

pseudo_cron_start.cities_synonyms = function (index)
{
    var current_id = isset(pseudo_cron_start.__cities, index) ? pseudo_cron_start.__cities[index].id : null;

    if (current_id !== null)
    {
        site.ajax({
            data: {
                class: 'trud\\admin\\pseudo_cron->ajax_recount_two',
                city_id: current_id
            },
            success: function (msg)
            {
                get_req(msg);
                pseudo_cron_start.msg('Считаем синонимы по городу: ' + index + ' из ' + pseudo_cron_start.__cities.length + ' '
                    + pseudo_cron_start.__cities[index].name_ru + ' id: ' + pseudo_cron_start.__cities[index].id);

                if (isset(pseudo_cron_start.__cities, index))
                {
                    index = parseInt(index) + 1;
                    pseudo_cron_start.cities_synonyms(index);
                }
            }
        });
    }
    else
    {
        // удаление вакансий
        pseudo_cron_start.msg('Деактивация старых вакансий.', 'Пересчет синонимов по городам окончен.');
        pseudo_cron_start.remove_old_vacancies();
    }

};

pseudo_cron_start.remove_old_vacancies = function ()
{
    site.ajax({
        data: {class: 'trud\\admin\\pseudo_cron->delete_old_vacancies'},
        success: function ()
        {
            pseudo_cron_start.msg('Физическое удаление удаленных вакансий (3-дневной давности)', 'Старые вакансии скрыты из поиска.');
            pseudo_cron_start.remove_deleted_vacancies();
        }
    });
};

pseudo_cron_start.remove_deleted_vacancies = function ()
{
    site.ajax({
        data: {class: 'trud\\admin\\pseudo_cron->delete_old_vacancies'},
        success: function ()
        {
            pseudo_cron_start.msg('Подготовка к ежедневной рассылке', 'Удаленные вакансии уничтожены');
            pseudo_cron_start.msg('Удаление старых файлов и записей', 'Обновили просмотры резюме и количество бесплатных публикаций');
            pseudo_cron_start.get_all_emails();
        }
    });
};

pseudo_cron_start.get_all_emails = function ()
{
    site.ajax({
        data: {class: 'trud\\admin\\pseudo_cron->search_agents_get_all'},
        success: function (msg)
        {
            var req = get_req(msg);
            req.emails = req.emails || [];
            pseudo_cron_start.__all_emails = req.emails || [];
            pseudo_cron_start.send_email(0);
        }
    });
};

pseudo_cron_start.send_email = function (index)
{
    if (isset(pseudo_cron_start.__all_emails, index))
    {
        var agent_id = pseudo_cron_start.__all_emails[index];
        setTimeout(function ()
        {
            site.ajax({
                data: {
                    class: 'trud\\admin\\pseudo_cron->search_agents_send',
                    agent_id: agent_id
                },
                success: function ()
                {
                    pseudo_cron_start.msg('Рассылка вакансий и резюме: ' + index + ' из ' + pseudo_cron_start.__all_emails.length);
                    pseudo_cron_start.send_email(parseInt(index) + 1);
                }
            });
        }, 350);
    }
    else
    {
        pseudo_cron_start.msg('Формирование xml карты', 'Письма разосланы');
        pseudo_cron_start.sitemap();
    }

};


pseudo_cron_start.sitemap = function ()
{
    site.ajax({
        data: {class: 'trud\\admin\\pseudo_cron->sitemap'},
        success: function (msg)
        {
           get_req(msg);
           pseudo_cron_start.end_message('Карта для поисковика сформирована');
        }
    });
};

pseudo_cron_start.msg = function (msg, complete)
{
    complete = complete || '';
    this.text = this.text || '';
    if (complete)
    {
        this.text += '- ' + complete + '<br>';
    }

    return site.messages.factory('pseudocronius js').create().append_content($('<div class="static_width" />').html(this.text + msg)).locked(true);
};

pseudo_cron_start.end_message = function (message)
{
    pseudo_cron_start.msg('', message + '<div style="color: green"> -Закончил!!!</div> (Некоторые операции доступны не всегда,' +
        ' имеют ограничение по частоте вызовов. Отправка писем например или пересчет синонимов по городам.)').locked(false);
};

new event.site.load().listen(function ()
{
    $('<button />')
        .css({
            position: 'fixed',
            right: 20,
            bottom: 20,
            padding: '10px 20px',
            cursor: 'pointer',
        })
        .text('Важные расчеты')
        .appendTo('body')
        .click(function ()
        {
            pseudo_cron_start.start();
            return false;
        })
    ;
});
