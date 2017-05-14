<div><?php echo_modules($paginate); ?></div>

<div><?php echo_modules($filter); ?></div>

<div><a href="<?php echo \MBCMS\routes::link('admin_resumes', 'edit') ?>">Добавить резюме</a></div>

<table>
    <thead>
        <tr>
            <td>id</td>
            <td>Название</td>
            <td>Кандидат</td>
            <td>Город</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($__items as $item) : ?>
            <tr>
                <td><?php isset_echo($item->id); ?></td>
                <td class="left-align"><a href="<?php echo \MBCMS\routes::link('admin_resumes', 'edit', "?id=$item->id"); ?>"><?php isset_echo($item->title); ?></a></td>
                <td class="left-align">
                    <a href="<?php echo MBCMS\routes::link('admin_candidats', 'edit', "?id=$item->userid"); ?>">
                        <?php isset_echo($item->lastname); ?> <?php isset_echo($item->firstname); ?>
                        <br>
                        <span><?php isset_echo($item->uname); ?></span>
                    </a>
                </td>
                <td><?php isset_echo($item->city); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div><?php echo_modules($paginate); ?></div>

