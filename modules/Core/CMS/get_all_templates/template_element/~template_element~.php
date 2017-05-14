<div class="boxes-right-ul" <?php echo_attrs($__cms_attrs); ?>>
    <div class="boxes-li" <?php echo_attrs($__cms_attrs); ?> >
        <span <?php echo_attrs($__cms_attrs); ?> >
            <span class="this-is-template-title" style=" font-weight: bold;"><?php isset_echo($title); ?></span>
            <span class="this-is-template-id" <?php attr($idTemplate, 'idtemplate'); ?> >( ID = <?php isset_echo($idTemplate); ?>)</span>
        </span>
    </div>
    <div class="hidden-panel">
        <a id="del_template" class="href template ico del" <?php echo_attrs($__cms_attrs, 'btns'); ?>></a>
        <a id="transfer_template" class="href template ico transfer-document"  <?php echo_attrs($__cms_attrs, 'btns'); ?>></a>
    </div>
    <div class="comment"><?php isset_echo($desc); ?></div>

</div>

