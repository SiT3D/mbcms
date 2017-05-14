<div id="MBCMS_MAIN_TEMPLATES_TAB_CREATE_MODULE" class="templates" style="padding-left: 3%;">
    <button id="add_folder" title="Добавить папку в текущий каталог">F+</button>
    <span id="TEMPLATES_current_folder_path">
        <?php if (isset($pathes) && !empty($path)) : ?>
            <a class="templates-path_href root" href="">ROOT</a>
            <?php foreach ($pathes as $path) : ?>
                <?php if ($path !== '') : ?>
                    <a class="templates-path_href" href="">/<?php echo $path; ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </span>
    <div id="MBCMS_MAIN_TEMPLATES_TAB_CREATE_MODULES">
        <?php echo_modules($modules); ?>
    </div>
</div>