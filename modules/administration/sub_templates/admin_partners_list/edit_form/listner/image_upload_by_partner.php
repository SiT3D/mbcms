<?php

namespace listners;

use event\image_galary\upload;
use image_galary\SimpleImage;
use MBCMS\image_galary;

class image_upload_by_partner
{
}

(new upload())->listen(function (upload $event)
{

    if (\GetPost::uget('partner_id'))
    {
        $tag  = image_galary::factory()->find_by_tags(['partner_id' => \GetPost::uget('partner_id')])->is_mono()->limit(1)->get();

        if ($tag)
        {
            image_galary::factory()->remove_image($tag->image_id);
        }

        image_galary::factory()->add_image_tag($event->getImageId(), \GetPost::uget('partner_id'), 'partner_id');
        image_galary::factory()->add_image_tag($event->getImageId(), 'Привязка к партнеру');
        image_galary::factory()->add_image_tag($event->getImageId(), 'ID статьи ' . \GetPost::uget('partner_id'));

        (new SimpleImage())->load($event->getFilename())->resizeToWidth(500)->save($event->getFilename());
    }
});