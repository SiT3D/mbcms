<li style=" <?php attr($active, 'display:'); ?> " >
    <ul class="dashboard-menu nav nav-list collapse in">
        <?php foreach ($__items as $item) : ?>
        <li><a <?php attr($item[0], 'href='); ?> <?php selected($item[2], true, 'active'); ?> ><?php isset_echo($item[1]); ?></a></li>
        <?php endforeach; ?>
    </ul>
</li>

