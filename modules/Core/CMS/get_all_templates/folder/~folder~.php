<div class="template-folder" <?php attr($fullPath, 'fullPath='); ?> <?php echo_attrs($__cms_attrs); ?> <?php attr($path, 'path='); ?>  >
    <img class="fimage" src="/modules/Core/ASSETS/icons_cms/css/folder-ico.png" />
    <span class="name"><?php isset_echo($name); ?></span>
    <div class="hidden-panel">
        <span class="ico pen" <?php attr($fullPath, 'fullPath='); ?>></span>
        <span class="ico transfer-document" <?php attr($fullPath, 'fullPath='); ?>></span>
        <span class="ico del" <?php attr($fullPath, 'fullPath='); ?> ></span>
    </div>
</div>

