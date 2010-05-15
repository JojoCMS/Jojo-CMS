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
 * @package jojo_contact
 */

class Jojo_Plugin_Jojo_contact extends Jojo_Plugin
{

    function sendEnquiry()
    {
        global $smarty;

        /* Check for form injection attempts */
        Jojo::noFormInjection();

        $fields = array();
        /* Fields from jojo_contact_fields.php in any plugin or theme */
        include array_pop(Jojo::listPlugins('jojo_contact_fields.php'));

        $errors = array();
        $from_email = '';
        $to_email = '';

       foreach ($fields as &$field) {
            /* set field value from POST */
            if (is_array($_POST['form_' . $field['field']])) {
                /* convert array to string */
                $field['value'] = implode(', ', $_POST['form_' . $field['field']]);
                /* create an assoc array for resetting the value of checkboxes when server-side checking fails */
                $field['valuearr'] = array();
                foreach($_POST['form_' . $field['field']] as $f) {
                    $field['valuearr'][$f] = $f;
                }
            } else {
                $field['value'] = Util::getFormData('form_' . $field['field'], '');
            }
            /* set the fromemail value if appropriate */
            if (isset($from_email_field) && $field['field'] == $from_email_field) {
                $from_email = $field['value'];
            }
            /* set the toemail value if appropriate */
            if (isset($to_email_field) && $field['field'] == $to_email_field) {
                $to_email = $field['value'];
            }
            /* check value is set on required fields */
            if ($field['required'] && empty($field['value'])) {
                $errors[] = $field['display'] . ' is a required field';
            }
            if (!empty($field['value'])) {
                switch ($field['validation']) {
                case 'email':
                   if (!Jojo::checkEmailFormat($field['value'])) {$errors[] = $field['display'] . ' is not a valid email format';}
                   break;
                case 'url':
                   $field['value'] = addHttp($field['value']);
                   if (!Jojo::checkUrlFormat($field['value'])) {$errors[] = $field['display'] . ' is not a valid URL format';}
                   break;
                case 'text':
                   //do we need to check anything?
                   break;
                case 'integer':
                   if (!is_numeric($field['value'])) {$errors[] = $field['display'] . ' is not an integer value';}
                   break;
                case 'numeric':
                   if (!is_numeric($field['value'])) {$errors[] = $field['display'] . ' is not an integer value';}
                   break;
                }
            }
        }
        unset($field);

        if (Jojo::getOption('contactcaptcha') == 'yes') {
            $captchacode = Jojo::getFormData('CAPTCHA','');
            if (!PhpCaptcha::Validate($captchacode)) {
                $errors[] = 'Incorrect Spam Prevention Code entered';
            }
        }

        if ((Jojo::getOption('contact_choice') == 'yes') && (Jojo::getOption('contact_choice_list') != '')) {
            $to       =  explode(",", $_POST['form_sendto']);
            $to_name  =  $to[0];
            $to_email =  $to[1];
        } else {
            $to_email       =  empty($to_email) ? Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS) : $to_email;
            $to_name       =  Jojo::either(_FROMNAME, _WEBMASTERNAME);
        }

        $from_name = '';
        foreach ($from_name_fields as $fromfieldname) {
            foreach ($fields as $field) {
                if ($fromfieldname == $field['field']) {
                    $from_name .= $field['value'];
                    continue 2;
                }
            }
           $from_name .= $fromfieldname;
        }
        $from_name = empty($from_name) ? Jojo::getOption('sitetitle') : $from_name;

        $from_email = empty($from_email) ? Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS) : $from_email;

        $subject  = 'Message from ' . Jojo::getOption('sitetitle') . ' website'." - to: $to_name, $to_email";

        $message  = '';
        foreach ($fields as $f) {
            if ($f.displayonly) { continue; }
            $message .= $f['display'] . ': ' . $f['value'] . "\r\n";
        }
        $message .= Jojo::emailFooter();

        if (!count($errors)) {
            if (Jojo::simpleMail($to_name, $to_email, $subject, $message, $from_name, $from_email)) {
                /* success */
                $successMessage = Jojo::getOption('contact_success_message');
                $smarty->assign('message', Jojo::either($successMessage, 'Your message was sent successfully.'));

                /* send a copy to the webmaster */
                if (Jojo::getOption('contact_webmaster_copy') != 'no' AND $to_email != _WEBMASTERADDRESS) { //note the !='no' which will ensure the default is to send the email (for installations where setup has not been run recently)
                    Jojo::simpleMail(_WEBMASTERNAME, _WEBMASTERADDRESS, $subject, $message, $from_name, $from_email);
                }

                /* log a copy of the message */
                $log             = new Jojo_Eventlog();
                $log->code       = 'enquiry';
                $log->importance = 'normal';
                $log->shortdesc  = 'Enquiry from '.$from_name.' '.$from_email;
                $log->desc       = $message;
                $log->savetodb();
                unset($log);

                return true;

            } else {
                $smarty->assign('message', 'There was an error sending your message. This error has been logged, so we will attend to this problem as soon as we can.');
                /* log a copy of the message */
                $log             = new Jojo_Eventlog();
                $log->code       = 'enquiry';
                $log->importance = 'high';
                $log->shortdesc  = 'Failed Enquiry from ' . $from_name . ' (' . $from_email . ') to ' . $to_name . ' (' . $to_email . ')';
                $log->desc       = $message;
                $log->savetodb();
                unset($log);
            }
        } else {
            $smarty->assign('message', implode("<br />\n", $errors));
            $smarty->assign('fields', $fields);
        }
        return false;
    }

    function _getContent()
    {
        global $smarty;
        $content = array();

        $fields = array();
        /* Fields from jojo_contact_fields.php in any plugin or theme */
        include array_pop(Jojo::listPlugins('jojo_contact_fields.php'));

        /* setup send to choices if it is set in options */
        if ((Jojo::getOption('contact_choice') == 'yes') && (Jojo::getOption('contact_choice_list') != '')) {
            $toaddresses = self::getToAddresses();
            $smarty->assign('toaddresses',$toaddresses);
        }

        $smarty->assign('posturl', $this->getCorrectUrl());

        $smarty->assign('fields',$fields);

        $sent = false;
        if (isset($_POST['submit'])) {
            $sent = $this->sendEnquiry();
        }
        $smarty->assign('sent', $sent);
        $smarty->assign('content', $this->page['pg_body']);

        $content['content']    = $smarty->fetch('jojo_contact.tpl');
        $content['javascript'] = $smarty->fetch('jojo_contact_js.tpl');

        return $content;
    }

    /* Create the array for the name and email addresses on the send to options */
    function getToAddresses()
    {
        static $_toAddresses;

        /* Fetch options from database if we don't have them */
        if (!is_array($_toAddresses)) {
            $_toAddresses = array();
            $rawList = Jojo::getOption('contact_choice_list');
            $list = explode(",", $rawList);
            $n = count($list);
            $i = 0;
            $j = 0;
            while($i < $n) {
                $_toAddresses[$j]['name'] = trim($list[$i]);
                $_toAddresses[$j]['email'] = trim($list[$i]);
                $i++;
                $_toAddresses[$j]['email'] .= ",".trim($list[$i]);
                $i++;
                $j++;
            }
        }

        return $_toAddresses;
    }

}