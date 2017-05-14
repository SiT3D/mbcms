

event.site = function ()
{
    this.__key = 'event.site'; // уникальный идентификатор, прсто название класса!
};
event.site.prototype = Object.create(event.prototype);

event.site.load = function ()
{
    this.__key = 'event.site.load'; // уникальный идентификатор, прсто название класса!
};
event.site.load.prototype = Object.create(event.prototype);

(function()
{
    $(document).ready(function()
    {
        new event.site.load().call();
    });
})();