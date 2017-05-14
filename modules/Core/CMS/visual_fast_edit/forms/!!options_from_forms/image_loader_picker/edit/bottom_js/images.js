


(function ()
{
    new event.visual_fast_edit.init().listen(function ()
    {
        this.jq_container
                .find('#images_form_picker_btn')
                .click(function ()
                {

                    mbcms.ajax({
                        data:
                                {
                                    class: 'MBCMS\\Forms\\OPT\\image_loader_picker->get_files_ajax'
                                },
                        success: function (msg)
                        {
                            mbcms.images_galary.load_form(msg);
                        }
                    });

                    return false;
                })
                ;


        this.jq_container
                .find('#upload_images_mbcms')
                .click(function ()
                {
                    $(this).parents('form').attr('action', '/ajax').submit();

                    return false;
                })
                ;

    });


    new event.images_galary.pick_image().listen(function ()
    {
        $('#images_form_picker_hidden').attr('value', this.src);
    });

})();