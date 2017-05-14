
<?php if (isset($title) && $title) : ?>

    <div>
        <label class="label req"><?php isset_echo($title); ?></label>
        <br>
        <select <?php echo_attrs($__cms_attrs); ?>   <?php attr($multiple, 'multiple='); ?> 
            <?php attr($class, 'class='); ?> <?php attr($name, 'name='); ?> <?php attr($chosen, 'chosen='); ?> >
                <?php if ($with_empty_option) : ?>
                <option <?php selected($eoption['value'], $values, 'selected'); ?>  <?php attr($eoption['value'], 'value='); ?> ><?php isset_echo($eoption['title']); ?></option>
            <?php endif; ?>
            <?php foreach ($options as $option) : ?>
                <option <?php selected($option['value'], $values, 'selected'); ?> <?php attr($option['value'], 'value='); ?> ><?php isset_echo($option['title']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

<?php else : ?>

    <select <?php echo_attrs($__cms_attrs); ?>  <?php attr($multiple, 'multiple='); ?> 
        <?php attr($class, 'class='); ?> <?php attr($name, 'name='); ?> <?php attr($chosen, 'chosen='); ?> >
            <?php if ($with_empty_option) : ?>
            <option <?php selected($eoption['value'], $values, 'selected'); ?>  <?php attr($eoption['value'], 'value='); ?> ><?php isset_echo($eoption['title']); ?></option>
        <?php endif; ?>
        <?php foreach ($options as $option) : ?>
            <option <?php selected($option['value'], $values, 'selected'); ?> <?php attr($option['value'], 'value='); ?> ><?php isset_echo($option['title']); ?></option>
        <?php endforeach; ?>
    </select>

<?php endif; 
