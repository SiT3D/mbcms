<div><?php echo_modules($paginate); ?></div>

<div><?php echo_modules($filter); ?></div>

<div><a href="<?php echo \MBCMS\routes::link('admin_vacancies', 'edit') ?>">Добавить вакансию</a></div>

<table>
    <thead>
        <tr>
            <td>id</td>
            <td>Название</td>
            <td>Пользователь</td>
            <td>Компания</td>
            <td>Статус</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($__items as $item) : ?>
            <tr>
                <td><?php isset_echo($item->id); ?></td>
                <td><a href="<?php echo MBCMS\routes::link('admin_vacancies', 'edit', "?id=$item->id") ?>"><?php isset_echo($item->title); ?></a></td>
                <td><a href="<?php echo MBCMS\routes::link('admin_employers', 'edit', "?id=$item->userid"); ?>"><?php isset_echo($item->fullname); ?></a></td>
                <td><a href="<?php echo MBCMS\routes::link('admin_companies', 'edit', "?id=$item->cid"); ?>"><?php isset_echo($item->companyname); ?></a></td>
                <td><?php isset_echo($item->status); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div><?php echo_modules($paginate); ?></div>
