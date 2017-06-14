<?php

if ($dev_mode)
{
    echo "\n";
}

if ($__cms_block_type !== 'empty')
{
    echo '<' . $__cms_block_type . ' ';
    echo_attrs($__cms_attrs);
    echo '>';
}

echo($__text);
echo_modules($modules);
echo_modules($after_modules);

if ($__cms_closing_type && $__cms_block_type !== 'empty')
{
    echo '</' . $__cms_block_type . '>';
}

if ($dev_mode)
{
    echo "\n";
}



