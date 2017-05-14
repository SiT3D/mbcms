<h1>Статьи</h1>

<?php echo_modules($paginate); ?>

<a href="<?php echo \MBCMS\routes::link('admin_news', 'edit'); ?>" >Добавить статью</a>

<table>
    <thead>
    <tr>
        <td>Миниатюра</td>
        <td>Дата размещения</td>
        <td>Название статьи</td>
        <td>Категория</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($news as $new): ?>
        <tr>
            <td><img <?php attr($new->image_src, 'src=') ?> width="100px"/></td>
            <td><?php isset_echo($new->date) ?></td>
            <td><a href="<?php echo \MBCMS\routes::link('admin_news', 'edit', '?id=' . $new->id); ?>"><?php isset_echo($new->title) ?></a></td>
            <td><?php isset_echo($new->name) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php echo_modules($modules); ?>
