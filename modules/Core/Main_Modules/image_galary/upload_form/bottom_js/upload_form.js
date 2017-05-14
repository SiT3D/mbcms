/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

new event.site.load().listen(function()
{
    new event.site.form.success().listen(function()
    {
        if (this.form_id == 'standart_image_uplouder')
        {
            if (this.is_errors())
            {
                this.print_errors();
            }
            else
            {
                location.reload();
            }
        }
    });
});
