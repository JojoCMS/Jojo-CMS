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
        Jojo::updateQuery("UPDATE `articlecomment` SET ac_useanchortext='yes', ac_nofollow='no' WHERE articlecommentid = ? LIMIT 1", array($commentid));
    } elseif ($value == 'no') {
        Jojo::updateQuery("UPDATE `articlecomment` SET ac_useanchortext='no' WHERE articlecommentid = ? LIMIT 1", array($commentid));
    } else {
        $frajax->alert('An error occured setting "use anchor text" option');
        $frajax->sendFooter();
        exit();
    }
    $comments = Jojo::selectQuery("SELECT * FROM articlecomment WHERE articlecommentid = ? LIMIT 1", array($commentid));
    $comment = $comments[0];

    $smarty->assign('commentid', $comment['articlecommentid']);
    $smarty->assign('ac_body', $comment['ac_body']);
    $smarty->assign('ac_website', $comment['ac_website']);
    $smarty->assign('ac_anchortext', $comment['ac_anchortext']);
    $smarty->assign('ac_useanchortext', $comment['ac_useanchortext']);
    $smarty->assign('ac_name', $comment['ac_name']);
    $smarty->assign('ac_nofollow', $comment['ac_nofollow']);
    $smarty->assign('ac_timestamp', $comment['ac_timestamp']);
    $smarty->assign('editperms',true);

    $frajax->assign('article-comment-wrap-'.$commentid,'innerHTML',$smarty->fetch('jojo_article_comment.tpl'));
    $frajax->alert('Comment edited - use anchor text: '.$value);
}

$frajax->sendFooter();