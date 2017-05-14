<?php

namespace trud\admin\templates;

use MBCMS\DB;
use MBCMS\form\input;
use MBCMS\image_galary;
use trud\templates\paginator;

class news_list extends \Module
{
    public function init()
    {
        parent::init();

        $pg = \GetPost::uget('pg', 1);
        $inPage = 20;
        $query = DB::q()->s(['*', 't_news.id'], 't_news')
            ->lj('t_news_categories', 't_news_categories.id = t_news.category_id')
            ->limit($inPage)->offset(($pg - 1) * $inPage);
        $query = $this->__filter_query($query);
        $count = $query->count();
        $this->news = $query->get();

        foreach ($this->news as $new)
        {
            $new->title = $new->title ? $new->title : 'Нет названия';
            $new->image_src = (new image_galary())->get_image_src_by_tags(['news_id' => $new->id]);
        }

        $this->__filter_form();
        $this->ADDM(new paginator($count, $pg, $inPage), '$paginate');
    }

    private function __filter_query(DB $query)
    {

        if ($value = \GetPost::uget('title'))
        {
            $query->w('title LIKE ?', "%$value%");
        }


        return $query;
    }

    private function __filter_form()
    {
        $form = filter_form::factory('');
        $this->ADDM($form, '$paginate');

        $form->ADDM(input::factory('title', \GetPost::uget('title')), 'modules');
    }
}