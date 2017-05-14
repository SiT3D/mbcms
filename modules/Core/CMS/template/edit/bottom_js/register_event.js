/* global mbcms */

(function ()
{
    option.registerEvent('mbcms_templates_settings', function ($option, window)
    {
        var key = $option.attr('key');
        var className = $option.parents('#MBCMS_STANDART_SETTINGS_EDITOR_MODULE').attr('class_name');
        var tidTemplate = window.$.find('[idtemplate]:first').attr('idtemplate');
        var $ttargetOutputs = mbcms.controll_window.get().find('[idtemplate="' + tidTemplate + '"]');
        var value = option.getValueByType($option, $option.attr('mtype'));
        $ttargetOutputs.each(function ()
        {
            //отправляем новые значения в шаблоны
            $currentTarget = $(this);
            var $optionAttrClass = window.$.find('[key=class][option_group=CSS]:first'); 
            var needClass = $optionAttrClass.optionValue();

            var selector = '';
            for (var i in needClass)
            {
                selector += ' ' + needClass[i]['name'];
            }
            
            var pregSelector = $.trim(selector);
            pregSelector = pregSelector.replace(/\s/gi,'\\s');
            var preg = new RegExp(pregSelector, 'i');
            var pregResult = preg.test($(this).prop('class'));
            
            selector = selector.replace(/\s/gi,'.');
            
            var $childs = $(this).find(selector);
            var $currentTarget = $childs.length > 0 && !pregResult ? $childs : $currentTarget;
            
            if ($childs.length === 0)
            {
                var $sselect = $optionAttrClass.find('select');
                $sselect.attr('current', $sselect.val());
            }
            
            if ($option.attr('key') === 'class' && $childs.length !== 0)
            {
                
            }
            else
            {
                option.triggerDinamicCallback(className, key, $currentTarget, value, $option);
            }
        });
    });
})();


