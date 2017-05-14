<?php

namespace Assets;

use CKEditor\full_ck;
use CKEditor\standart_ck;
use MBCMS\block;

/**
 * Нужно подключать в 2х местах, в init_static_status + init(modules) для автоматичного подключения к
 * элементам с атрибутом [ckeditor]
 * или использовать js апи этого модуля в файле ck_editor.js
 * !!!!!!!! ОБЯЗАТЕЛЬНО ВСТАВЛЯТЬ ВИД ЭТОГО МОДУЛЯ В КОД СТРАНИЦЫ (подключать в позицию этот модуль), иначе ckeditor будет выдавать ошибки.
 * Class ck_editor
 * @package Assets
 */
class ck_editor extends block
{
    public function init_files()
    {
        return [
            parent::init_files(),
            new jQuery(),
            new full_ck(),

        ];
    }

    public function init()
    {
        parent::init();

        $this->ADDM(new full_ck(), 'modules');
    }
}