<h1>Статьи категории</h1>

<a href="<?php echo \MBCMS\routes::link('admin_news_categories', 'edit') ?>">Добавить новую категорию</a>

<table>
    <thead>
    <tr>
        <td>Название</td>
        <td>Родитель</td>
        <td>Видимость на сайте в меню</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($categories as $category) : ?>
        <tr>
            <td><a href="<?php echo \MBCMS\routes::link('admin_news_categories', 'edit', '?id=' . $category->id) ?>"><?php isset_echo($category->name) ?></a></td>
            <td><?php isset_echo($category->parent_name); ?></td>
            <td><?php isset_echo($category->visible); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>