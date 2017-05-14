/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

site.image_galary = function ()
{

};

site.image_galary.add_filter = function (tag_name)
{

    $('#select_filter_galary')
            .find('[value=' + tag_name + ']')
            .each(function ()
            {
                $(this).prop('selected', !$(this).prop('selected'));
            })
            ;


    $('#select_filter_galary').trigger('chosen:updated');
};

/**
 * 
 * @param {type} image_id
 * @param {type} tag_value
 * @param {$} after_trg
 * @returns {undefined}
 */
site.image_galary.add_tag_to_image = function (image_id, tag_value, after_trg)
{
    // тут аякс
    site.ajax({
        data: {class: 'MBCMS\\image_galary->ajax_add_tag', image_id: image_id, tag_value: tag_value},
        success: function (req)
        {
            req = $.parseJSON(req);
            req.tag_id = req.tag_id || null;
            if (req.tag_id)
            {
                var inp = $('<span />')
                        .addClass('tag-name')
                        .attr({tag_id: req.tag_id})
                        .text(tag_value)
                        ;

                site.image_galary.one_image.init_tag(inp);

                after_trg.after(inp);
            }
        }
    });
};

/**
 * 
 * @param {type} tag_id
 * @returns {undefined}
 */
site.image_galary.remove_tag = function (tag_id)
{
    site.ajax({
        data: {class: 'MBCMS\\image_galary->ajax_remove_tag', tag_id: tag_id},
        success: function ()
        {

        }
    });
};