<div><?php echo_modules($paginate); ?></div>

<div><?php echo_modules($filter); ?></div>

<a href="<?php echo \MBCMS\routes::link('admin_candidats', 'edit') ?>">Добавить соискателя</a>

<table>
    <thead>
        <tr>
            <td>id</td>
            <td>Имя</td>
            <td>Резюмехи</td>
            <td>Пол</td>
            <td>Статус</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($__items as $item) : ?>
        <tr>
            <td><?php isset_echo($item->uid); ?></td>
            <td class="left-align">
                <a style="text-decoration: none; color: blue;" href="<?php echo MBCMS\routes::link('admin_candidats', 'edit', "?id=$item->uid"); ?>">
                    <?php isset_echo($item->lastname); ?> <?php isset_echo($item->firstname); ?>
                    <br>
                    <span style="color: #888; font-size: 11px;"><?php isset_echo($item->uname); ?></span>
                </a>
            </td>
            <td><a href="<?php echo \MBCMS\routes::link('admin_resumes', '?uname=' . $item->uname) ?>"><?php isset_echo($item->resumes); ?></a></td>
            <td><?php isset_echo($item->sex); ?></td>
            <td><?php isset_echo($item->status); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div><?php echo_modules($paginate); ?></div>

