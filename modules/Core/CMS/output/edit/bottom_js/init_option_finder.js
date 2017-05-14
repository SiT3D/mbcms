/* global mbcms */

(function()
{
    mbcms.admin.tab.addListner('MBCMS_ADD_OUTPUTS_MODULES', function(info)
    {
        info.module.find('.options-finder').each(function()
        {
            var $module_take = $(this).parents('.MBCMS_MY_MODULE_TAKE:first');
            mbcms.classes.Options_Plugin__option_finder.init($(this), $module_take, info.module);
        });
    });
})();

