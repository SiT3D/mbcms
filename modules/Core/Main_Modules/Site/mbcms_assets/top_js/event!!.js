


/**
 * 
 создание события 
 
 event.test_evt = function ()
 {
 this.__key = 'event.test_evt'; // уникальный идентификатор, прсто название класса!
 this.is_bottom_call = false; // декларируем параметры, для четкой связи
 };
 event.test_evt.prototype = Object.create(event.prototype);
 
 использование событий
 
 var evt = new event.test_evt(); // экземпляр события
 
 var evt = new event.test_evt().call(); 
 evt.is_bottom_call = false; // установка значений для события 
 
 new event.test_evt().listen(function () // слушаем событие
 {
 console.log(this); // событие в this + переданные свойства там!
 });
 
 
 * @returns {event}
 */
function event()
{}

event.__listners = {};
event.prototype.__key = null;

/**
 * 
 * @param {type} async
 * @returns {event.prototype}
 */
event.prototype.call = function (async)
{
    var self = this;
    async = async !== false;

    var _call = function ()
    {
        var key = self.__key;

        event.__listners[key] = event.__listners[key] || [];

        for (var i in event.__listners[key])
        {
            var callback = event.__listners[key][i];
            if (typeof callback == 'function')
            {
                callback.call(self);
            }
        }
    };

    if (async === true)
    {
        setTimeout(function ()
        {
            _call();
        }, 0);
    }
    else if (async === false)
    {
        _call();
    }

    return this;

};

/**
 *
 * @param {function} callback
 * @returns {event}
 */
event.prototype.listen = function (callback)
{
    event.__listners[this.__key] = event.__listners[this.__key] || [];
    event.__listners[this.__key].push(callback);
    return this;
};

event.prototype.unlisten = function (callback)
{
    // lalala поиск в листнерах + их удаление оттуда слайсом
};
