<div class="control-group input-prepend input-append" <?php attr($name, '__FE'); ?> >
    <div class="controls">


        <?php if (!$hide_value) : ?>

            <?php if ($type == \MBCMS\Forms\OPT\main_option::TYPE_NUMBER || $type == \MBCMS\Forms\OPT\main_option::TYPE_TEXT) : ?>

                <input id="value_with_metric_v"
                       class="<?php isset_echo($value_width); ?> <?php isset_echo($dop_classes_value); ?> value_with_metric mbcms-bootstrap"
                    <?php attr($name, '__ov='); ?>
                    <?php attr($type, 'type=') ?>
                    <?php attr($value, 'value='); ?>
                    <?php attr($step, 'step='); ?>
                    <?php attr($readonly, 'readonly='); ?>
                    <?php attr($placeholder, 'placeholder='); ?>
                    <?php attr($colorpicker, 'colorpicker=') ?>
                    <?php isset_echo($__value_mousedown); ?>
                       name="data[<?php echo $name; ?>][value]"
                />

            <?php elseif ($type == \MBCMS\Forms\OPT\main_option::TYPE_AREA) : ?>

                <textarea id="value_with_metric_v"
                          class="<?php isset_echo($value_width); ?>  <?php isset_echo($dop_classes_value); ?> value_with_metric mbcms-bootstrap"
                    <?php attr($name, '__ov='); ?>
                    <?php attr($type, 'type=') ?>
                    <?php attr($step, 'step='); ?>
                    <?php attr($__cn, 'cn=') ?>
                    <?php attr($placeholder, 'placeholder='); ?>
                    <?php attr($ckeditor, 'ckeditor=') ?>
                    <?php attr($readonly, 'readonly='); ?>
                    <?php isset_echo($__value_mousedown); ?>
                          name="data[<?php echo $name; ?>][value]"
                ><?php echo($value); ?></textarea>

            <?php endif; ?>

        <?php endif; ?>

        <?php if (!$hide_metric) : ?>

            <select id="value_with_metric_m"
                    class="<?php isset_echo($metric_width); ?> <?php isset_echo($dop_classes_metric); ?> mbcms-bootstrap"
                    __MOUSEDOWN
                <?php attr($multiple_metrix, 'multiple='); ?>
                    __OV="<?php echo $name; ?>"
                <?php if ($multiple_metrix) : ?>
                    name="data[<?php echo $name; ?>][metrica][]">
                <?php else : ?>
                    name="data[<?php echo $name; ?>][metrica]">
                <?php endif; ?>

                <?php foreach ($metrix as $__metrica) : ?>
                    <option <?php selected($__metrica, $metrica, 'selected'); ?> <?php attr($__metrica, 'value='); ?> ><?php isset_echo($__metrica); ?></option>

                <?php endforeach; ?>

            </select>

        <?php endif; ?>

    </div>
</div>

<?php echo_modules($modules) ?>