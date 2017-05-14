<h2>Заявки на подтверждение</h2>
<div style="color: #999;">
    Также можно просто перейти к ресурсу, и сделать его активным, в этом случае не будет отправлено письмо, а так же с пользователя не будет снята публикация.
    Но модерация такого ресурса пропадет.
</div>

<table>
    <thead>
    <tr>
        <td>Ресурс для премодерации</td>
        <td>Дата создания резюме</td>
        <td>Действия</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($__confirmes as $confirme) : ?>
        <tr>
            <td><a <?php attr($confirme->source_src, 'href') ?>><?php isset_echo($confirme->types) ?></a></td>
            <td><?php isset_echo($confirme->date) ?></td>
            <td>
                <button class="true_confirmes" <?php attr($confirme->id, 'cid=') ?> <?php attr($confirme->type, '_t') ?>>
                    Утвердить
                </button>
                <button class="false_confirmes" <?php attr($confirme->id, 'cid=') ?> <?php attr($confirme->type, '_t') ?>>
                    Отказать
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php echo_modules($modules); ?>