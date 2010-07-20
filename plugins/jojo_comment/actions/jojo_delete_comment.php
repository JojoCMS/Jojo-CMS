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

$arg1 = Jojo::getFormData('arg1', '');
$arg2 = Jojo::getFormData('arg2', '');
$arg3 = Jojo::getFormData('arg3', '');
$arg4 = Jojo::getFormData('arg4', '');


$commentid = $arg1;

$frajax = new frajax();
$frajax->title = 'Delete Comment - ' . _SITETITLE;
$frajax->sendHeader();

if ($commentid) {

  $data = Jojo::selectQuery("SELECT * FROM {comment} WHERE commentid = ? LIMIT 1", array($commentid));
  if (count($data) > 0) $name = $data[0]['name'];

  Jojo::deleteQuery("DELETE FROM {comment} WHERE commentid = ? LIMIT 1", array($commentid));
  $frajax->assign('comment-wrap-'.$commentid, 'innerHTML', '');
  $frajax->assign('numcomments', 'innerHTML', '');
  $frajax->alert('Comment by '.$name.' deleted.');

}

$frajax->sendFooter();