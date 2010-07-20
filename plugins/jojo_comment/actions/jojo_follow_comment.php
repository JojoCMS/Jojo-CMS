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
 * @package jojo_article
 */

/* ensure users of this function have access to the admin page */
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin'));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
  echo "You do not have permission to use this function";
  exit();
}


$commentid = Jojo::getFormData('arg1', 0);
$arg2 = Jojo::getFormData('arg2', 'nofollow');
$value = $arg2=='nofollow' ? 1 : 0;

$frajax = new frajax();
$frajax->title = 'Set nofollow for comment - ' . _SITETITLE;
$frajax->sendHeader();

if ($commentid) {
    Jojo::updateQuery("UPDATE {comment} SET nofollow = ? WHERE commentid = ? LIMIT 1", array($value, $commentid));

    $frajax->alert('Comment set to '.$arg2);
}

$frajax->sendFooter();