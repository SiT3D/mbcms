new event.site.load().listen(function ()
{

    $('.filter-form-clear')
        .click(function ()
        {
            var form = $(this)
                .parents('.mbcms-form');

            form
                .find('*:not([type=submit])')
                .val('')
            ;

            form
                .find('select')
                .val([])
                .trigger('chosen:updated')
            ;
        });
});