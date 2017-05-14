<?php if ($__standart) : ?>
    <div class="p404-main">
        <h1>Страница 404</h1>
        <span>Эта страница не существует <a href="/">Главная страница</a> </span>
    </div>
<?php else: ?>
    <?php echo_modules($modules); ?>
<?php endif; 

