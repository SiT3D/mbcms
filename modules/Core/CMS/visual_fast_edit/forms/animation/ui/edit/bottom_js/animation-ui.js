/**
 * Created by sit3d on 22.04.2017.
 */

function animationclass()
{
}

animationclass.new_css_class = null;

new event.site.load().listen(function ()
{


    new event.visual_fast_edit.init().listen(function ()
    {

        var self = this;

        if ((animationclass.new_css_class || false) && mbcms.visual_fast_edit.get_current_data().out_index == animationclass.__out_index)
        {
            self.jq_container.find('[type=hidden][name=current_class]').attr('value', animationclass.new_css_class).val(animationclass.new_css_class);
        }

        function init_pick_keyframe(kf, keyframe_css_class)
        {
            kf
                .attr('css_class', keyframe_css_class)
                .click(function ()
                {
                    var trg = mbcms.visual_fast_edit.get_targets(undefined, true);
                    var new_css_class = $(this).attr('css_class');
                    trg.removeClass(trg.attr('css_class'));
                    trg
                        .addClass(new_css_class)
                        .attr('css_class', new_css_class);

                    trg.attr('style', '');

                    self.jq_container.find('[type=hidden][name=current_class]').attr('value', new_css_class).val(new_css_class);
                    animationclass.new_css_class = new_css_class;
                    animationclass.__out_index = mbcms.visual_fast_edit.get_current_data().out_index;
                    mbcms.visual_fast_edit.get_current_data().current_class = new_css_class;
                    mbcms.visual_fast_edit.get_current_data().css_class = new_css_class;
                    var idtemplate = mbcms.visual_fast_edit.get_current_data().idTemplate || null;
                    mbcms.dinamic_js_css_loader.load('', idtemplate);


                    return false;
                });
        }

        function create_keyframes()
        {
            var idtemplate = mbcms.visual_fast_edit.get_current_data().idTemplate || null;
            var animation_name = self.jq_container.find('[__ov=animation_name]').val();

            site.ajax({
                data: {
                    class: 'MBCMS\\Forms\\animation->get_keyframes',
                    idTemplate: idtemplate,
                    animation_name: animation_name,
                },
                success: function (msg)
                {
                    var req = get_req(msg);

                    for (var i in req.result)
                    {
                        var classname = req.result[i];
                        var kf = $('<button />').appendTo(animator_panel).addClass('keyframe').text(animator_panel.find('.keyframe').length);
                        init_pick_keyframe(kf, classname);
                    }

                }
            });
        }


        var animator_panel = this.jq_container.find('.animator-ui-panel:first');
        var animation_name = self.jq_container.find('[__ov=animation_name]').val();

        if (animation_name)
        {
            create_keyframes();
        }

        animator_panel.find('.add_key')
            .click(function ()
            {

                var idtemplate = mbcms.visual_fast_edit.get_current_data().idTemplate || null;
                var css_class;

                if (animator_panel.find('.keyframe:last').length)
                {
                    css_class = animator_panel.find('.keyframe:last').attr('css_class');
                }
                else
                {
                    css_class = mbcms.visual_fast_edit.get_current_data().css_class || null;
                }

                var animation_name = self.jq_container.find('[__ov=animation_name]').val();

                if (!idtemplate || !animation_name)
                {
                    alert('animation_name!!!');
                    return false;
                }


                site.ajax({
                    data: {
                        class: 'MBCMS\\Forms\\animation->clone_class',
                        idTemplate: idtemplate,
                        css_class: css_class,
                        animation_name: animation_name,
                    },
                    success: function (msg)
                    {
                        var req = get_req(msg);
                        var kf = $('<button />').appendTo(animator_panel).addClass('keyframe').text(animator_panel.find('.keyframe').length);
                        init_pick_keyframe(kf, req.newname);
                        mbcms.dinamic_js_css_loader.load('', idtemplate);
                    }
                });

                return false;
            })
        ;


        animator_panel.find('.generate_animation')
            .click(function()
            {
                var css_classses = [];

                mbcms.visual_fast_edit.saver.submit(animator_panel.parents('form:first').parent());

                setTimeout(function()
                {

                    animator_panel.find('.keyframe')
                        .each(function ()
                        {
                            if ($(this).attr('css_class') || false)
                            {
                                css_classses.push($(this).attr('css_class'));
                            }
                        });

                    if (css_classses.length)
                    {
                        site.ajax(
                            {
                                data: {
                                    class: 'MBCMS\\Forms\\animation->generate_animation',
                                    idTemplate: mbcms.visual_fast_edit.get_current_data().idTemplate,
                                    out_index: mbcms.visual_fast_edit.get_current_data().out_index,
                                },
                                success: function ()
                                {
                                    var idtemplate = mbcms.visual_fast_edit.get_current_data().idTemplate || null;
                                    mbcms.dinamic_js_css_loader.load('', idtemplate);
                                }
                            }
                        );

                    }

                }, 1500);

                return false;
            });


        $('<button />')
            .text('Просмотреть анимацию')
            .click(function()
            {
                var trg = mbcms.visual_fast_edit.get_targets(undefined, true);
                var animation_name = self.jq_container.find('[__ov=animation_name]').val();
                trg.toggleClass(mbcms.visual_fast_edit.get_current_data().idTemplate + '_' + animation_name);
                return false;
            })
            .appendTo(animator_panel)
        ;

    });
});

