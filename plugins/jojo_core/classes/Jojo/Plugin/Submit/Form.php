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
class Jojo_Plugin_Submit_Form extends Jojo_Plugin
{
    function _getContent()
    {
        $formID = isset($_POST['form_id']) ? $_POST['form_id'] : '';
        if ($formID) {
            $response = Jojo_Plugin_Jojo_contact::sendEnquiry($formID);
            $referring_page = $_SERVER['HTTP_REFERER'];
            header('location: ' .  $referring_page);
            exit();
        } 

        global $smarty;

        $content = array();

        /* Check for form injection attempts */
        Jojo::noFormInjection();

        if (count($_POST) >= 2) {
            /* Look for a subject */
            $subject_line = isset($_POST['subject']) ? $_POST['subject'] : _SITETITLE . " Form Submission";
            unset($_POST['subject']);

            /* Look for an email address */
            $contact_email = _FROMADDRESS;
            foreach (array('emailsender', 'email', 'Email', 'form_Email') as $field) {
                if (isset($_POST[$field])) {
                    $contact_email = $_POST[$field];
                    break;
                }
            }

            /* Look for a name */
            $contact_name = _FROMNAME;
            foreach (array('name', 'Name', 'form_Name') as $field) {
                if (isset($_POST[$field])) {
                    $contact_name = $_POST[$field];
                    break;
                }
            }

            /* Who are we sending it to */
            $send_to = isset($_POST['sendtoemail']) ? $_POST['sendtoemail'] : Jojo::either(_CONTACTADDRESS, _FROMADDRESS);

            /* Put the message content together */
            $referring_page = $_SERVER['HTTP_REFERER'];
            $contact_message = "This is an automated response, please do not reply. \n\n" . "A visitor to " . _SITETITLE . " submitted the following information on your form. The address of the form is $referring_page.\n\n";

            /* Add form values */
            foreach ($_POST as $k => $v) {
                if ($k == 'MAX_FILE_SIZE') {
                    continue;
                }
                if (is_array($v)) {
                    $text = "\n";
                    foreach ($v as $k2 => $v2) {
                         $text .= sprintf("    %s: %s\n", $k2, $v2);
                    }
                    $contact_message .= sprintf("%s: %s\n", $k, $text);
                } else {
                    $contact_message .= sprintf("%s: %s\n", $k, $v);
                }
            }

            /* Add files */
            $toAttach = array();
            foreach ($_FILES as $k => $v) {
                if ($v['error'] == UPLOAD_ERR_INI_SIZE || $v['error'] == UPLOAD_ERR_FORM_SIZE || $v['error'] == UPLOAD_ERR_PARTIAL) {
                    /* We didn't get all of the file */
                    return array('content' => 'There was a problem recieving you file/image. Please go back and try again or try sending a smaller file');
                }
                if ($v['error'] != 0 || !is_uploaded_file($v['tmp_name'])) {
                    /* No file was selected, not an error, just ignore it */
                    continue;
                }
                $contact_message .= sprintf("%s: %s\n", $k, 'Attached file "' . $v['name'] . '"');
                $toAttach[] = $v;
            }

            /* Protect against email injection */
            $badStrings = array("Content-Type:",
                             "MIME-Version:",
                             "Content-Transfer-Encoding:",
                             "bcc:",
                             "cc:",
                             "%0A");
            foreach($badStrings as $v){
                if ((strpos($contact_name, $v) !== false) || (strpos($contact_email, $v) !== false) ){
                    header('location: http://en.wikipedia.org/wiki/Email_injection');
                    exit;
                }
            }

            $smtp = Jojo::getOption('smtp_mail_enabled', 'no');
            if ($smtp == 'yes') {
                /* Create the email */
                $mail = new htmlMimeMail();
                if (Jojo::getOption('smtp_mail_user', '')) {
                    $mail->setSMTPParams(Jojo::getOption('smtp_mail_host', 'localhost'), Jojo::getOption('smtp_mail_port', 25), _SITEURL, true, Jojo::getOption('smtp_mail_user', ''), Jojo::getOption('smtp_mail_pass', ''));
                } else {
                    $mail->setSMTPParams(Jojo::getOption('smtp_mail_host', 'localhost'), Jojo::getOption('smtp_mail_port', 25), _SITEURL);
                }
                $mail->setFrom('"' . $contact_name . '" <' . $contact_email . '>');
                $mail->setText($contact_message . Jojo::emailFooter());
                $mail->setSubject($subject_line);

                /* Add the attachments */
                foreach($toAttach as $file) {
                    $mail->addAttachment($mail->getFile($file['tmp_name']), $file['name'], $file['type']);
                }

                /* Send email */
                $result = $mail->send(array($send_to), 'smtp');

                /* Send to Webmaster */
                if ($send_to != Jojo::getOption('webmasteraddress')) {
                    $contact_message .= "\n\n This is a copy of the message sent to $sendto\n";
                    $mail->setText($contact_message . Jojo::emailFooter());
                    $result = $mail->send(array(Jojo::getOption('webmasteraddress')), 'smtp');
                }
            } else {
                /* Email headers */
                $headers = "From: $contact_name <$contact_email>\r\n";
                $headers .= "X-Sender: <$contact_email>\r\n";
                $headers .= "X-Mailer: PHP\r\n";
                $headers .= "X-Priority: 3\r\n";
                $headers .= "Return-Path: <$contact_email>\r\n";

                /* Send email */
                $result = mail($send_to, $subject_line, $contact_message . Jojo::emailFooter(), $headers);

                /* Send to Webmaster */
                if ($send_to != Jojo::getOption('webmasteraddress')) {
                    $contact_message .= "\n\n This is a copy of the message sent to $send_to\n";
                    $result = mail(Jojo::getOption('webmasteraddress'), $subject_line, $contact_message . Jojo::emailFooter(), $headers);
                }
            }

            if ($result) {
                /* log a copy of the message */
                $log             = new Jojo_Eventlog();
                $log->code       = 'form submission';
                $log->importance = 'normal';
                $log->shortdesc  = 'Form submission from '.$contact_name.' '.$contact_email;
                $log->desc       = $contact_message;
                $log->savetodb();
                unset($log);
            } else {
                $smarty->assign('message', 'There was an error sending your message. This error has been logged, so we will attend to this problem as soon as we can.');
                /* log a copy of the message */
                $log             = new Jojo_Eventlog();
                $log->code       = 'form submission';
                $log->importance = 'high';
                $log->shortdesc  = 'Failed form submission from ' . $contact_name . ' (' . $contact_email . ') to ' .  $send_to ;
                $log->desc       = $contact_message;
                $log->savetodb();
                unset($log);
            }

            header('location: ' . _SITEURL . (_MULTILANGUAGE ? '/' . $this->page['pg_language'] : '') . '/thank-you/');
            exit();
        }

        return array('content' => "There may have been an error submitting the form. Please press back and try again.");
    }
}