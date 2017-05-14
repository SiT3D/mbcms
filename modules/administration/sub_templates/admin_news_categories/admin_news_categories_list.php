<?php

namespace trud\admin\templates;

use trud\classes\model\news_categories;

class admin_news_categories_list extends \Module
{
    public function init()
    {
        parent::init();


        $this->categories = (new news_categories)->get_all()->o('parent_id ASC')->get();
        $this->categories = __many($this->categories, [], 'id', true);


        foreach ($this->categories as $cat)
        {
            if (isset($this->categories->{$cat->parent_id}))
            {
                $cat->parent_name = $this->categories->{$cat->parent_id}->name;
            }

            $cat->visible = $cat->visible ? 'Видимая' : '---';
        }
    }
}