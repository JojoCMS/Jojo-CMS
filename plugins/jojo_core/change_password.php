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

class Jojo_Plugin_Change_password extends Jojo_Plugin
{

    function _getContent()
    {
        $minlength = 8;
        $maxlength = 20;

        global $smarty, $_USERID;

        $reset  = Jojo::getPost('reset', '');
        $old    = Jojo::getPost('oldp',  '');
        $new    = Jojo::getPost('newp',  '');
        $new2   = Jojo::getPost('newp2', '');
        $errors = array();

        if (!empty($reset)) {
            /* error checking */
            if (!$_USERID) {
                $errors[] = 'There was an error with your login session. Please login again and try again';
            } else {
                /* ensure current password is correct */
                $data = Jojo::selectQuery("SELECT * FROM {user} WHERE userid = ? AND (us_password=SHA1(CONCAT(?, us_salt)) OR us_password=MD5(CONCAT(?, us_salt)))", array($_USERID, $old, $old));
                if (count($data) != 1) {
                    $errors[] = 'Your current password does not match';
                }
            }
            /* ensure new password matches confirmation */
            if ($new != $new2)  {
                $errors[] = 'The passwords you entered do not match';
            }

            /* ensure new password is not the same as the original */
            if ($new == $old)  {
                $errors[] = 'The new password is the same as the current password';
            }

            /* ensure new password is 8 - 20 characters */
            if (strlen($new) < $minlength)  {
                $errors[] = 'The new password is too short - passwords must be a minimum of ' . $minlength . ' characters';
            }
            if (strlen($new) > $maxlength)  {
                $errors[] = 'The new password is too long - passwords must be a maximum of ' . $maxlength . ' characters';
            }

            if (!count($errors)) {
                $salt = Jojo::randomString(16);
                Jojo::updateQuery("UPDATE {user} SET us_salt=?, us_password=? WHERE userid=? LIMIT 1", array($salt, sha1($new.$salt), $_USERID));

                /* get user details for the email */
                $users = Jojo::selectQuery("SELECT us_login, us_firstname, us_lastname, us_email FROM {user} WHERE userid = ? LIMIT 1", $_USERID);
                $user = $users[0];

                error_reporting(0);
                $mail = new htmlMimeMail();

                $smarty->assign('name',  Jojo::either($user['us_firstname'],$user['us_login']));
                $smarty->assign('login', $user['us_login']);
                $smarty->assign('new',   $new);
                $text = $smarty->fetch('change-password-confirmation.tpl');

                $mail->setText($text);
                $mail->setFrom(_SITETITLE.' <'._FROMADDRESS.'>');
                $mail->setSubject('Password Change Confirmation');
                $result = $mail->send(array($user['us_email']));

                $content['content'] = 'Your password has been changed. You have been emailed a confirmation of the new password. You do not need to login again.';
                return $content;
            }

        }

        $smarty->assign('error',     implode("<br />\n", $errors));
        $smarty->assign('minlength', $minlength);
        $smarty->assign('maxlength', $maxlength);

        if (!$_USERID) {
            $content['content'] = 'You must be logged in to change your password';
        } else {
            $content['content'] = $smarty->fetch('change_password.tpl');
        }

        $content['head'] = $smarty->fetch('change_password_head.tpl');
        return $content;
    }
}