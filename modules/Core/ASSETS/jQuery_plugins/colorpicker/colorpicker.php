<?php

namespace Plugins;

/**
 * Class colorpicker
 * @package Plugins
 *
 * in js
 *
 * .colorpicker
 * (
 * {
 * parts: 'full',
 * alpha: true,
 * colorFormat: 'RGBA',
 * ok: function (a, b)
 * {
 * mbcms.visual_fast_edit.get_targets(data).css('background-color', b.formatted);
 * self.__background_color = b.formatted;
 * self.__color_picker.css({background: b.formatted, color: b.formatted});
 * },
 * select: function (a, b)
 * {
 * mbcms.visual_fast_edit.get_targets(data).css('background-color', b.formatted);
 * self.__background_color = b.formatted;
 * self.__color_picker.css({background: b.formatted, color: b.formatted});
 * }
 * }
 * )
 *
 *
 */
class colorpicker extends \Module
{

}

