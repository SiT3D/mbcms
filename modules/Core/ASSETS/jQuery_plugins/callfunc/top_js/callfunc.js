
Plugins.callfunc = function () {
};

/**
 * 
 * @param {type} actionFuncName
 * @returns {Boolean}
 */
Plugins.callfunc.call = function (actionFuncName, apply_object)
{

    if (typeof actionFuncName === 'undefined' || actionFuncName === '')
    {
        return false;
    }
    
    var args = [];
    for (var i in arguments)
    {
        args.push(arguments[i]);
    }
    args.splice(0,2);

    if (typeof actionFuncName == 'function')
        actionFuncName.apply(apply_object, args[0]);

    var array = actionFuncName.split(/\./);

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
        currentFunction.apply(apply_object, args[0]);
};

Plugins.callfunc.get = function (actionFuncName)
{
    if (typeof actionFuncName === 'undefined' || actionFuncName === '')
    {
        return undefined;
    }
    
    if (typeof actionFuncName == 'function')
        return actionFuncName;
    
    var array = actionFuncName.split(/\./);

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
        return currentFunction;
};