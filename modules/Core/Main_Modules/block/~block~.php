<?php

    echo "\n";
    if ($__cms_block_type !== 'empty')
    {
        echo '<' . $__cms_block_type . ' '; /* open tag */
        echo_attrs($__cms_attrs);
        echo '>';
    }

    echo ($__text);
    echo_modules($modules);
    echo_modules($after_modules);

    if ($__cms_closing_type && $__cms_block_type !== 'empty')
    {
        echo '</' . $__cms_block_type . '>'; /* close tag */
    }
    echo "\n";



