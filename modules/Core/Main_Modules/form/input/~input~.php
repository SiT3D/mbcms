
<?php if (isset($title) && $title) : ?>

    <div <?php echo_attrs($__cms_attrs); ?>>
        <label class="label req"><?php isset_echo($title); ?></label>
        <br>
        <input 
        <?php attr($placeholder, 'placeholder='); ?> 
        <?php attr($readonly, 'readonly='); ?> 
        <?php attr($multiple, 'multiple='); ?> 
        <?php selected($selected_index, $value, 'checked'); ?> 
        <?php attr($value, 'value=') ?> 
        <?php attr($type, 'type=') ?>
        <?php attr($class, 'class=') ?>  
            <?php attr($name, 'name=') ?> />
    </div>

<?php else : ?>

    <input 
    <?php attr($placeholder, 'placeholder='); ?> 
    <?php attr($readonly, 'readonly='); ?> 
    <?php attr($multiple, 'multiple='); ?> 
    <?php selected($selected_index, $value, 'checked'); ?> 
    <?php attr($value, 'value=') ?> 
    <?php attr($type, 'type=') ?>
    <?php attr($class, 'class=') ?>  
        <?php attr($name, 'name=') ?> />

<?php endif;
