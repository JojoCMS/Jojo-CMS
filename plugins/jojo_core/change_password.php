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
        $messages = array();
        $errors = array();

        if (!empty($reset)) {
            /* error checking */
            if (!$_USERID) {
                $errors[] = 'There was an error with your login session. Please login again and try again';
            } else {
                /* ensure current password is correct */
                $data = Jojo::selectRow("SELECT * FROM {user} WHERE userid = ?", array($_USERID));
                if (!Jojo_Auth_Local::checkPassword($old, $data['us_password'], $data['us_salt'])) {
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
                $newpass = Jojo_Auth_Local::hashPassword($new);
                Jojo::updateQuery("UPDATE {user} SET us_password=? WHERE userid=? LIMIT 1", array($newpass, $_USERID));

                if (Jojo::getOption('password_email', 'no')=='yes') {
                    /* Email the username and new password to the user for reference

                    /* get user details for the email */
                    $user = Jojo::selectRow("SELECT us_login, us_firstname, us_lastname, us_email FROM {user} WHERE userid = ? LIMIT 1", $_USERID);

                    $smarty->assign('name',  Jojo::either($user['us_firstname'],$user['us_login']));
                    $smarty->assign('login', $user['us_login']);
                    $smarty->assign('email', $user['us_email']);
                    $smarty->assign('new',   $new);
                    $text = $smarty->fetch('change_password_confirmation.tpl');

                    require_once _BASEPLUGINDIR . '/jojo_core/external/parsedown/Parsedown.php';
                    $parsedown = new Parsedown();
                    $htmltext = $parsedown->text($text);

                     if (Jojo::simpleMail(Jojo::either($user['us_firstname'],$user['us_login']), $user['us_email'], 'Password Change Confirmation', $text, _SITETITLE, _FROMADDRESS, $htmltext)) {
                        $messages[] = 'Your password has been changed. You have been emailed a confirmation of the new password. You do not need to login again.';
                        $smarty->assign('success',   true);
                    } else {
                        $errors[] = 'There was a problem changing the password. Please contact the webmaster for further help ' . _FROMADDRESS;
                    }
                } else {
                    $messages[] = 'Your password has been changed. You do not need to login again.';
                    $smarty->assign('success',   true);
                }
            }

        }

        if ($errors) {
            $smarty->assign('error',     implode("<br />\n", $errors));
        } elseif ($messages) {
            $smarty->assign('messages', implode("<br />\n", $messages));
        }
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
