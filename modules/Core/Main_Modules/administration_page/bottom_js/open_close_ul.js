open_close_ul = function ()
{

};


open_close_ul.action = function (item, speed)
{
    speed = speed || 'normal';

    var parentLi = item.parents('li:first');
    var nextLi = parentLi.next('li');

    if (nextLi.find('ul.dashboard-menu').length > 0)
    {
        if (nextLi.is(':visible'))
        {
            open_close_ul.close(nextLi);
        }
        else
        {
            open_close_ul.close_all();
            open_close_ul.open(nextLi);
        }
    }
};

open_close_ul.open = function (nextLi)
{
    nextLi.css({display: ''});
};

open_close_ul.close = function (nextLi)
{
    nextLi.css({display: 'none'});
};

open_close_ul.init = function ()
{
    $('.nav-header')
        .click(function ()
        {
            open_close_ul.action($(this));
        })
    ;
};

open_close_ul.close_all = function ()
{
    $('ul.dashboard-menu')
        .each(function ()
        {
            open_close_ul.close($(this).parent());
        })
    ;
};

new event.site.load().listen(function ()
{
    open_close_ul.init();
});
