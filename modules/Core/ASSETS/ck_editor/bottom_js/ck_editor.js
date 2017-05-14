/**
 * Created by user on 14.03.2017.
 */


function ck_editor()
{
}

ck_editor.user_config = {
    toolbar: [
        ['Font'],
        '/',
        ['Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Undo', 'Redo', '-', 'Find', 'Replace', '-', 'Outdent', 'Indent', '-'],
        '/',
        ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
        ['TextColor', 'Maximize']
    ],
};

ck_editor.admin_config = {
    toolbar: [
        ['Format', 'Font', 'FontSize'],
        '/',
        ['Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Undo', 'Redo', '-', 'Find', 'Replace', '-', 'Outdent', 'Indent', '-', 'Print'],
        '/',
        ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
        ['Image', 'Link', 'TextColor', 'BGColor', 'Source', 'Maximize']
    ],
};

/**
 *
 * @type {{}} standart
 */
ck_editor.superadmin_config = {};

/**
 * Подключает ко всем элементам внутри parent_for_init у которых есть атрибут [ckeditor]
 * @param parent_for_init
 */
ck_editor.init = function (parent_for_init)
{
    parent_for_init = parent_for_init || false;


    if (!parent_for_init)
    {
        return;
    }

    if (!parent_for_init.find('[ckeditor]').length)
    {
        return;
    }


    clearInterval(this.__timer_ck_editor || false);

    var self = this;
    this.__interval_tick = 0;


    this.__timer_ck_editor = setInterval(function ()
    {
        if (!(parent_for_init instanceof jQuery))
        {
            return;
        }

        if (typeof CKEDITOR != 'undefined')
        {
            self.__interval_tick = 1000;
        }

        self.__interval_tick++;
        if (self.__interval_tick > 100)
        {
            clearInterval(self.__timer_ck_editor || false);
        }


        if (typeof CKEDITOR == 'undefined' && self.__interval_tick > 100)
        {
            if (!site.is_static_templates())
            {
                alert('Ошибка подключения ck ditor');
            }
            console.log('Not render ck_editor in HTML body. Please check module position in your parent module, who uses ckeditor module.');
        }
        else if (typeof CKEDITOR != 'undefined')
        {
            parent_for_init
                .find('[ckeditor]')
                .each(function ()
                {
                    var id = $(this).attr('id');
                    id = id || 'element' + parseInt(Math.random() * 10000);
                    $(this).prop('id', id);


                    if ($(this).attr('not_first_init_ckeditor') || false)
                    {
                        return;
                    }

                    $(this).removeAttr('ckeditor');

                    var config = configname = $(this).attr('cn');
                    if (config == 'u')
                    {
                        config = ck_editor.user_config;
                    }
                    else if (config == 'a')
                    {
                        config = ck_editor.admin_config;
                    }
                    else if (config == 'superadmin')
                    {
                        config = ck_editor.superadmin_config;
                    }

                    if (!config)
                    {
                        config = ck_editor.user_config;
                    }


                    CKEDITOR.replace(id, config);

                    var $this = $(this);

                    CKEDITOR.instances[id]
                        .on('change', function ()
                        {
                            $this.html(ck_editor.get_value(id));
                        });
                });
        }

    }, 20);

};

/**
 *
 * @param id не селектор просто строка с id  textarea к которой был прикреплен редактор
 * @returns {string|*}
 */
ck_editor.get_value = function (id)
{
    return CKEDITOR.instances[id].getData();
};