/**
 * Created by user on 25.05.2017.
 */

function fast_validator()
{
}

fast_validator.VALIDATAION_TYPE_EMAIL = 1;
fast_validator.VALIDATAION_TYPE_PASSWORD = 2;
fast_validator.__class = 'trud\\fast_validator';

/**
 *
 * @param {jQuery} parent == body
 * @param selector
 * @param {const} validation_type fast_validator.VALIDATAION_TYPE_EMAIL
 */
fast_validator.init = function (parent, selector, validation_type)
{
    parent = parent || $('body');

    var items = parent
            .find(selector)
            .change(function()
            {
                __delay(this, 'pause', 500, function ()
                {
                    fast_validator.validate(validation_type, items);
                });
            })
        ;
};

/**
 *
 * @param validation_type
 * @param {jQuery} items
 */
fast_validator.validate = function (validation_type, items)
{
    switch (validation_type)
    {
        case fast_validator.VALIDATAION_TYPE_EMAIL :
            fast_validator.valid_email(items);
            break;
        case fast_validator.VALIDATAION_TYPE_PASSWORD :
            fast_validator.valid_password(items);
            break;
    }
};

/**
 *
 * @param {jQuery} items
 */
fast_validator.valid_email = function (items)
{
    var data  = {class: this.__class + '->ajax_validate_email'};
    fast_validator.print(items, data);
};

fast_validator.valid_password = function (items)
{
    var data  = {class: this.__class + '->ajax_validate_password'};
    fast_validator.print(items, data);
};

fast_validator.print = function(items, data)
{
    var name;

    items
        .each(function ()
        {
            name =  $(this).attr('name');
            data[name] = $(this).val();
        });

    site.ajax({
        data: data,
        success: function (req)
        {
            req = get_req(req);

            $('.fastvalidator' + name).remove();

            if (req.errors)
            {
                var text = '';

                for (var i in req.errors)
                {
                    var er = isset(req.errors, i, '0') ? (req.errors[i][0] ? req.errors[i][0] : i) : '';
                    text += er ? er + "\n" + '<br>' : '';
                }

                items.parent().append(
                    $('<div />').addClass('fastvalidator' + name)
                        .html(text).css({color: '#b11a0c', background: '#eee', padding: '3px 5px',
                        display: 'inline-block', fontWeight: 'bold', borderRadius: '5px'}));
            }

        }
    });
};