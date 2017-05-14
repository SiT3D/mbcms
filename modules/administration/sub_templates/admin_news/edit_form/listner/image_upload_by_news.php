<?php

namespace listners;

use event\image_galary\upload;
use image_galary\SimpleImage;
use MBCMS\image_galary;

class image_upload_by_news
{
}

(new upload())->listen(function (upload $event)
{

    if (\GetPost::uget('news_id'))
    {
        $tag  = image_galary::factory()->find_by_tags(['news_id' => \GetPost::uget('news_id')])->is_mono()->limit(1)->get();

        if ($tag)
        {
            image_galary::factory()->remove_image($tag->image_id);
        }

        image_galary::factory()->add_image_tag($event->getImageId(), \GetPost::uget('news_id'), 'news_id');
        image_galary::factory()->add_image_tag($event->getImageId(), 'Привязка к статье');
        image_galary::factory()->add_image_tag($event->getImageId(), 'ID статьи ' . \GetPost::uget('news_id'));

        (new SimpleImage())->load($event->getFilename())->resizeToWidth(500)->save($event->getFilename());
    }
});