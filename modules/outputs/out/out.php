<?php

namespace MBCMS;

use MBCMS\Forms\animation;
use MBCMS\Forms\cloner;

class out extends block
{

    public $__text, $__db_text, $__prepars_text;

    public function __construct()
    {
        parent::__construct();
        $this->take_alias('<span style="color: lightskyblue; font-weight: bold;">Текст / Блок</span>');
        $this->__cms_closing_type = true;
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new animation(),

        ];
    }

    public function init()
    {
        if (\MBCMS\routes::is_admin())
        {
            $this->fast_edit($this, [
                    new Forms\output($this),
                    new Forms\text($this),
                    new Forms\text_styles($this),
                    new Forms\size($this),
                    new Forms\position($this),
                    new Forms\display($this),
                    new Forms\border($this),
                    new Forms\fixed($this),
                    new Forms\deleter($this),
                    new Forms\images($this),
                    new Forms\flex($this),
                    new cloner($this),
                    new animation($this),

                ]
            );
        }

        parent::init();

    }

    public function preview()
    {
        parent::preview();

        $this->__start_out_css = '+';
        $this->add_attr('__start_out_css', 'start_out_css');
    }

}
