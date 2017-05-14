
event.images_galary = function ()
{
    this.__key = 'event.images_galary'; // уникальный идентификатор, прсто название класса!
};
event.images_galary.prototype = Object.create(event.prototype);

event.images_galary.pick_image = function ()
{
    this.__key = 'event.images_galary.pick_image'; // уникальный идентификатор, прсто название класса!

    /**
     * Путь к выбранному изображению
     */
    this.src = '';
};
event.images_galary.pick_image.prototype = Object.create(event.prototype);


mbcms.images_galary = function ()
{

};


mbcms.images_galary.load_form = function (galary_html)
{
    this.remove();
    this.__galary = $(galary_html);

    this.__galary
            .click(function ()
            {
                this.remove();
            });

    this.__galary
            .find('img')
            .click(function ()
            {
                var evt = new event.images_galary.pick_image().call();
                evt.src = $(this).attr('name');

            })
            ;

    this.__galary.appendTo('body');
};

mbcms.images_galary.remove = function ()
{
    if (isset(this.__galary, 'remove'))
    {
        this.__galary.remove();
    }
};