/**
 * Created by user on 03.05.2017.
 */

new event.site.load().listen(function()
{
    new event.site.form.success().listen(function()
    {
        if (this.form_id == 'vrabote_parser')
        {
            if (this.is_errors())
            {
                this.print_errors();
            }
            else
            {
                var vr = new vrabote();
                vr.get_all_categories(this.__req.cat, this.__req.alias);
            }
        }
    });
});