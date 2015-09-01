<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

/* 404 header */
header("HTTP/1.0 404 Not Found");

/* output the template - if 404.tpl exists within a theme, this will be used in preference to the Jojo default */
global $smarty;

if (isset($smarty)) {
    $smarty->assign('obfuscatedemail_mailto', Jojo::obfuscateEmail(_WEBMASTERADDRESS, true));
    $smarty->assign('obfuscatedemail', Jojo::obfuscateEmail(_WEBMASTERADDRESS, false));
    echo $smarty->fetch('404.tpl');
}

/* If the page was loaded using Google Chrome's preview (while the user is typing) then don't log the error, they're still typing */
if (isset($_SERVER['HTTP_X_PURPOSE']) && $_SERVER['HTTP_X_PURPOSE'] == ': preview') exit;

$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

/* log the error */
$ref             = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$log             = new Jojo_Eventlog();
$log->code       = '404';
$log->importance = !empty($ref) ? 'high' : 'normal'; //if they came from another page, this could indicate a broken link so is a higher priority
$log->shortdesc  = '404 error: '. _SITEURL . '/' . _SITEURI;
$log->desc       = '404 error on ' . _SITEURI . ' - Referer: ' . $ref . ' - User Agent: ' . $ua ;
$log->savetodb();
unset($log);
ob_end_flush(); // Send the output and turn off output buffering
exit;
