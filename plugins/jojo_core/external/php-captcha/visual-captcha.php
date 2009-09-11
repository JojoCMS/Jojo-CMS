<?php

/* Using class from http://www.ejeliot.com/pages/2 */

require_once(_BASEPLUGINDIR.'/jojo_core/external/php-captcha/php-captcha.inc.php');

/* define fonts */
$aFonts = array(
        _BASEPLUGINDIR . '/jojo_core/external/php-captcha/gm.ttf',
        _BASEPLUGINDIR . '/jojo_core/external/php-captcha/kickflip.ttf',
        _BASEPLUGINDIR . '/jojo_core/external/php-captcha/karenshand.ttf'
        );

$aFonts = Jojo::applyFilter('captcha_fonts', $aFonts);

/* default CAPTCHA background */
$image = _BASEPLUGINDIR . '/jojo_core/external/php-captcha/captcha2.jpg';

/* for simple customization of the background, add 'images/captcha.jpg' to any plugin / theme (200 x 50 pixels) */
foreach (Jojo::listPlugins('images/captcha.jpg') as $pluginfile) {
    $image = $pluginfile;
    break;
}

/* for more advanced customization of the background, use this plugin filter */
$image = Jojo::applyFilter('captcha_background', $image);

/* create new image */
$oPhpCaptcha = new PhpCaptcha($aFonts, 200, 50);
$oPhpCaptcha->SetBackgroundImages($image);
$oPhpCaptcha->SetNumChars(Jojo::getOption('captcha_num_chars', 3));
$oPhpCaptcha->UseColour(true);
$oPhpCaptcha->Create();
