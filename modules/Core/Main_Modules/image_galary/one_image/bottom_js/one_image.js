/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

site.image_galary.one_image = function () 
{
    
};

/**
 * 
 * @param {$} tag
 * @returns {undefined}
 */
site.image_galary.one_image.init_tag = function (tag)
{
    tag
            .click(function ()
            {
                site.image_galary.add_filter($(this).text());
            })
            .dblclick(function ()
            {
                var tag_id = $(this).attr('tag_id') || null;

                if (tag_id)
                {
                    site.image_galary.remove_tag(tag_id);
                    $(this).remove();
                }
            });
};


new event.site.load().listen(function ()
{
    $('[image_id]')
            .click(function ()
            {
                var $this = $(this);

                site.ajax({
                    data: {class: 'MBCMS\\image_galary->ajax_remove_image', id: $(this).attr('image_id')},
                    success: function ()
                    {
                        $this.parents('.galary-one-image:first').remove();
                    }
                });
            });

    site.image_galary.one_image.init_tag($('.tag-name'));

    $('.add_new_tag')
            .click(function ()
            {
                $('.add_new_tag_input').remove();
                var btn = $(this);

                var input = $('<input />')
                        .addClass('add_new_tag_input')
                        .blur(function ()
                        {
                            if ($.trim($(this).val()))
                            {
                                var text = $(this).val();
                                $(this).remove();
                                site.image_galary.add_tag_to_image(btn.attr('add_image_id'), text, btn);
                            }
                        })
                        .keyup(function (e)
                        {
                            if (e.keyCode == 13)
                            {
                                $(this).blur();
                            }

                            if (!$(this).val() && e.keyCode != KEY_BACKSPACE)
                            {
                                $(this).blur();
                            }
                        })
                        ;

                $(this).after(input);

                input.focus();

                return false;
            })
            ;
});