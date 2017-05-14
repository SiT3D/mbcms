/* global mbcms */

(function()
{
    mbcms.admin.tab.addListner('MBCMS_ADD_OUTPUTS_MODULES', function(info)
    {
        info.module.find('#minmaxbtn').click(function()
        {
            var $compacts = $('.MBCMS_MY_MODULE_TAKE:NOT(.compact)');
            if ($compacts.length > 0)
            {
                $compacts.find('.arrow:first').click();
            }
            else
            {
                $('.MBCMS_MY_MODULE_TAKE.compact').find('.arrow:first').click();
            }
            return false;
        });
    });
})();

