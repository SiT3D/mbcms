/**
 * Created by user on 18.04.2017.
 */

function moder()
{
}

moder.create_panel = function (text, callback)
{
    var a = $('<div />');

    $('<div />').text('Сообщение для письма');

    var textarea = $('<textarea />')
        .val(text)
        .attr('id', 'text_from_message_moder')
        .attr('ckeditor', true)
        .appendTo(a);

    $('<br>').appendTo(a);

    $('<button />')
        .text('Отправить')
        .click(function ()
        {
            if (is_callable(callback))
            {
                callback.call(callback);
            }

            return false;
        })
        .addClass('trud-btn')
        .appendTo(a);

    ck_editor.init(a);
    site.messages.factory('messgaes_from email').create().append_content(a);
};


new event.site.load().listen(function ()
{


    $('.true_confirmes')
        .click(function ()
        {
            var self = $(this);
            var type = $(this).attr('_t');
            var text = '';

            if (type == 'vacancy')
            {
                text = 'Уважаемый пользователь портала Trud.Net Наш модератор проверил размещенную вами вакансию и ' +
                    'опубликовал ее на портале. Теперь вы можете самостоятельно управлять подтвержденной вакансией ' +
                    'в вашем личном кабинете на <a href="http://trud.net">Trud.Net</a> С уважением, команда Trud.Net';
            }
            else if (type == 'resume')
            {
                text = 'Уважаемый пользователь портала Trud.Net Наш модератор проверил размещенное вами резюме и ' +
                    'опубликовал ее на портале. Теперь вы можете самостоятельно управлять подтвержденным резюме ' +
                    'в вашем личном кабинете на <a href="http://trud.net">Trud.Net</a> С уважением, команда Trud.Net';
            }
            else if (type == 'company')
            {
                text = 'Вашей компании был присвоен статус "проверенной компании" на портале trud.net. С уважением, команда Trud.Net';
            }

            var cid = $(this).attr('cid');

            moder.create_panel(text, function ()
            {

                site.ajax(
                    {
                        method: 'POST',
                        data: {
                            class: 'trud\\admin\\templates\\admin_moderation->ajax_remove_confirme',
                            id: cid,
                            message: ck_editor.get_value('text_from_message_moder')
                        },
                        success: function (msg)
                        {
                            get_req(msg);
                            self.parents('tr:first').remove();
                            site.messages.factory('messgaes_from email').remove();
                            site.messages.factory('messgaes_from email_after').create().append_content('Сообщение отправлено');
                        }
                    }
                );
            });

            return false;

        })
    ;


    $('.false_confirmes')
        .click(function ()
        {
            var type = $(this).attr('_t');
            var text = '';

            if (type == 'vacancy')
            {
                text = 'Уважаемый пользователь портала Trud.Net Наш модератор проверил размещенную вами вакансию и ' +
                    'принял решение удалить ее из системы. На наш взгляд она не соответсвует правилам изложенным в нашей ' +
                    'публичной оферте. С уважением, команда Trud.Net';
            }
            else if (type == 'resume')
            {
                text = 'Уважаемый пользователь портала Trud.Net Наш модератор проверил размещенное вами резюме и ' +
                    'принял решение удалить его из системы. На наш взгляд она не соответсвует правилам изложенным в нашей ' +
                    'публичной оферте. С уважением, команда Trud.Net';
            }
            else if (type == 'company')
            {
                text = 'Ваша компания вызывает у нас подозрение, мы не можем присвоить вам статус проверенной компании. С уважением, команда Trud.Net';
            }

            var cid = $(this).attr('cid');

            moder.create_panel(text, function ()
            {
                site.ajax(
                    {
                        method: 'POST',
                        data: {
                            class: 'trud\\admin\\templates\\admin_moderation->ajax_send_message',
                            id: cid,
                            message: ck_editor.get_value('text_from_message_moder')
                        },
                        success: function (msg)
                        {
                            get_req(msg);
                            site.messages.factory('messgaes_from email').remove();
                            site.messages.factory('messgaes_from email_after').create().append_content('Сообщение отправлено');
                        }
                    }
                );
            });

            return false;

        })
    ;
});