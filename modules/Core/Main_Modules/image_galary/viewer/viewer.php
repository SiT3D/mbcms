<?php

namespace MBCMS\image_galary;


use MBCMS\image_galary;
use MBCMS\routes;

class viewer extends  \Module
{
    /**
     * @param string $value
     * @return $this
     */
    public function setSrc($image_id)
    {
        $this->__my_src = image_galary::factory()->get_image_src_by_id($image_id);
        $this->__my_src = str_replace(HOME_PATH, '', $this->__my_src);
        return $this;
    }

    /**
     * @param $width int or string
     * @param $height
     * @return $this
     */
    public function setSize($width, $height)
    {
        $this->__width = $width;
        $this->__height = $height;
        return $this;
    }


    /**
     * @param array $tags indexis array
     * @return $this
     */
    public function setGalaryAdminHref($tags)
    {
        $req = '';

        $first = true;

        foreach ($tags as $tag)
        {
            $tag = str_replace(' ', '_', $tag);

            if ($first)
            {
                $req .= '?filters[]=' . $tag;
                $first = false;
            }
            else
            {
                $req .= '&filters[]=' . $tag;
            }
        }


        $this->__href = routes::link('admin_galary', $req);
        return $this;
    }
}