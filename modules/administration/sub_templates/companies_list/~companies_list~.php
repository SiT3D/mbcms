<div><?php echo_modules($paginate); ?></div>

<div><?php echo_modules($filter); ?></div>

<div><a href="<?php echo \MBCMS\routes::link('admin_companies', 'edit') ?>">Добавить компанию</a></div>

<table>
    <thead>
        <tr>
            <td>id</td>
            <td>Название</td>
            <td>Владелец</td>
            <td>Вакансии</td>
            <td>Тип компании</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($__items as $item) : ?>
        <tr>
            <td><?php isset_echo($item->id); ?></td>
            <td class="left-align">
                <a href="<?php echo MBCMS\routes::link('admin_companies', 'edit', "?id=$item->id"); ?>">
                    <?php isset_echo($item->companyname); ?>
                </a>
            </td>
            <td class="left-align">
                <a href="<?php echo \MBCMS\routes::link('admin_employers', 'edit', "?id=$item->uid"); ?>">
                    <?php isset_echo($item->fullname); ?>
                    <br>
                    <span><?php isset_echo($item->uname); ?></span>
                </a>
            </td>
            <td><a href="<?php echo \MBCMS\routes::link('admin_vacancies', '?uname=' . $item->uname) ?>"><?php isset_echo($item->vacancies); ?></a></td>
            <td><?php isset_echo($item->companytype); ?> <?php isset_echo($item->firstname); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div><?php echo_modules($paginate); ?></div>

