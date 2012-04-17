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

class Jojo_Plugin_Forgot_password extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty;

        $content  = array();
        $action   = '';
        $type     = Jojo::getFormData('type', 'reset');
        $search   = Jojo::getFormData('search', '');
        $reset    = Jojo::getFormData('reset', '');
        $messages = array();;
        $errors   = array();


        /* A reset hash has been sent via GET - find the relevant user and generate random password */
        if ($reset != '') {
            $users = Jojo::selectQuery("SELECT userid, us_email, us_login, us_reminder FROM {user} WHERE us_reset= ? LIMIT 1", array($reset));

            if (!count($users)) {
                $errors[] = 'This password reset code has expired. Please use the form below to generate another reset code.';
            } else {
                $userid = $users[0]['userid'];
                $newpassword = Jojo::makepassword();
                /* Save new password to DB. Clear old reminder. Clear reset code. Display password on screen. */
                Jojo::updateQuery("UPDATE {user} SET us_password = ?, us_salt='', us_reminder='', us_reset='' WHERE userid = ? LIMIT 1", array(sha1($newpassword), $userid));
                $messages[] = "Your password has been reset to <b>$newpassword</b>";
                $smarty->assign('changed', true);
                $smarty->assign('newpassword', $newpassword);
            }

        /* Email address / username has been entered */
        } elseif ($search != '') {
            $users = Jojo::selectQuery("SELECT userid, us_email, us_login, us_reminder FROM {user} WHERE us_email = ? OR us_login = ?", array($search, $search));

            if (!count($users)) {
                $errors[] = 'There is no user in our system with email address or username: '.$search;
            }
            
            foreach ($users as $user) {
                /* ensure we have an email address */
                $email = $user['us_email'];
    
    
                if (($type == 'reminder') && ($user['us_reminder'] == '')) {
                    $action = 'reset';
                    $messages[] = 'There is no password reminder for this account - sending password reset link instead.';
                } else  {
                    $action = $type;
                }
                
    
                if (empty($email) && !count($errors)) {
                    $errors[] = 'There is no email address stored against this user account, so the password is unable to be reset. Please contact the webmaster ('._FROMADDRESS.') to manually reset your password.';
                } elseif ($action == 'reminder') {
                    /* Send reminder email */
                    $reminder = $user['us_reminder'];
                    $login    = $user['us_login'];
                    $userid   = $user['userid'];
    
                    $mail = new htmlMimeMail();
    
                    $smarty->assign('email',    $email);
                    $smarty->assign('login',    $login);
                    $smarty->assign('reminder', $reminder);
                    $text = $smarty->fetch('forgot-password-reminder.tpl');
    
                    $mail->setText($text);
                    $mail->setFrom(_SITETITLE.' <'._FROMADDRESS.'>');
                    $mail->setSubject('Password Reminder');
                    $result = $mail->send(array($email));
                    $messages[] = $result ? 'Password reminder has been sent to the email address associated with username '.$login : 'There was an error sending the reminder email. Please contact the webmaster for further help '._FROMADDRESS;
                } else if ($action == 'reset') {
                    $userid = $user['userid'];
                    $login  = $user['us_login'];
                    /* Generate a random hash and store this against the user */
    
                    /* keep generating random codes until we get a unique one */
                    while (empty($auth)) {
                        $auth = strtolower(Jojo::randomstring(16));
                        $data = Jojo::selectQuery("SELECT COUNT(*) AS num FROM {user} WHERE us_reset = ?", array($auth));
                        if ($data[0]['num'] > 0) unset($auth);
                    }
                    Jojo::updateQuery("UPDATE {user} SET us_reset = ? WHERE userid = ? LIMIT 1", array($auth, $userid));
    
                    /* Send reset email */
                    $mail = new htmlMimeMail();
                    $smarty->assign('email', $email);
                    $smarty->assign('login', $login);
                    $smarty->assign('auth',  $auth);
                    $text = $smarty->fetch('forgot-password-reset.tpl');
                    $mail->setText($text);
                    $mail->setFrom(_SITETITLE.' <'._FROMADDRESS.'>');
                    $mail->setSubject('Password Reset Link');
                    $result  = $mail->send(array($email));
                    $messages[] = $result ? 'Password reset link has been sent to '.$email : 'There was an error sending the Reset email. Please contact the webmaster for further help '._FROMADDRESS;
                }
            }
        }

        $smarty->assign('search', $search);
        $smarty->assign('type',   $type);
        if (!count($errors)) $smarty->assign('messages', $messages);
        $smarty->assign('errors', $errors);
        $content['content'] = $smarty->fetch('forgot-password.tpl');

        return $content;
    }

    function getCorrectUrl()
    {
        $u      = Jojo::getFormData('u', '');
        $search = Jojo::getGet('search', '');
        $reset  = Jojo::getGet('reset',  '');
        if (!empty($search)) {
            return parent::getCorrectUrl().'email/'.$search.'/';
        } elseif (!empty($reset)) {
            return parent::getCorrectUrl().'reset/'.$reset.'/';
        } else {
            return parent::getCorrectUrl();
        }
    }

}