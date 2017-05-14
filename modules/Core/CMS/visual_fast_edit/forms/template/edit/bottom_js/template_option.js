/**
 * Created by user on 03.03.2017.
 */


template_option = function ()
{
};

event.template_option_pages = function ()
{
    this.__key = 'event.template_option_pages'; // уникальный идентификатор, прсто название класса!
    this.idTemplates = {};
};
event.template_option_pages.prototype = Object.create(event.prototype);

template_option.get_all_pages = function ()
{
    mbcms.ajax({
        data: {class: 'MBCMS\\Forms\\template->get_pages'},
        success: function (msg)
        {
            var req = get_req(msg);
            var templates = [];

            for (var i in req.pages)
            {
                var item = req.pages[i];
                templates.push(item.idTemplate);
            }

            var evt = new event.template_option_pages().call();
            evt.idTemplates = templates;
        }
    });
};


template_option.go = function (array)
{
    site.messages.factory('length_array_autogenerateee').create().append_content(array.length).locked(true);

    if (array.length <= 0)
    {
        site.messages.factory('length_array_autogenerateee').create().append_content(array.length).locked(false).remove();
    }

    if (array.length == 0)
    {
        return;
    }

    var id = array.pop();

    if (id)
    {
        mbcms.template.autogenerate_static(id, function ()
        {
            setTimeout(function()
            {
                template_option.go(array);
            }, 50);
        });
    }

};

new event.template_option_pages().listen(function ()
{
    template_option.go(this.idTemplates);
});


new event.visual_fast_edit.init().listen(function ()
{
    this.jq_container
        .find('.generate_all_templates')
        .click(function ()
        {
            template_option.get_all_pages();
            return false;
        });
});