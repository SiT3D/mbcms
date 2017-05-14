<?php


namespace trud\admin\templates;


use MBCMS\cache;
use MBCMS\DB;
use trud\classes\vacancy_search;
use trud\conn\connector;
use trud\site_metrics;
use trud\templates\by_cities;

class admin_synonyms extends \Module implements \adminAjax
{

    public function __construct()
    {
        parent::__construct();
    }

    public function ajax_find()
    {
        $value = site_metrics::morph(\GetPost::uget('value'));
        $ret   = [];

        if ($value)
        {
            $ret = DB::q()->s(['*'], 't_synonyms')->w('word = ?', $value)->get();
        }

        self::add_response('values', $ret);
        self::response();
    }


    public function ajax_delete()
    {
        if ($id = \GetPost::uget('id'))
        {
            DB::q("DELETE FROM t_synonyms WHERE id= ?", [$id])->get();
        }

        self::response();
    }

    public function ajax_set()
    {
        $value   = trim(mb_strtolower(\GetPost::uget('value')));
        $keyword = site_metrics::morph(\GetPost::uget('keyword'));
        $id      = \GetPost::uget('id');
        $morph   = site_metrics::morph($value);

        if ($id)
        {
            DB::q(" UPDATE  t_synonyms SET synword=?, synword_morphy=? WHERE id=? ", [$value, $morph, $id])->get();
            self::add_response('id', $id);
        }
        else if (!$id && $keyword)
        {
            $id = DB::q("INSERT INTO t_synonyms (word,synword,synword_morphy) VALUES (?,?,?) ", [$keyword, $value, $morph])->get();

            self::add_response('id', $id);
        }

        self::response();
    }

    public function ajax_ss()
    {
        DB::q(" UPDATE  t_synonyms SET search=? WHERE id=? ", \GetPost::ar(['value', 'id'], true))->get();
        self::response();
    }

    public function ajax_sl()
    {
        DB::q(" UPDATE  t_synonyms SET islike=? WHERE id=? ", \GetPost::ar(['value', 'id'], true))->get();
        self::response();
    }
}