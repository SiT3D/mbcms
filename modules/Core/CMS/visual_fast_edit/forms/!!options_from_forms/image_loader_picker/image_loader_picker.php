<?php

namespace MBCMS\Forms\OPT;

use MBCMS\Forms\OPT\image_loader_picker as me;

class image_loader_picker extends \Module implements \adminAjax
{

    const TYPE_LOAD = 1;
    const TYPE_PICK = 12;

    private $__folder = '';

    const FOLDER = 'images';

    public function __construct($type = null)
    {
        parent::__construct();

        $this->__type   = $type;
        $this->__folder = HOME_PATH . self::FOLDER;
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new me\galary(),
        ];
    }

    public function save_file_ajax()
    {
        if (isset($_FILES['files']['tmp_name']))
        {
            if (is_array($_FILES['files']['tmp_name']))
            {
                $tmps  = $_FILES['files']['tmp_name'];
                $names = $_FILES['files']['name'];
                for ($i = 0; $i < count($tmps); $i++)
                {
                    move_uploaded_file($tmps[$i], $this->__folder . DIRECTORY_SEPARATOR . $names[$i]); // random name
                }
            }
            else
            {
                $tmp  = $_FILES['files']['tmp_name'];
                $name = $_FILES['files']['name'];
                move_uploaded_file($tmp, $this->__folder . DIRECTORY_SEPARATOR . $name); // random name
            }
        }

        \MBCMS\routes::redirect($_SERVER['HTTP_REFERER']);
    }

    public function get_files_ajax()
    {
        $galary         = new me\galary();
        $galary->images = [];

        // нужна кнопка которая откроет все изображения, и по их загрузке сделает чтобы клик возвращал src и вписывал в опцию и блок

        if (file_exists($this->__folder))
        {
            foreach (scandir($this->__folder) as $file)
            {
                if ($file != '.' && $file != '..') // png, jpg проверка
                {
                    $data = [
                        'url' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR . self::FOLDER . DIRECTORY_SEPARATOR . $file,
                        'name' => DIRECTORY_SEPARATOR . self::FOLDER . DIRECTORY_SEPARATOR . $file,
                    ];

                    $galary->images[] = $data;
                }
            }
        }

        $galary->set_main_module();
    }

}
