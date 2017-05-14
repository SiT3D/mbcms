(function()
{
    mbcms.admin.tab.addListner('MBCMS_ADD_OUTPUTS_MODULES', function(info)
    {
        mbcms.admin.adminTitle.all_templates_hover_init.init(info.module);
    });
})();


