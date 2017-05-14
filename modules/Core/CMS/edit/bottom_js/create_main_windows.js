(function ()
{
    var window = new mbcms.window('mbcms_main_window');
    window.undestroy = true;
    window.setWidth(100);
    window.setTitle('Основное окно', 'red', 'white');
    window.$.css({textAlign: 'center'});
    window.$.hide();
    window.close();

})();


