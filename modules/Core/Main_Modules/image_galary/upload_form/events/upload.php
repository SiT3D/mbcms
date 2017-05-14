<?php

namespace event\image_galary;

use event\event;

class upload extends event
{
    protected $__filename;
    protected $__name;
    protected $__ext;

    public function setImageId($value)
    {
        $this->image_id = $value;
        return $this;
    }

    /**
     * @return null
     */
    public function getImageId()
    {
        return isset($this->image_id) ? $this->image_id : null;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->__filename;
    }

    /**
     * @param $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->__filename = $filename;
        return $this;
    }

    public function getName()
    {
        return $this->__name;
    }

    /**
     * Устанавливает название файла (без пути)
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->__name = $name;
        return $this;
    }

    public function setExtension($ext)
    {
        $this->__ext = $ext;
        return $this;
    }

    public function getExtension()
    {
        return $this->__ext;
    }
}