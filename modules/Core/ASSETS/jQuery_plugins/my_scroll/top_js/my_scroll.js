

Plugins.my_scroll = function () {
};


/**
 * 
 * @param {type} trg
 * @returns {Number} %
 */
Plugins.my_scroll.get_pers = function (trg)
{
    trg = typeof trg == 'undefined' ? $(window) : trg;
    var progress = trg.scrollTop() / ($(document).height() - trg.height());
    return Math.round(progress * 100);
};