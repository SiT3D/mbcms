/* global mbcms */

mbcms.classes.Admin_Standart__module_take = function () {
};

mbcms.classes.Admin_Standart__module_take.$currentMove = null;

mbcms.classes.Admin_Standart__module_take.initDragRow = function ($move)
{
    $move = $move.find('#move_module_take_preview');
    var self = this;
    $(this).data('mousedown', false);

    $move.mousedown(function ()
    {
        $(this).data('mousedown', true);
        return false;
    }).mouseleave(function ()
    {
        if ($(this).data('mousedown'))
        {
            self.start_drag($(this));
        }
    }).mouseup(function (e)
    {
        self.end_drag(e);
    });
};

mbcms.classes.Admin_Standart__module_take.end_drag = function (e)
{
    if (this.$currentMove !== null)
    {
        this.$currentMove.data('mousedown', false);
        this.$currentMove.data('realUL').animate({opacity: 1});
        this.$currentMove = null;
    }
};

mbcms.classes.Admin_Standart__module_take.move_drag = function (e)
{
    if (this.$currentMove !== null)
    {
        this.moveResort(e);
    }
};

mbcms.classes.Admin_Standart__module_take.start_drag = function ($move)
{
    this.$currentMove = $move;
    var $ulBox = $move.parents('.MBCMS_MY_MODULE_TAKE:first');
    $ulBox.animate({opacity: 0.5}, 'slow');
    this.$currentMove.data('realUL', $ulBox);

    var id = 0;
    $move.parents('.mbcms-standart-take-editor').find('.MBCMS_MY_MODULE_TAKE').each(function ()
    {
        $(this).attr('drag_id', id);
        id++;
    });

};

mbcms.classes.Admin_Standart__module_take.moveResort = function (e)
{
    this.oldY = typeof this.oldY === 'undefined' ? e.clientY : this.oldY;
    var deltaY = e.clientY - this.oldY;
    this.oldY = e.clientY;

    var $trg = $(e.target);
    if ($trg.attr('drag_id') !== this.$currentMove.data('realUL').attr('drag_id') && $trg.hasClass('MBCMS_MY_MODULE_TAKE'))
    {
        if (deltaY > 0) // down
        {
            $trg.after(this.$currentMove.data('realUL'));
        }
        else // top
        {
            $trg.before(this.$currentMove.data('realUL'));
        }
    }
};

(function ()
{
    var self = mbcms.classes.Admin_Standart__module_take;
    $(document).mouseup(function (e)
    {
        self.end_drag(e);
    }).mousemove(function (e)
    {
        self.move_drag(e);
    });
})();