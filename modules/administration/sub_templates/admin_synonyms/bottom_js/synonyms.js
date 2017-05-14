function synonyms()
{
}

synonyms.class = 'trud\\admin\\templates\\admin_synonyms';

synonyms.init = function ()
{
    this.__init_finder();
};

synonyms.__init_finder = function ()
{
    $('.syn_finder:first')
        .keyup(function ()
        {
            if (this.__timer != undefined)
            {
                clearTimeout(this.__timer);
            }

            var $this = $(this);

            this.__timer = setTimeout(function ()
            {
                synonyms.__find_word($this.val());
            }, 800);
        })
    ;
};

synonyms.__find_word = function (value)
{
    value = value == undefined ? $('.syn_finder:first').val() : value;

    site.ajax(
        {
            data: {
                class: synonyms.class + '->ajax_find',
                value: value
            },
            success: function (msg)
            {
                var req = get_req(msg);
                synonyms.create_elements(req.values);
                synonyms.add_new();
            }
        });
};

synonyms.create_elements = function (values)
{
    var container = $('.syn_result');

    this.remove_elements();

    var n = 0;

    for (var i in values)
    {
        var line = values[i];

        synonyms.create_element(line).appendTo(container);

        n++;
    }

    $('<span />')
        .text('TOTAL: ' + n)
        .css({
            marginTop: 20,
            display: 'block'
        })
        .appendTo(container)
    ;
};

/**
 *
 * @param {type} line - values[i]
 * @param empty
 * @returns {undefined}
 */
synonyms.create_element = function (line, empty)
{
    empty = empty == undefined ? false : empty;

    var main = $('<tr />')
        .addClass('syn-element')
        .addClass(empty ? 'add-new-syn' : '')
        .attr('id', line.id)
    ;

    var td_title = $('<td />')
        .appendTo(main)
    ;

    var title = $('<input type="text"/>')
        .val(line.synword)
        .appendTo(td_title)
        .css({})
    ;

    var td_search = $('<td />')
        .appendTo(main)
    ;

    var issearch = $('<input type="checkbox"  value="1" />')
        .attr('checked', line.search == 1)
        .appendTo(td_search)
        .css({marginLeft: 20})
    ;

    var td_islike = $('<td />')
        .appendTo(main)
    ;

    var islike = $('<input type="checkbox"  value="1" />')
        .attr('checked', line.islike == 1)
        .appendTo(td_islike)
        .css({marginLeft: 20})
    ;

    synonyms.init_elements(title, issearch, islike);

    return main;
};

synonyms.remove_elements = function ()
{
    $('.syn_result').empty();
};

/**
 *
 * @param {type} jq_title
 * @param {type} jq_issearch
 * @param {type} jq_islike
 * @returns {undefined}
 */
synonyms.init_elements = function (jq_title, jq_issearch, jq_islike)
{
    jq_title.keyup(function ()
    {
        // update del + add new line
        if (!$.trim($(this).val()))
        {
            if (confirm('DELETE???'))
            {
                $(this).parents('tr:first').remove();
                synonyms.db_delete($(this).parents('tr:first').prop('id'));
                synonyms.add_new();
            }
            else
            {
                synonyms.__find_word();
            }
        }
        else
        {

            if (this.__timer != undefined)
            {
                clearTimeout(this.__timer);
            }

            var $this = $(this);

            this.__timer = setTimeout(function ()
            {
                var id = $this.parents('tr:first').attr('id') || '';
                synonyms.save_title(id, $this.val(), $this.parents('tr:first'), $('.syn_finder').val());
                $this.parents('tr:first').removeClass('add-new-syn');
            }, 1500);
        }

        synonyms.add_new();
    });

    jq_issearch.click(function ()
    {
        var value = $(this).prop('checked') ? 1 : 0;
        synonyms.save_search(value, $(this).parents('tr:first').prop('id'));
    });

    jq_islike.click(function ()
    {
        var value = $(this).prop('checked') ? 1 : 0;
        synonyms.save_like(value, $(this).parents('tr:first').prop('id'));
    });
};

/**
 *
 * @param {type} id
 * @returns {undefined}
 */
synonyms.db_delete = function (id)
{
    site.ajax(
        {
            data: {
                class: synonyms.class + '->ajax_delete',
                id: id
            }
        });
};

synonyms.add_new = function ()
{
    if ($('.add-new-syn').length == 0)
    {
        var new_element = synonyms.create_element([], true);
        $('.syn_result').prepend(new_element);
    }
};

/**
 *
 * @param {type} id
 * @param {type} synword
 * @param {type} jq_element_tr
 * @param {type} keyword
 * @returns {undefined}
 */
synonyms.save_title = function (id, synword, jq_element_tr, keyword)
{
    site.ajax(
        {
            data: {
                class: synonyms.class + '->ajax_set',
                value: synword,
                id: id,
                keyword: keyword
            },
            success: function (msg)
            {
                var req = get_req(msg);
                jq_element_tr.prop('id', req.id);
                synonyms.__find_word();
            }
        });
};

synonyms.save_search = function (val, id)
{
    site.ajax(
        {
            data: {
                class: synonyms.class + '->ajax_ss',
                value: val,
                id: id
            }
        });
};

synonyms.save_like = function (val, id)
{
    site.ajax(
        {
            data: {
                class: synonyms.class + '->ajax_sl',
                value: val,
                id: id
            }
        });
};

new event.site.load().listen(function ()
{
    synonyms.init();
});


