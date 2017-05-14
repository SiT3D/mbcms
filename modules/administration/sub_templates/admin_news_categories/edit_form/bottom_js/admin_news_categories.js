/**
 * Created by user on 16.03.2017.
 */


new event.site.load().listen(function()
{
    new event.site.form.success().listen(function()
    {
        if (this.form_id == 'admin_news_categories_form' || this.form_id == 'admin_news_categories_remove_form')
        {
            if (this.action_method == 'edit')
            {
                alert('Сохранено');
            }
            else
            {
                this.redirect();
            }
        }
    });
});