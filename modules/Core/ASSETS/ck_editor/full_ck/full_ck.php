<?php

namespace CKEditor;

class full_ck extends \Module
{
    public $path = '';

    public function init()
    {
        parent::init();

        if ($this->__cms_module_position == self::NO_POSITION)
        {
            /* MDS */
            echo 'VAR DUMP 16:47 13.03.2017 standart_ck.php 16 <br>';
            echo '<pre>';
            var_dump('Модуль должен иметь позицию, иначе не будут подключены его файлы');
            echo '</pre>';
            echo '<br>';
            /* MDS */
        }

        $path = str_replace(HOME_PATH, '',__DIR__);
        $this->path = 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . 'bottom_js/ckeditor.js';

    }
}