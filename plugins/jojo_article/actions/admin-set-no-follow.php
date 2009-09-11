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

$id = Jojo::getFormData('arg1', '');
$value = Jojo::getFormData('arg2', '');

$frajax = new frajax();
$frajax->title = 'Set nofollow for comment - ' . _SITETITLE;
$frajax->sendHeader();

Jojo::updateQuery("UPDATE {articlecomment} SET ac_nofollow= ? WHERE articlecommentid = ? LIMIT 1", array($value, $id));

$frajax->alert('Done');
$frajax->redirect(_SITEURL . '/admin/');
$frajax->sendFooter();