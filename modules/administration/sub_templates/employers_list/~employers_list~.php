<div><?php echo_modules($paginate); ?></div>

<div><?php echo_modules($filter); ?></div>

<div><a href="<?php echo \MBCMS\routes::link('admin_employers', 'edit') ?>">Добавить работодателя</a></div>

<table>
    <thead>
    <tr>
        <td>id</td>
        <td>Имя</td>
        <td>Вакансюхи</td>
        <td>Компания</td>
        <td>Статус</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($__items as $item) : ?>
        <tr>
            <td><?php isset_echo($item->id); ?></td>
            <td class="left-align">
                <a href="<?php echo MBCMS\routes::link('admin_employers', 'edit', "?id=$item->userid"); ?>">
                    <?php isset_echo($item->fullname); ?>
                    <br>
                    <span style="font-size: 12px; color: #999;" ><?php isset_echo($item->uname); ?></span>
                </a>
            </td>
            <td>
                <a href="<?php echo \MBCMS\routes::link('admin_vacancies', '?uname=' . $item->uname) ?>"><?php isset_echo($item->vacancies); ?></a>
            </td>
            <td class="left-align">
                <a href="<?php echo \MBCMS\routes::link('admin_companies', 'edit', "?id=$item->cid"); ?>"><?php isset_echo($item->companyname); ?></a>
            </td>
            <td><?php echo_modules(${'status' . $item->id}); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div><?php echo_modules($paginate); ?></div>

