<div class="galary-one-image">
    <img <?php attr($src, 'src='); ?> <?php attr($alt, 'alt='); ?> />
    <div class="one-image-name" <?php attr($name, 'title='); ?>><?php isset_echo($name); ?></div>
    <input value="<?php isset_echo($src) ?>" />
    <div class="tags-container">
        <h4>Метки:</h4>
        <button class="add_new_tag"  <?php attr($id, 'add_image_id='); ?> title="Добавить метку к изображению">+</button>
        <?php for ($i = 0; $i < count($tags); $i++) : ?>
            <span class="tag-name" <?php attr($tags_ids[$i], 'tag_id='); ?>><?php isset_echo($tags[$i]); ?></span>
        <?php endfor; ?>
    </div>
    <button  <?php attr($id, 'image_id='); ?>>Удалить</button>
</div>

