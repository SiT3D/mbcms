<?php

namespace MBCMS\image_galary;

use event\image_galary\upload;
use event\upload_event;
use MBCMS\block;
use MBCMS\cache;
use MBCMS\files;
use MBCMS\form\form;
use MBCMS\form\input;
use MBCMS\image_galary;
use Plugins\scrollbar;
use trud\classes\auth;


if (!auth::factory()->admin() && !auth::factory()->user())
{
    die('Нельзя так делать =)');
}


/**
 * Class upload_form
 * @package MBCMS\image_galary
 * Вызывает событие, после успешной закачки изображения, для всех подписанных классов.
 */
class upload_form extends \MBCMS\block implements \ajax
{

    private $__multiple = true;

    private $__white_list = ['jpg', 'jpeg', 'png'];
    /**
     * @var int bytes
     */
    private $__size = 1024 * 1024 * 1;

    public function init_files()
    {
        return [
            parent::init_files(),
            new scrollbar(),
            new form(),
            new input(1),
            new block(),
        ];
    }

    public function init()
    {
        parent::init();

        $this->ADDM(block::factory('Загрузка изображений','h3') ,'modules');

        $form = form::factory(__CLASS__ . '->upload', 'standart_image_uplouder');
        $this->ADDM($form, 'modules');

        $opt = new input('images[]', null, input::TYPE_FILE);
        $opt->multiple = $this->__multiple;
        $form->ADDM($opt, 'modules');

        $opt = new input('sub', 'Загрузить изображение', input::TYPE_SUBMIT);
        $form->ADDM($opt, 'modules');

        $this->__user_cms_class = 'upload_form_images';
    }

    /**
     * @param boolean $value
     * @return $this
     */
    public function setMultiple($value)
    {
        $this->__multiple = (boolean)$value;
        return $this;
    }

    /**
     * Смотреть событие!
     */
    public function upload()
    {
        $key = 'images';
        $path = HOME_PATH . 'images';


        if (!isset($_FILES[$key]["error"]))
        {
            self::add_response('upload_errors[]', 'Файлы отсутствуют');
            self::response();
        }

        foreach ($_FILES[$key]["error"] as $index => $error)
        {
            if ($error == UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES[$key]["tmp_name"][$index];
                $filename = basename($_FILES[$key]["name"][$index]);
                $ext = files::get_extension($filename);

                if ($this->__valid_ext($ext) && filesize($tmp_name) <= $this->__size)
                {
                    $filename = md5($filename . '_img' . rand(100000, 999999));
                    $md5_path = $this->__get_md5_path($filename);
                    $md5_path .= '.' . $ext;

                    $global_path = $path . $md5_path;

                    image_galary::create_images_md5_dirs($md5_path);
                    move_uploaded_file($tmp_name, $global_path);


                    $image_id = image_galary::factory()->link_image($md5_path, basename($_FILES[$key]["name"][$index]));
                    (new upload())
                        ->setImageId($image_id)
                        ->setName(basename($_FILES[$key]["name"][$index]))
                        ->setFilename($global_path)
                        ->setExtension($ext)
                        ->call();
                }
                else
                {
                    $__sz = (int) (($this->__size / 1024 / 1024) * 100) / 100;
                    $sz = (int) ((filesize($tmp_name) / 1024 / 1024) * 100) / 100;
                    $__formats = implode(', ', $this->__white_list);
                    form::errors(['err' => ["Файл должен быть ($__formats) и не должен привышать размер в {$__sz}мб ваш файл: [$ext] " . $sz . 'мб']]);
                }

            }
            else
            {
                self::add_response('upload_errors[]', 'При загрузке файлов чтото пошло ни так. Ошибка UPLOAD_ERR_OK.');
            }
        }

        self::response();
    }

    private function __get_md5_path($filename)
    {
        return cache::get_md5_path($filename);
    }

    private function __valid_ext($ext)
    {
        if (in_array($ext, $this->__white_list))
        {
            return true;
        }

        return false;
    }

}
