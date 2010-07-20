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


$arg1 = Jojo::getFormData('arg1', 0);
$arg2 = Jojo::getFormData('arg2', '');
$arg3 = Jojo::getFormData('arg3', '');
$arg4 = Jojo::getFormData('arg4', '');


$commentid = $arg1 ? $arg1 : Jojo::getFormData('commentid', 0);
$userid = $arg2 ? $arg2 : Jojo::getFormData('userid', 0);

/* ensure users of this function have access to the admin page */
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin'));
if (!($page->perms->hasPerm($_USERGROUPS, 'view') || ($_USERID && $_USERID==$userid))) {
    echo "You do not have permission to use this function";
    exit();
}

$body = Jojo::getFormData('comment-body', 0);
$save = Jojo::getFormData('save', 0);

$frajax = new frajax();
$frajax->title = 'Edit Comment - ' . _SITETITLE;
$frajax->sendHeader();

/* Load into textarea action */
if ($commentid && !$body) {

    $comment = Jojo::selectRow("SELECT * FROM {comment} WHERE commentid = ? ", array($commentid));
    $html  = '<form action="actions/jojo_edit_comment.php" target="frajax-iframe" method="post">';
    $html .= '<input type="hidden" name="commentid" value="'.$commentid.'" />';
    $html .= '<input type="hidden" name="userid" value="'.$userid.'" />';
    $html .= '<textarea name="comment-body" id="comment-body-'.$commentid.'" style="width: 98%; height: 300px;">'.htmlentities($comment['bbbody']).'</textarea><br />';
    $html .= '<input type="submit" name="save" value="Save" /></form>';

    $frajax->assign('comment-'.$commentid,'innerHTML',$html);
}

/* Save action */
if ($commentid && $save) {
    $bb = new bbconverter;
    $bb->truncateurl = 30;
    $bb->nofollow = true;
    $bb->setBBCode($body);
    $htmlbody = $bb->convert('bbcode2html');
    Jojo::updateQuery("UPDATE {comment} SET bbbody = ?, body = ? WHERE commentid = ? LIMIT 1", array($body, $htmlbody, $commentid));
    $frajax->assign('comment-'.$commentid,'innerHTML',$htmlbody);
}

$frajax->sendFooter();