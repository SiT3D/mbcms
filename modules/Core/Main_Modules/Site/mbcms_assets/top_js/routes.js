event.routes_link = function ()
{
    this.__key = 'event.routes_link'; // уникальный идентификатор, прсто название класса!
    this.req = {};
};
event.routes_link.prototype = Object.create(event.prototype);

function routes()
{}

/**
 * 
 * @param {string} rout_name
 * @param {array} params 
 * @returns {undefined} - event routes_link.call
 */
routes.link = function (rout_name, params)
{
    $.ajax({
        url: '/ajax',
        data: {class: 'MBCMS\\routes\\js_routes->link', name: rout_name, params: params},
        success: function (req)
        {
            var evt = new event.routes_link().call();
            evt.req = $.parseJSON(req);
        }
    });
};