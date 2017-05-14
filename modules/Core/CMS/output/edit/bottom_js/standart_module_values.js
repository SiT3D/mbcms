/* global mbcms */

(function()
{
    option.addTypeCallback('string', function($module)
    {
        return $module.val();
    });
    
    option.addTypeCallback('array', function()
    {
        return 'array()';
    });
    
    option.addTypeCallback('text', function($module)
    {
        return $module.text();
    });
})();


