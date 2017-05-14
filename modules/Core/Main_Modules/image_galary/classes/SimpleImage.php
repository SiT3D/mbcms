<?php

namespace image_galary;


class SimpleImage
{

    var $image;
    var $image_type;

    function load($filename)
    {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];

        if ($this->image_type == IMAGETYPE_JPEG)
        {
            $this->image = imagecreatefromjpeg($filename);
        }
        elseif ($this->image_type == IMAGETYPE_GIF)
        {
            $this->image = imagecreatefromgif($filename);
        }
        elseif ($this->image_type == IMAGETYPE_PNG)
        {
            $this->image = imagecreatefrompng($filename);
        }

        return $this;
    }

    function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 100, $permissions = null)
    {
        if ($image_type == IMAGETYPE_JPEG)
        {
            imagejpeg($this->image, $filename, $compression);
        }
        elseif ($image_type == IMAGETYPE_GIF)
        {
            imagegif($this->image, $filename);
        }
        elseif ($image_type == IMAGETYPE_PNG)
        {
            imagepng($this->image, $filename);
        }
        if ($permissions != null)
        {
            chmod($filename, $permissions);
        }

        return $this;
    }

    function output($image_type = IMAGETYPE_JPEG)
    {
        if ($image_type == IMAGETYPE_JPEG)
        {
            imagejpeg($this->image);
        }
        elseif ($image_type == IMAGETYPE_GIF)
        {
            imagegif($this->image);
        }
        elseif ($image_type == IMAGETYPE_PNG)
        {
            imagepng($this->image);
        }

        return $this;
    }

    function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height);

        return $this;
    }

    function getHeight()
    {
        return imagesy($this->image);
    }

    function getWidth()
    {
        return imagesx($this->image);
    }

    function resize($width, $height)
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;

        return $this;
    }

    function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height);

        return $this;
    }

    function scale($scale)
    {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resize($width, $height);

        return $this;
    }
}