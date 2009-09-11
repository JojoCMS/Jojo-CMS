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

/*
 A generic form handler script. Point any form at "http://www.domain.com/submit-form/", and it will send the contents of the POST array to the admin email.
 */
class Jojo_Plugin_Submit_form extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty;

        $content = array();

        /* Check for form injection attempts */
        Jojo::noFormInjection();

        if (isset($_POST['submit']) || isset($_POST['Submit']) || isset($_POST['btn_submit'])) {
            if (isset($_POST['subject'])) {
                $subject_line = $_POST['subject'];
                unset($_POST['subject']);
            } else {

                $subject_line = _SITETITLE . " Form Submission";
            }

            if (isset($_POST['emailsender'])) {
                $contact_email = $_POST['emailsender'];
                unset($_POST['emailsender']);
            } elseif (isset($_POST['Email'])) {
                $contact_email = $_POST['Email'];
                //unset($_POST['Email']);
            } else {
                $contact_email = _FROMADDRESS;
            }

            if (isset($_POST['name'])) {
                $contact_name = $_POST['name'];
                unset($_POST['name']);
            } else {
                $contact_name = _FROMNAME;
            }

            $referring_page = $_SERVER['HTTP_REFERER'];

            if (isset($_POST['sendtoemail'])) {
                $send_to = $_POST['sendtoemail'];
            } else {
                //default to admin person
                $send_to = Jojo::either(_CONTACTADDRESS, _FROMADDRESS);
            }

            $contact_message = "This is an automated response, please do not reply. \n\n" . "A visitor to " . _SITETITLE . " submitted the following information on your form. The address of the form is $referring_page.\n\n";
            // Add form values
            foreach ($_POST as $k => $v) {
                $contact_message .= sprintf("%s: %s\n", $k, $v);
                //assign post variables to smarty
                $smarty->assign('post_' . $k, $v);
            }

            // Create message headers
            $headers = "From: $contact_name <$contact_email>\r\n";
            $headers .= "X-Sender: <$contact_email>\r\n";
            //mailer
            $headers .= "X-Mailer: PHP\r\n";
            //1 UrgentMessage, 3 Normal
            $headers .= "X-Priority: 3\r\n";
            $headers .= "Return-Path: <$contact_email>\r\n";

            $contact_message .= Jojo::emailFooter();

            // Send email
            $res = mail($send_to, $subject_line, $contact_message, $headers);

            //send to webmaster
            $res = mail(Jojo::getOption('webmasteraddress'), $subject_line, $contact_message, $headers);

            if ($res) {
                $smarty->assign('message', 'Your message has been sent.');
            } else {
                $smarty->assign('message', 'There was an error processing the form.');
                echo "There was an error processing the form.";
            }

            if (_MULTILANGUAGE) {
                if (isset($longcodes[$this->page['pg_language']])) {
                    /* Long Language Code */
                    header('location: ' . _SITEURL . '/' . $longcodes[$this->page['pg_language']] . '/thank-you/');
                } else {
                    /* Short Language code */
                    header('location: ' . _SITEURL . '/' . $this->page['pg_language'] . '/thank-you/');
                }
            } else {
                header('location: ' . _SITEURL . '/thank-you/');
            }
            exit();
        }

        echo "There may have been an error submitting the form. Please press back and try again.";
        exit;
        return $content;
    }

    function getCorrectUrl()
    {
        global $secure;
        $link = Jojo::urlPrefix(Jojo::yes2true($this->page['pg_ssl']));
        if ($this->page['pg_url']) {
            $link .= $this->page['pg_url'] . '/';
        } else {
            $link .= Jojo::rewrite('page', $this->page['pageid'], 'index');
        }

        /* Are we on the homepage? */
        $expectedurl = '/' . $link;

        /* Is this a secure page? */
        if ($secure) {
            $expectedurl = _SECUREURL . $expectedurl;
        } else {
            $expectedurl = _SITEURL . $expectedurl;
        }
        return $expectedurl;
    }
}