/* global mbcms */

/**
 * Класс для работы с опциями // Options\option
 * 
 * @returns {undefined}
 */
option = function ()
{
};


option.typeCallbacks = {};
option.typeCallbacksSet = {};
option.eventCallbacks = {};
option.dinamicCallbacks = {};
option.dinamicCallbacksClonesStack = {};
option.__regEvent = {};
option.__initListners = [];
option.__fast_edit_callbacks = {};



option.init_option_block = function ($option_block, window)
{
    this.__init_groups($option_block); // тут может не id а селектор, или объект после которого или в который вставлять все эти чудеса!!
    this.__init_dinamic_event($option_block, window);
    // инит от каждой опции еще нужно запускать!!! для каждого окна
    // опции могут подписываться сюда... и тут инитить дополнительные свои функции.. 
    this.__call_init_listners($option_block, window); // тут ошибка!!
};

option.__call_init_listners = function ($option_block, window)
{
    this.__initListners[i]
    for (var i in this.__initListners)
    {
        if (typeof this.__initListners[i] === 'function')
        {
            this.__initListners[i]($option_block, window);
        }
    }
};

/**
 * 
 * @param {function} callback /// function($option_block, window)
 * @returns {undefined}
 */
option.addInitListner = function (callback)
{
    this.__initListners.push(callback);
};

option.__init_dinamic_event = function ($option_block, window)
{
    for (var mtype in option.eventCallbacks)
    {
        $option_block.find('.settings-editor-info-module[mtype="' + mtype + '"]').each(function ()
        {
            if (typeof option.eventCallbacks[mtype] === 'function')
            {
                option.eventCallbacks[mtype]($(this), window);
            }
        });
    }
};

option.__init_groups = function ($option_block)
{
    var init_tab_click = function ($tab, $parent, group)
    {
        $tab.click(function ()
        {
            $parent.find('.group-container:visible').hide();
            $parent.find('.group-container.' + group).show();
            $parent.find('.settings-editor-info-module').show();
        });
    };


    var have = {};

    var $groupTab = $('<div/>');
    $option_block
            .find('[option_target="groups"]:first')
            .append($groupTab);

    $option_block
            .find('[option_group]')
            .each(function ()
            {
                var group = $(this).attr('option_group');

                if (!isset(have, group))
                {
                    var $groupContainer = $('<div />');
                    $groupContainer.css({marginBottom: '40px', textAlign: 'center'});
                    $groupContainer.addClass('group-container');
                    $groupContainer.addClass(group);
                    $option_block.find('.settings-editor-info-module:first').before($groupContainer);
                    $option_block.find('.group-container:first').after($groupContainer);
                    have[group] = group;
                    $groupContainer.hide();

                    var $headTab = $('<span/>');
                    $headTab.addClass('option-span-tab');
                    $headTab.text(group);
                    $headTab.appendTo($groupTab);
                    $headTab.css({marginLeft: '10px'});
                    init_tab_click($headTab, $option_block, group);
                }

                $option_block
                        .find('.group-container.' + group)
                        .append($(this));
            });
};



/**
 * Получение информации из опции <br/> 
 * Функция которая регистрирует, каким образом опция расчитывает свои значения
 * и возвращает результат для записи в базу данных, который потом передается в модуль-родитель
 * этой опции
 * 
 * @param {string} mtype тип модуля
 * @param {function} callback
 * @returns {undefined} callback vars: $module, mtype;  function($module, mtype)
 */
option.addTypeCallback = function (mtype, callback)
{
    this.typeCallbacks[mtype] = callback;
};

/**
 * Установка значения опции <br/>
 * Регистрирует поведение опции, при передаче ей значения.
 * Перебирает значение в обратном порядке, как при его составлении, но наоборот
 * и раздает внутренним элементам соответсвующие кусочки этого значения.
 * 
 * @param {string} mtype
 * @param {function} callback function($option, value) в callback приходит $option, value;
 * @returns {undefined}
 */
option.addTypeCallbackSet = function (mtype, callback)
{
    this.typeCallbacksSet[mtype] = callback;
};

/**
 * Установка значений в опции
 * 
 * @param {$} $option
 * @param {mixed} value
 * @returns {undefined}
 */
option.typeTriggerSet = function ($option, value)
{
    var mtype = $option.attr('mtype');
    if (typeof this.typeCallbacksSet[mtype] === 'function')
    {
        this.typeCallbacksSet[mtype]($option, value);
    }
};

/**
 * 
 * @param {type} $option_block
 * @param {type} with_styles
 * @returns {option.getSettingsData.settings|value}
 */
option.getSettingsData = function ($option_block, with_styles)
{
    with_styles = with_styles ? with_styles : true;

    var settings = {};
    var styles = {};
    $option_block
            .find('.settings-editor-info-module')
            .each(function ()
            {
                var mtype = $(this).attr('mtype');
                var skey = $(this).attr('key');
                var value = option.getValueByType($(this), mtype);

                if (value !== '') // del????? no pls
                {
                    settings[skey] = value;

                    if ($(this).attr('is_style') === '1' && with_styles)
                    {
                        styles[skey] = value;
                    }
                }
            });

    return styles;
};

/**
 * Вернет значение опции
 * 
 * @param {$} $optionJQuery html опции
 * @param {string} mtype тип опции
 * @returns {mixed} value
 */
option.getValueByType = function ($optionJQuery, mtype)
{
    if (typeof option.typeCallbacks[mtype] !== 'undefined')
    {
        return option.typeCallbacks[mtype]($optionJQuery, mtype);
    }

    return 'NONE_TYPE';
};


/**
 * Регистрирует поведение обаботки опции и поиска модуля-цели в управлении,
 * в зависимости от текущего типа закладки, например если это стандартные настройки, 
 * то класс модуля находится в главном окне этой закладки, 
 * а если это закладка "вывод информации" то класс модуля можно получить из
 * take_integrator. // теперь вместо табов, окна
 * 
 * Это позволяет добовлять поведение обработки для новых пользовательских закладок, в которых
 * используются опции, если возникает необходимость динамической обработки этих опций.
 * 
 * @param {type} window_unical_id id таба
 * @param {type} callback функция обработки и формирования информации для события
 * @returns {undefined} в callback($option, $tabInfo);
 */
option.registerEvent = function (window_unical_id, callback)
{
    this.__regEvent[window_unical_id] = callback;
};

/**
 * Функция проверяет изменения динамических свойст, вернее их наличие,
 * для объекта внутри закладки управления,
 * при успешном сопастовлении применяет значение, к соответствующему свойству модуля.
 * 
 * @param {type} className класс модуля
 * @param {type} optionKey ключ опции
 * @param {type} $controllTabTarget модуль-элемент закладки управления
 * @param {type} value значение для модуля
 * @param {jQuery} $option опция html jquery 
 * @returns {undefined}
 */
option.triggerDinamicCallback = function (className, optionKey, $controllTabTarget, value, $option)
{
    if (typeof this.dinamicCallbacks[className] !== 'undefined')
    {
        if (typeof this.dinamicCallbacks[className][optionKey] !== 'undefined')
        {
            this.dinamicCallbacks[className][optionKey]($controllTabTarget, value, $option);
        }
    }
};

/**
 * Вызывает ряд действий связаных с динамическим изменением опции, и ее модуля-хозяина
 * 
 * @param {$} $option опция которая меняет информацию
 * @param {window} window окно
 * @returns {undefined}
 */
option.event = function ($option, window)
{
    var key = window.unical_id;
    if (typeof this.__regEvent[key] === 'function')
    {
        this.__regEvent[key]($option, window);
    }
};

/**
 * 
 * @param {type} className
 * @param {type} callback
 * @returns {undefined}
 */
option.fast_edit = function (className, callback)
{
    if (!isset(option.__fast_edit_callbacks, className))
    {
        option.__fast_edit_callbacks[className] = {};
        option.__fast_edit_callbacks[className]['func'] = callback;
    }
};

option.fast_edit_init_controll_window = function ($window)
{
    // в окне ищем все зарегистрированные классы, вешаем на них обработчик на клик. 
    // в кэлбек передаем $(this) через call + объект с данными типа idTemplate 
    for (var className in option.__fast_edit_callbacks)
    {
        var fclass = className.replace("\n", '\\\\');
        fclass = fclass.replace("\t", '\\\\');
        fclass = fclass.replace(new RegExp('\\\\', 'g'), '\\\\');

        $window
                .find('[fast_edit_class=' + fclass + '][fast_edit=1]')
                .each(function ()
                {
                    if (typeof $(this).data('__init_this_fast_edit') == 'undefined')
                    {
                        $(this).data('__init_this_fast_edit', true);
                        var func = option.__fast_edit_callbacks[className]['func'];
                        if (typeof func === 'function')
                        {
                            func.call($(this), option.get_fast_edit_data($(this)));
                        }
                    }
                })
                ;
    }

    $window
            .find('[idtemplate]:NOT([fast_edit])')
            .click(function ()
            {
                var jQuery = $window.find('[idtemplate=' + $(this).attr('idtemplate') + '][fast_edit=1]:first');
                var data = option.get_fast_edit_data(jQuery);
                mbcms.visual_fast_edit.create(data);

                return false;
            })
            ;
};

option.get_fast_edit_data = function ($jQuery)
{
    var idTemplate, parent_class, parent, pidTemplate, db_table, unical_selector;
    if (typeof $jQuery.attr('idtemplate') !== 'undefined')
    { // template
        idTemplate = $jQuery.attr('idtemplate');
        parent_class = $jQuery.attr('css_class');
        parent = $jQuery.parents('[idtemplate]:first') || $('body');
        pidTemplate = parent.attr('idtemplate');
        db_table = parent.attr('db_table');
        unical_selector = '[idtemplate=' + idTemplate + ']:first';
    }
    else
    { // output
        parent = $jQuery.parents('[idtemplate]:first') || null;
        idTemplate = parent.attr('idtemplate') || null;
        pidTemplate = parent.parents('[idtemplate]:first').attr('idtemplate');
        db_table = parent.parents('[idtemplate]:first').attr('db_table');
        parent_class = parent.attr('css_class');
        var out_index = $jQuery.attr('__cms_output_index');
        unical_selector = '[parentidtemplate=' + idTemplate + '][__cms_output_index=' + out_index + ']:first';
    }

    var current_class = $.trim($jQuery.attr('css_class'));
    current_class = $.trim(current_class.replace('  ', ' '));

    var ret = {
        idTemplate: idTemplate,
        out_index: parseInt($jQuery.attr('__cms_output_index')),
        template_index: parseInt($jQuery.attr('__cms_template_index')),
        primary_key: $jQuery.attr('primary_key'),
        primary_key_value: $jQuery.attr('primary_key_value'),
        db_table: db_table,
        parent_class: parent_class,
        current_class: current_class,
        parent: parent,
        fast_edit_class: $jQuery.attr('fast_edit_class'),
        list: option.__fast_edit_get_list($jQuery.attr('fast_edit_class')),
        this: $jQuery,
        connect_type: $jQuery.attr('connect_type'),
        pidTemplate: pidTemplate,
        unical_selector: unical_selector
    };

    if (isset($jQuery[0], 'attributes'))
    {
        for (var i in $jQuery[0].attributes)
        {
            var key = $jQuery[0].attributes[i].name;
            ret[key] = $jQuery[0].attributes[i].value;
        }
    }

    return ret;
};

// листы для опций, птом они будут заполняться при регистрации по имени класса!

option.__fast_edit_get_list = function (fast_edit_class)
{
    return isset(this.__lists, fast_edit_class) ? this.__lists[fast_edit_class] : [];
};

option.__lists = {};

/**
 * 
 * @param {type} fast_edit_class
 * @param {type} list
 * @returns {undefined}
 */
option.register_fast_edit_list = function (fast_edit_class, list)
{
    this.__lists[fast_edit_class] = this.__lists[fast_edit_class] == undefined ? [] : this.__lists[fast_edit_class];
    $.merge(this.__lists[fast_edit_class], list);
};

(function ()
{
    $.fn.optionValue = function (value)
    {
        if (typeof value === 'undefined')
        {
            return option.getValueByType($(this), $(this).attr('mtype'));
        }
        else
        {
            option.typeTriggerSet($(this), value);
        }
    };

})();


