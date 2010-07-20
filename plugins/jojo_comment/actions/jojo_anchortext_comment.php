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
$value = Jojo::getFormData('arg2', '');

$frajax = new frajax();
$frajax->title = 'Set use anchor text for comment - ' . _SITETITLE;
$frajax->sendHeader();

if ($commentid) {

    if ($value == 'yes') {
        Jojo::updateQuery("UPDATE {comment} SET useanchortext='1', nofollow='0' WHERE commentid = ? LIMIT 1", array($commentid));
    } elseif ($value == 'no') {
        Jojo::updateQuery("UPDATE {comment} SET useanchortext='0' WHERE commentid = ? LIMIT 1", array($commentid));
    } else {
        $frajax->alert('An error occured setting "use anchor text" option');
        $frajax->sendFooter();
        exit();
    }
    $frajax->alert('Comment edited - use anchor text: '.$value);
}

$frajax->sendFooter();