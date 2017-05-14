<?php

namespace MBCMS\Forms;

class images extends main_form
{

    public function __construct($parent = null)
    {
        parent::__construct(self::FORM_TYPE_SETTINGS);

        if ($parent)
        {
            $parent->fast_edit_reg_option('ico background KEY_I', $this, 'view');
        }

        $this->form_method = 'POST';
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new OPT\image_loader_picker,
        ];
    }

    public function init()
    {
        parent::init();

        $opt = new OPT\title('Загрузить изображения на сервер');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\image_loader_picker(OPT\image_loader_picker::TYPE_LOAD);
        $this->ADDM($opt, 'modules');


        $opt = new OPT\title('Выбрать изображение как картинку к img');
        $this->ADDM($opt, 'modules');

        $opt = new OPT\image_loader_picker(OPT\image_loader_picker::TYPE_PICK);
        $this->ADDM($opt, 'modules');
    }

}
