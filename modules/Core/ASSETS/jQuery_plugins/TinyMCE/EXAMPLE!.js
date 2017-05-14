(function ()
{
    return;

    tinymce.init({
        theme: "modern",
        selector: '.walk-option-clone textarea[tinymce]', // вешать на те которые TinyMCE true

        // teest part!!
        plugins: ["save", 'visualblocks', 'code', 'charmap', 'link', 'lists', 'textcolor', 'textpattern', 'fullscreen', 'hr', 'colorpicker'
                    , 'image'],
        toolbar: ['save', "styleselect,formatselect,fontselect,fontsizeselect", 'fullscreen', 'forecolor,backcolor', 'justifyleft,justifycenter,justifyright,justifyfull'],
        image_list: [
//            {title: 'My image 2', value: 'http://www.moxiecode.com/my2.gif'} // задавать динамически
        ],
        //not del!!!
        save_enablewhendirty: true,
        save_onsavecallback: function (e) {
            $option.optionValue(e.getContent());
            option.event($option, mbcms.classes.Wrappers__option.tabInfo);
        }
    });
})();


