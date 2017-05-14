
<h1>Партнеры</h1>

<?php echo_modules($paginate); ?>

<a href="<?php echo \MBCMS\routes::link('admin_partners', 'edit'); ?>" >Добавить партнера</a>

<table>
    <thead>
    <tr>
        <td>Миниатюра</td>
        <td>Дата размещения</td>
        <td>Название статьи</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($partners as $partner): ?>
        <tr>
            <td><img <?php attr($partner->image_src, 'src=') ?> width="100px"/></td>
            <td><?php isset_echo($partner->date) ?></td>
            <td><a href="<?php echo \MBCMS\routes::link('admin_partners', 'edit', '?id=' . $partner->id); ?>"><?php isset_echo($partner->title) ?></a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php echo_modules($modules); ?>