mbcms.tables = function ()
{
    this.__form = null;
    this.__table_picker = null;
    this.__rows = [];
    this.__opt_container = null;

    this.__init = function (form)
    {
        this.__form = form;
        this.__table_picker = this.__form.find('.tables_picker');
        this.__create_opt_container();

        this.__init_pick_table();

        this.__load_rows();
    };

    this.__create_opt_container = function ()
    {
        this.__opt_container = $('<div/>')
                .appendTo(this.__form)
                .css({minHeight: 400, minWidth: 400})
                ;
    };

    this.__init_pick_table = function ()
    {
        var self = this;

        new event.visual_fast_edit.main_option.go().listen(function ()
        {
            __delay(self, '__timer', 1000, function ()
            {
                self.__load_rows();
            });
        });

    };

    this.__get_current_table = function ()
    {
        return this.__table_picker.val();
    };

    this.__create_rows = function (rows)
    {
        this.__destroy_rows();

        for (var i in rows.Field)
        {
            var name = rows.Field[i];
            this.__create_visual_row(name);
        }
    };

    this.__destroy_rows = function ()
    {
        for (var i in this.__rows)
        {
            var vrow = this.__rows[i];
            if (isset(vrow, 'remove'))
            {
                vrow.remove();
            }
        }

        this.__rows = [];
    };

    this.__create_visual_row = function (field_name)
    {
        this.__rows.push(
                $('<div/>')
                .css({color: '#fff', fontWeight: 'bold', background: '#444'})
                .text(field_name)
                .appendTo(this.__opt_container)
                );
    };

    this.__load_rows = function ()
    {
        var table = this.__get_current_table();
        var self = this;

        mbcms.ajax(
                {
                    data:
                            {
                                class: 'MBCMS\\Forms\\DBV\\tables->ajax_get_rows',
                                table: table,
                            },
                    success: function (msg)
                    {
                        var req = get_req(msg);

                        if (isset(req, 'rows'))
                        {
                            var rows = req.rows;
                            self.__create_rows(rows);
                        }
                    }
                });
    };
};

mbcms.tables.init = function (form)
{
    var $this = new mbcms.tables();
    $this.__init(form);
};


(function ()
{
    new event.visual_fast_edit.init().listen(function ()
    {
        if (this.jq_container.hasClass('tables_form_'))
        {
            mbcms.tables.init(this.jq_container);
        }
    });

})();


