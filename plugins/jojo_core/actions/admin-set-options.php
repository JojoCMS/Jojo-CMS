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

/* ensure users of this function have access to the admin page */
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin'));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
  echo "You do not have permission to use this function";
  exit();
}

$frajax = new frajax();
$frajax->title = 'Set options - ' . _SITETITLE;
$frajax->sendHeader();

/* Update the database */
$var   = Jojo::getFormData('arg1', '');
$value = Jojo::getFormData('arg2', '');
$result = Jojo::updateQuery("UPDATE {option} SET op_value = ? WHERE op_name = ? LIMIT 1", array($value, $var));

/* Return the result to the user */
if (!$result) {
    $msg = 'Not saved';
} else {
    $msg = 'Saved';
}
$frajax->script('parent.$("#savemsg_'.str_replace('.', '_', $var).'").html("'.$msg.'").fadeIn("slow").fadeTo(5000, 1).fadeOut("slow");');

$frajax->sendFooter();