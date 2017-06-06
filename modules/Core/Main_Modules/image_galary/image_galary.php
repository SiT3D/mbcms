<?php

namespace MBCMS;

use GetPost;
use image_galary\SimpleImage;
use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\form\select;
use MBCMS\image_galary\one_image;
use MBCMS\image_galary\upload_form;
use Plugins\scrollbar;
use trud\templates\paginator;

class image_galary extends \Module implements \adminAjax
{

    const FOLDER_NAME = 'images';
    const COUNT       = 100;
    private static $__class = null;

    /**
     * @param $md5_path
     */
    public static function create_images_md5_dirs($md5_path)
    {
        files::create_path_dirs($md5_path, HOME_PATH . DIRECTORY_SEPARATOR . self::FOLDER_NAME);
    }

    public function init_files()
    {
        return [
            parent::init_files(),
            new scrollbar(),
            new form(),
            new upload_form(),
            new paginator(null, null, null),
            new one_image(),
        ];
    }

    public function init()
    {
        parent::init();


        $this->ADDM(new upload_form(), 'content');

        $this->__images();
        $this->__tags();
    }

    private function __images()
    {
        $page  = GetPost::uget('pg', 1);
        $query = DB::q()
            ->s(['*'], 't_images')
            ->limit(self::COUNT)
            ->o('t_images.id ASC')
            ->offset(($page - 1) * self::COUNT);

        $this->__filter_search($query);

        $paginator = new paginator($query->count(), $page, self::COUNT);
        $this->ADDM($paginator, 'content');

        $images = $query->get();

        foreach ($images as $image)
        {
            if ($image->id)
            {
                $one           = new one_image();
                $one->src      = 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR . self::FOLDER_NAME . $image->dir;
                $one->name     = $one->alt = $image->name;
                $one->id       = $image->id;
                $one->tags     = explode(',', $image->mytags);
                $one->tags_ids = explode(',', $image->mytags_ids);

                $this->ADDM($one, 'content');
            }
        }

        $paginator = new paginator($query->count(), $page, self::COUNT);
        $this->ADDM($paginator, 'content');
    }

    private function __filter_search(DB $query)
    {
        $filters = GetPost::get('filters', []);

        if (count($filters))
        {
            $query->s([
                'DISTINCT t_images.id',
                't_images_tags.image_id',
                't_images.*',
                "group_concat(TT.value) as mytags",
                'group_concat(TT.id) as mytags_ids',
            ]);

            $query->j('(SELECT * FROM t_images_tags) AS TT', 'TT.image_id = t_images.id');
            $query->w('TT.name = ?', 'tag');

            $query->wc('L', 'AND');
            foreach ($filters as $filter)
            {
                if ($filter !== 'Нет ни одной метки')
                {
                    $query->w('t_images_tags.value = ?', $filter, 'OR');
                }
                else
                {
                    $query->w('t_images_tags.image_id IS NULL', [], 'OR');
                }
            }
            $query->wc('R', '');
        }
        else
        {
            $query->s([
                "group_concat(t_images_tags.value separator ',') as mytags",
                "group_concat(t_images_tags.id separator ',') as mytags_ids",
                't_images.*',
            ]);
        }

        $query->g('t_images.id');
        $query->lj('t_images_tags', "t_images_tags.image_id = t_images.id");
        $query->w('t_images_tags.name = ?', 'tag');
    }

    private function __tags()
    {
        $form                = form::factory(null);
        $form->not_ajax_send = true;
        $form->method        = 'GET';
        $this->ADDM($form, 'right');

        $tags = DB::q()->s(['*'], 't_images_tags')
            ->g('value')
            ->w('name = ?', ['tag'])
            ->get();

        $cl        = new \stdClass();
        $cl->value = 'Нет ни одной метки';
        $tags[]    = $cl;

        $opt         = new select('filters[]');
        $opt->values = GetPost::get('filters', []);
        $opt->__id   = 'select_filter_galary';
        $opt->add_attr('__id', 'id');
        foreach ($tags as $tag)
        {
            $opt->options[] = ['value' => $tag->value, 'title' => $tag->value . ' ( ' . str_replace('_', ' ', $tag->value) . ' )'];
        }
        $opt->multiple = true;
        $form->ADDM($opt, 'modules');

        $opt = new input('', 'Фильтр', input::TYPE_SUBMIT);
        $form->ADDM($opt, 'modules');
    }

    /**
     * Связывает изображение с базой данных
     * @param $dir путь к файлу от папки images пример "/aa/bb/cc/dd/ee/ff/xxxxxxxxxxxxxxxxxxxx.jpg"
     * @param $name имя файла с расширением
     * @return array|null|object
     */
    public function link_image($dir, $name)
    {
        routes::not_ajax(__METHOD__);

        return DB::i('t_images', ['dir' => $dir, 'name' => $name])->get();
    }

    /**
     * @param $image_id
     * @param $width
     * @internal param $height
     */
    public function resize_image($image_id, $width)
    {
        $filename = $this->get_image_filename_by_id($image_id);
        (new SimpleImage())->load($filename)->resizeToWidth($width)->save($filename);
    }

    /**
     *
     * @param $image_id
     * @return null|string
     */
    public function get_image_filename_by_id($image_id)
    {
        routes::not_ajax(__METHOD__);

        $result = DB::q()->s(['*'], 't_images')->w('id = ?', $image_id)->is_mono()->get();

        if (isset($result->dir) && $result->dir)
        {
            $filename = self::FOLDER_NAME . $result->dir;

            return file_exists(HOME_PATH . $filename) ? HOME_PATH . $filename : null;
        }

        return '';
    }

    /**
     *
     */
    public function ajax_remove_image()
    {
        $id = GetPost::uget('id');
        $this->remove_image($id);
        self::response();
    }

    /**
     *
     * @param $image_id
     */
    public function remove_image($image_id)
    {
        routes::not_ajax(__METHOD__);

        if (!$image_id)
        {
            return;
        }

        $filename = $this->get_image_filename_by_id($image_id);

        if ($filename)
        {
            DB::d('t_images')->w('id = ?', $image_id)->get();
            DB::d('t_images_tags')->w('image_id = ?', $image_id)->get();
            files::remove_dir($filename);
        }
    }

    public function ajax_add_tag()
    {
        list($tag_value, $image_id) = GetPost::ar(['tag_value', 'image_id'], true);
        $id = $this->add_image_tag($image_id, $tag_value);
        self::add_response('tag_id', $id);
        self::response();
    }

    /**
     *
     * @param integer $image_id
     * @param mixed $tag_value
     * @param string $tag_name - ключ по которому привязываем значение, любая строка кроме tag - это стандартный публичный атрибут для поиска в админке
     * но также можно прикрепить скажем company_id|user_id и так далее для связи с компанией или пользователем
     * @return array|null|object
     */
    public function add_image_tag($image_id, $tag_value, $tag_name = 'tag')
    {
        routes::not_ajax(__METHOD__);

        $tag_value = str_replace(' ', '_', $tag_value);


        $query = DB::q()
            ->s(['*'], 't_images_tags')
            ->w('value = ?', $tag_value)
            ->w('name = ?', $tag_name)
            ->w('image_id = ?', $image_id)
            ->is_mono()
            ->get();

        $id = isset($query->id) ? $query->id : null;

        if ($id)
        {
            DB::q()
                ->u(['name' => $tag_name, 'value' => $tag_value], 't_images_tags')
                ->w('id = ?', $id)
                ->get();
        }
        else
        {
            $id = DB::i('t_images_tags', ['image_id' => $image_id, 'value' => $tag_value, 'name' => $tag_name])->get();
        }

        return $id;
    }

    public function ajax_remove_tag()
    {
        $this->remove_image_tag(GetPost::uget('tag_id'));
    }

    /**
     *
     * @param $tag_id
     */
    public function remove_image_tag($tag_id)
    {
        routes::not_ajax(__METHOD__);
        DB::d('t_images_tags')->w('id = ?', $tag_id)->get();
    }

    /**
     *
     * @param $image_id
     * @return \MBCMS\DB
     */
    public function get_tags_by_image_id($image_id)
    {
        routes::not_ajax(__METHOD__);

        return DB::q()
            ->s(['*'], 't_images_tags')
            ->w('image_id = ?', $image_id);
    }

    /**
     * @param $tags
     * @return string
     */
    public function get_image_src_by_tags($tags)
    {
        $tag = image_galary::factory()->find_by_tags($tags)->is_mono()->limit(1)->get();

        if ($tag)
        {
            return $this->get_image_src_by_id($tag->image_id);
        }

        return '';
    }

    /**
     * @param array $tags ['tag' => value, 'company_id' => value, 'tag2' => value] etc
     * @return DB
     */
    public function find_by_tags(array $tags)
    {
        $query = DB::q();

        $select = [];
        $i      = 0;

        foreach ($tags as $key => $tag)
        {
            $select[] = 't_images_tags as t' . $i;
            $query->w("(t$i.name = ? AND t$i.value = ?)", [$key, $tag]);
            $i++;
        }

        $query->s(['*'], implode(',', $select));

        return $query;
    }

    /**
     *
     * @return \MBCMS\image_galary
     */
    public static function factory()
    {
        routes::not_ajax(__METHOD__);

        self::$__class = self::$__class ? self::$__class : new image_galary();

        return self::$__class;
    }

    /**
     * @param integer $image_id
     * @return string
     */
    public function get_image_src_by_id($image_id)
    {
        $result = DB::q()->s(['*'], 't_images')->w('id = ?', $image_id)->is_mono()->get();
        $path   = HOME_PATH . self::FOLDER_NAME . (isset($result->dir) ? $result->dir : '');

        if (file_exists($path) && is_file($path))
        {
            return DIRECTORY_SEPARATOR . self::FOLDER_NAME . $result->dir;
        }
        else
        {
            return '';
        }
    }

}
