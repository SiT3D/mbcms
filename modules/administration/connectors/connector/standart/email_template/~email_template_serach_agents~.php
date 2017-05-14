<div style="width: 100%; max-width: 600px; margin: 0 auto; display: block;">
    <?php foreach ($__resources as $resource) : ?>
        <div style="display: block; padding: 30px 20px; text-align: left; border: 1px solid #eee;background: #fff;">
            <?php if ($resource->src) : ?>
                <img style="float: right" <?php attr($resource->src, 'src=') ?> height="60px"/>
            <?php endif; ?>
            <a <?php attr($resource->href, 'href=') ?>><?php isset_echo($resource->title) ?></a>
            <div><?php isset_echo($resource->dop_title) ?><span> </span><?php isset_echo($resource->name_ru) ?></div>
        </div>
    <?php endforeach; ?>
</div>