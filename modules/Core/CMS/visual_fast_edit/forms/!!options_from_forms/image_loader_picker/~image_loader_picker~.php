<?php if ($__type == MBCMS\Forms\OPT\image_loader_picker::TYPE_LOAD) : ?>

    <input name="files[]" type="file" multiple="true" />
    <input type="hidden" name="class" value="MBCMS\Forms\OPT\image_loader_picker->save_file_ajax" />
    <input type="submit" id="upload_images_mbcms" value="Сохранить изображения"/>

<?php else : ?>

    <button id="images_form_picker_btn">Выбрать изображение</button>
    <input id="images_form_picker_hidden" type="hidden" name="data[__user_cms_src]" value="" />

<?php endif;