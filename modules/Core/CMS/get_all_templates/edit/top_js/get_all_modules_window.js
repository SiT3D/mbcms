
mbcms.get_all_modules_window = function () {
};


mbcms.get_all_modules_window.FILTER_TYPE_TEMPLATE = 'TYPE_TEMPLATE';
mbcms.get_all_modules_window.FILTER_TYPE_OUTPUT = 'TYPE_OUTPUT';

/**
 * 
 * @param {constant} type ///mbcms.get_all_modules_window.FILTER_TYPE_TEMPLATE
 * @param {function} callback // callback(info) info = {name: 'class_name'};
 * @returns {undefined}
 */
mbcms.get_all_modules_window.start = function (type, callback)
{

    var window = new mbcms.window('get_all_modules_window');
    window.setWidth(800);
    window.setTitle('Получение шаблонов', 'white', 'black');

    window.setContent(function ()
    {
        $.ajax(
                {
                    url: '/ajax',
                    data: {class: 'MBCMS\\get_all_modules_window', filter_type: type},
                    type: 'GET',
                    success: function (msg)
                    {
                        $(msg).appendTo(window.$);
                        mbcms.get_all_modules_window.__inits(window, callback);
                    }
                });
    });


};

mbcms.get_all_modules_window.__inits = function (window, callback)
{
    window.$
            .find('.ie.folder.cont-fold')
            .css({cursor: 'poitnter'})
            .click(function ()
            {
                $(this)
                        .children()
                        .is(':visible')
                        ?
                        $(this)
                        .children()
                        .hide()
                        :
                        $(this)
                        .children()
                        .show()
                        ;
                return false;
            })
            ;

    window.$
            .find('.module-in-folder')
            .css({cursor: 'poitnter'})
            .click(function ()
            {
                if (confirm('Создать модуль из выбранного элемента?') && typeof callback === 'function')
                {
                    callback
                            .call(window,
                                    {
                                        name: $(this).attr('name')
                                    });
                    window.destroy();
                }

                return false;
            })
            ;
};
