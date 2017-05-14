/* global mbcms */


mbcms.dinamic_js_css_loader = function ()
{
};

/**
 * 
 * @param {string} class_name or
 * @param {int} idTemplate or
 * @param {bool} only_new загрузит только новые css STANDART: false
 * @param {function} callback выполняется после того как все файлы будут обновлены, сss и js //function(req)
 * @returns {undefined}
 */
mbcms.dinamic_js_css_loader.load = function (class_name, idTemplate, only_new, callback)
{
    // расширить, вторым параметром не передавать idTemplate его можно и в первом проверять
    // вторым параметром идет вопрос, загружать ли всегда, или если есть уже в head такой css то не загружать уже
    only_new = only_new == undefined ? false : only_new;

    var $data = {connect_class_name: class_name, connect_idTemplate: idTemplate, class: 'MBCMS\\dinamic_js_css_loader->ajax'};
    mbcms.ajax(
            {
                data: $data,
                success: function (msg)
                {
                    if (msg !== '')
                    {
                        var newPaths = {};
                        var req = get_req(msg);

                        for (var i in req['css'])
                        {
                            if (only_new)
                            {
                                for (var path in newPaths)
                                {
                                    $('head').append('<link href="' + path + '?t='
                                            + Math.round(new Date() / 1000) + '" type="text/css" rel="stylesheet">');
                                }
                            }
                            else
                            {
                                var path = req['css'][i]['metapath'];
                                $('head').append('<link href="' + path + '?t=' + Math.round(new Date() / 1000) + '" type="text/css" rel="stylesheet">');
                            }

                            var __current_css = req['css'][i]['name'];
                            var __current_css_template = req['css'][i]['template'];

                            var selector = 'link[href*="' + __current_css + '"][href*="' + __current_css_template + '"]';

                            $('head')
                                    .find(selector + ':not(:last)')
                                    .each(function ()
                                    {
                                        var $this = $(this);
                                        var src = $this.attr('href') || $this.attr('src');

                                        src = src.replace(/\\/g, '/');

                                        __current_css = __current_css.replace(/\-/g, '\\-');
                                        __current_css = __current_css.replace(/\./g, '\\.');

                                        if (new RegExp('\/' + __current_css).test(src))
                                        {
                                            setTimeout(function ()
                                            {
                                                $this.remove();
                                            }, 300);
                                        }
                                    });


                        }

                        if (typeof callback === 'function')
                        {
                            callback(req);
                        }
                    }
                }
            });
};


(function ()
{
    var get_view = new event.template.get_view().listen(function ()
    {
//        mbcms.visual_fast_edit.activate_blocks_gradient(true);
    });

})();