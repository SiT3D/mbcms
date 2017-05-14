/* global mbcms.admin.tab, mbcms */

(function()
{
    mbcms.admin.tab.addListner(mbcms.admin.tab.MBCMS_MAIN_IFRAME_TAB_CREATE, function(e)
    {
        e.tab.click(function()
        {
            var $frame = $('#admin_iframe');
            var $src = $frame.prop('src');
            $frame.prop('src', '#');
            $frame.prop('src', $src);
            var scrollTop = $('#admin_iframe').contents().scrollTop();
            $frame.data('scrollTop', scrollTop);
            
            return false;
        });
        
        var $frame = $('#admin_iframe');
        $frame.load(function()
        {
            var scrollTop = $frame.data('scrollTop');
            $('#admin_iframe').contents().scrollTop(scrollTop);
        });
    });
})();


