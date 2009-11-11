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

$arg1 = Jojo::getFormData('arg1', 0);
$arg2 = Jojo::getFormData('arg2', '');
$arg3 = Jojo::getFormData('arg3', '');
$arg4 = Jojo::getFormData('arg4', '');


$commentid = $arg1 ? $arg1 : Jojo::getFormData('commentid', 0);
$body = Jojo::getFormData('article-comment-body', 0);
$save = Jojo::getFormData('save', 0);

$frajax = new frajax();
$frajax->title = 'Edit Comment - ' . _SITETITLE;
$frajax->sendHeader();

/* Load into textarea action */
if ($commentid && !$body) {

    $comments = Jojo::selectQuery("SELECT * FROM {articlecomment} WHERE articlecommentid = ? LIMIT 1", array($commentid));
    $comment = $comments[0];
    $html  = '<form action="actions/jojo_article_edit_comment.php" target="frajax-iframe" method="post">';
    $html .= '<input type="hidden" name="commentid" value="'.$commentid.'" />';
    $html .= '<textarea name="article-comment-body" id="article-comment-body-'.$commentid.'" style="width: 98%; height: 300px;">'.htmlentities($comment['ac_bbbody']).'</textarea><br />';
    $html .= '<input type="submit" name="save" value="Save" /></form>';

    $frajax->assign('article-comment-'.$commentid,'innerHTML',$html);
    $frajax->focus('article-comment-body-'.$commentid);
}

/* Save action */
if ($commentid && $save) {
    $bb = new bbconverter;
    $bb->truncateurl = 30;
    $bb->nofollow = true;
    $bb->setBBCode($body);
    $htmlbody = $bb->convert('bbcode2html');
    Jojo::updateQuery("UPDATE articlecomment SET ac_bbbody = ?, ac_body = ? WHERE articlecommentid = ? LIMIT 1", array($body, $htmlbody, $commentid));
    $frajax->assign('article-comment-'.$commentid,'innerHTML',$htmlbody);
    $frajax->alert('Changes saved');
}

$frajax->sendFooter();