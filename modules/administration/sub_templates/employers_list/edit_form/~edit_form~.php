<h1>Добавление редактирование компании</h1>

<?php echo_modules($modules); ?>

<?php if ($uid) : ?>
    <div>
        <button id="delete_employer" <?php attr($uid, 'uid=') ?>>Удалить работодателя, его компанию, и все вакансии
        </button>
    </div>
<?php endif; ?>