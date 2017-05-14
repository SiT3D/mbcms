(function () // находит класс и метод если они есть и вызывает, для кнопки
{
    $('.action-btn').click(function ()
    {
        var actionFunc = $(this).attr('action');
        
        if (typeof actionFunc === 'undefined' || actionFunc === '')
        {
            return false;
        }
        
        var array = actionFunc.split(/\./);

        function recursiveWindow(targetObject, key)
        {
            if (typeof targetObject[key] !== 'undefined')
            {
                return targetObject[key];
            }
            
            return targetObject;
        }
        
        currentFunction = window;
        for (var i in array)
        {
            var key = array[i];
            var currentFunction = recursiveWindow(currentFunction, key);
        }

        if (typeof currentFunction === 'function')
        {
            currentFunction($(this));
        }
    });
})();

