<div id="images_galary">
    <?php foreach ($images as $image) : ?>
        <image <?php attr($image['name'], 'name='); ?> <?php attr($image['url'], 'src='); ?> <?php attr($width, 'width='); ?> height="auto" />
    <?php endforeach; ?>
</div>

