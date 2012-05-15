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

    function sendEnquiry($formID=false)
    {
        global $smarty;
        /* Check for form injection attempts */
        Jojo::noFormInjection();

        $fields = array();
        $errors = array();
        $from_email = '';
        $from_name = '';
        $sender_email =  Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS);
        $to_email = '';
        $formSubject = '';
        $formID;

        /* Get form from current page id or formid*/
        if ($formID) {
            $formfields = Jojo::selectQuery("SELECT * FROM {form} f LEFT JOIN {formfield} ff ON ( ff.ff_form_id = f.form_id) WHERE f.form_id = ? ORDER BY ff_order", array($formID));
        } else {
            $pageID = $this->page['pageid'];
            $formfields = Jojo::selectQuery("SELECT * FROM {form} f LEFT JOIN {formfield} ff ON ( ff.ff_form_id = f.form_id) WHERE f.form_page_id = ? ORDER BY ff_order", array($pageID));
        }

        /* Find the form that belongs to the current page id and get all the formfields that belong to that form */
        $form = $formfields[0];
        $formID = $form['form_id'];
        $formSend = isset($form['form_send']) ? $form['form_send'] : 1;
        $formName = $form['form_name'];
        $formSubject = $form['form_subject'];
        $formAnalytics = $form['form_tracking_code_analytics'];
        $formTrackingcode = $form['form_tracking_code'];
        $formSuccessMessage = !empty($form['form_success_message']) ? $form['form_success_message'] : (Jojo::getOption('contact_success_message', '') ? Jojo::getOption('contact_success_message') : 'Your message was sent successfully.');

        $smarty->assign('formTrackingcode', $formTrackingcode);

        $fields = Jojo::applyFilter("formfields_first", array(), $formID);
        $f = count($fields);
        foreach ($formfields as $ff) {
            foreach ($ff as $k=>$v) {
                $key = str_replace('ff_', '', $k);
                $fields[$f][$key] = $v;
            }
            $fields[$f]['fieldsetid']       = isset($ff['ff_fieldset']) && $ff['ff_fieldset'] ? Jojo::cleanURL($ff['ff_fieldset']) : '';
            $fields[$f]['field']       = isset($ff['ff_fieldname']) && $ff['ff_fieldname'] ? Jojo::cleanURL($ff['ff_fieldname']) : Jojo::cleanURL($ff['ff_display']);
            $fields[$f]['options']     = explode("\r\n", $ff['ff_options']);
            $f++;
        }
        $fields = Jojo::applyFilter("formfields_last", $fields, $formID);

        if ($form['form_captcha']){
            $captchacode = Jojo::getFormData('CAPTCHA','');
            if (!PhpCaptcha::Validate($captchacode)) {
                $errors[] = 'Incorrect Spam Prevention Code entered';
            }
        }


        if ($form['form_choice'] && $form['form_choice_list']) {
            $sendto       =  $_POST['form_sendto'];
            $formchoices = explode("\r\n", $form['form_choice_list']);
            foreach ($formchoices as $to) {
                $to = explode(',', $to);
                $to_email =  trim(array_pop($to));
                $to_name  =  str_replace('#', '', trim(implode(',', $to)));
                if (Jojo::cleanURL($to_name)==$sendto) {
                    break;
                }
            }

        } else {
            $to_email       =  empty($form['form_to']) ? Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS) : $form['form_to'];
            $to_name       =  Jojo::either(_FROMNAME, _WEBMASTERNAME);
        }

        foreach ($fields as &$field) {
            /* set field value from POST */
            if (isset($_POST['form_' . $field['field']]) && is_array($_POST['form_' . $field['field']])) {
                /* convert array to string */
                $field['value'] = implode("\r\n", $_POST['form_' . $field['field']]);
                /* create an assoc array for resetting the value of checkboxes when server-side checking fails */
                $field['valuearr'] = array();
                foreach($_POST['form_' . $field['field']] as $f) {
                    $field['valuearr'][$f] = $f;
                }
            } elseif (isset($_POST[$field['field']]) && is_array($_POST[$field['field']])) {
                /* convert array to string */
                $field['value'] = implode("\r\n", $_POST[$field['field']]);
                /* create an assoc array for resetting the value of checkboxes when server-side checking fails */
                $field['valuearr'] = array();
                foreach($_POST[$field['field']] as $f) {
                    $field['valuearr'][$f] = $f;
                }
            } elseif (Util::getFormData('form_' . $field['field'], '')) {
                $field['value'] = ($field['type']!='heading') ? Util::getFormData('form_' . $field['field'], '') : $field['value'];
            } else {
                $field['value'] = ($field['type']!='heading') ? Util::getFormData($field['field'], '') : $field['value'];
            }
            /* set the fromemail value if appropriate */

             if($field['is_email']){
                $from_email = $field['value'];
             }
             if($field['is_name']){
                $from_name .= $field['value'];
                $from_name .= ' ';
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

            /* if field is confirmation field then need to check both fields match */
            if($field['type'] == 'emailwithconfirmation') {
                $confirmation = $_POST['form_' . $field['field'] . '_confirmation'];
                if($field['value'] != $confirmation) {
                    $errors[] = $field['display'] . ' and confirmation email fields must match';
                }
            }
        }
        
        if(!count($errors)){
            /* run further validation hook */
            $validationReturn = Jojo::runHook('contact_form_validation_success', array($errors, $fields));
            $errors = $validationReturn[0];
        }
        
        unset($field);
        

        $from_name = empty($from_name) ? Jojo::getOption('sitetitle') : $from_name;
        $from_email = empty($from_email) ? Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS) : $from_email;
        $subject  = $formSubject ? $formSubject : 'Message from the ' . $formName . ' form';
        $subject = mb_convert_encoding($subject, 'HTML-ENTITIES', 'UTF-8');

        $smarty->assign('subject', $subject);

        $message  = '';
        foreach ($fields as $f) {
            if (isset($f['displayonly'])) { continue; };
            if ($f['type'] == 'note') { continue; };
            if ($f['type'] == 'heading') {
                $message .=  "\r\n" . $f['value'] . "\r\n";
                for ($i=0; $i<strlen($f['value']); $i++) {
                    $message .= '-';
                }
                $message .= "\r\n";
            } else {
                $message .= (isset($f['showlabel']) && $f['showlabel'] ? $f['display'] . ': ' : '' ) . ($f['type'] == 'textarea' || $f['type'] == 'checkboxes' ? "\r\n" . $f['value'] . "\r\n\r\n" : $f['value'] . "\r\n" );
            }
        }
        $message .= Jojo::emailFooter();

        $messagefields = '';
        foreach ($fields as $f) {
            if (isset($f['displayonly']) || $f['type'] == 'note') { continue; };
            if ($f['type'] == 'heading') {
                $messagefields .=  '<h' . ($f['size'] ? $f['size'] : '3') . '>' . $f['value'] . '</h' . ($f['size'] ? $f['size'] : '3') . '>';
            } else {
                $messagefields .= '<p>' . (isset($f['showlabel']) && $f['showlabel'] ? $f['display'] . ': ' : '' ) . ($f['type'] == 'textarea' || $f['type'] == 'checkboxes' ? '<br>' . nl2br($f['value']) : $f['value'] ) . '</p>';
            }
        }
        $replymessage = isset($form['form_autoreply_bodycode']) && $form['form_autoreply_bodycode'] ? Jojo::relative2absolute($form['form_autoreply_bodycode'], _SITEURL) :  '';
        if (strpos($replymessage, '[[conditional:')!==false) {
            preg_match('~\[\[conditional:([^\]]*)\]\]~', $replymessage, $matches);
            if (isset($matches[1])) {
                $condition = explode(':', $matches[1]);
                $conditionfield = $condition[0];
                $conditional = $condition[1];
                $conditionstate = false;
                foreach ($fields as $f) {
                    if ($f['display'] == $conditionfield && $f['value'] == $conditional) {
                        $conditionstate = true;
                        break;
                    }
                }
                $replymessage = explode('[[else]]', $replymessage);
                $replymessage = $conditionstate ? str_replace($matches[0], '', $replymessage[0]) : $replymessage[1];
            }
        }
        $htmlcss = isset($form['form_autoreply_css']) ? $form['form_autoreply_css'] : '';
        $autoreply =  0;
        if (isset($form['form_autoreply']) && $form['form_autoreply'] ) {
            $autoreply = 1;
            $replymessage .=  $messagefields;
            $replymessage =  self::personaliseMessage($replymessage, $fields);
            $replymessage = self::cleanHTML($replymessage, $htmlcss);
            $smarty->assign('htmlmessage', $replymessage);
            $replymessage  = $smarty->fetch('jojo_contact_autoreply.tpl');
        }

        $htmlmessage =  $messagefields . '<p>' . nl2br(Jojo::emailFooter()) . '</p>';
        $htmlmessage = self::cleanHTML($htmlmessage, $htmlcss);
        $smarty->assign('htmlmessage', $htmlmessage);
        $htmlmessage  = $smarty->fetch('jojo_contact_autoreply.tpl');

        if (!count($errors)) {
            if (($formSend && Jojo::simpleMail($to_name, $to_email, $subject, $message, $from_name, $from_email, $htmlmessage, $sender_email)) || !$formSend) {

                /* success */
                $response = $formSuccessMessage;
                $smarty->assign('contactFrom_tracking_analytics', $formAnalytics);

                /* send a confirmation to the enquirer */
                if ($autoreply) {
                    Jojo::simpleMail($from_name, $from_email, $subject, $message, $to_name, $to_email, $replymessage);
                }

                /* send a copy to the webmaster */
               if ($form['form_webmaster_copy'] && $to_email != _WEBMASTERADDRESS) {
                    Jojo::simpleMail(_WEBMASTERNAME, _WEBMASTERADDRESS, $subject, $message, $from_name, $from_email);
                }
                
                /* store a copy of the message in the database*/
                $res = Jojo::insertQuery("INSERT INTO {formsubmission} (`form_id`,`submitted`,`success`,`to_name`,`to_email`,`subject`,`from_name`,`from_email`,`content`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($formID, time(), 1, $to_name, $to_email, $subject, $from_name, $from_email, serialize($fields)) );

                /* run success hook */
                Jojo::runHook('contact_form_success', array($formID, $res));
                /* add formSubmissionID for use in the template if ever needed (eg paypal form in response) */
                $smarty->assign('formSubmissionID', $res);

                $success = true;

            } else {
                $response = 'There was an error sending your message. This error has been logged, so we will attend to this problem as soon as we can.';
                $success = false;

                /* store a copy of the message in the database*/
                Jojo::insertQuery("INSERT INTO {formsubmission} (`form_id`,`submitted`,`success`,`to_name`,`to_email`,`subject`,`from_name`,`from_email`,`content`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($formID, time(), 0, $to_name, $to_email, $subject, $from_name, $from_email, serialize($fields)) );

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
            $response =  implode("<br />\n", $errors);
            $success = false;
            $smarty->assign('fields', $fields);
        }
        return array('sent'=>$success, 'responsemessage'=>$response);
    }

    function _getContent()
    {
        global $smarty;
        $content = array();
        $fields = array();

        /* Get current page id */
        $pageID = $this->page['pageid'];
        $form = Jojo::selectRow("SELECT form_id, form_thank_you_uri FROM {form} f WHERE f.form_page_id = ?", array($pageID));
        $formID = $form ? $form['form_id'] : '';
        $formhtml = self::getFormHtml($formID, $this->getCorrectUrl());

        $sent = false;
        if (isset($_POST['submit'])) {
            $response = $this->sendEnquiry();
            $smarty->assign('message', $response['responsemessage']);
            $sent = $response['sent'];
            /* redirect visitor to thank you page if one has been configured */
            if ($sent && $optionNewDatabaseMethod && !empty($form['form_thank_you_uri'])) {
                Jojo::redirect(_SITEURL.'/'.$form['form_thank_you_uri'], 302);
            }
        }
        $smarty->assign('sent', $sent);

        if (strpos($this->page['pg_body'], '[[contactform')===false) {
            $smarty->assign('content', $this->page['pg_body']);
            $content['content']    = $smarty->fetch('jojo_contact.tpl');
        } else {
            $formhtml = $smarty->fetch('jojo_contact.tpl');
            $content['content']    = str_replace('[[contactform]]', $formhtml, $this->page['pg_body']);
        }
        $content['javascript'] = $smarty->fetch('jojo_contact_js.tpl');

        return $content;
    }

    /* content filter to replace [[contactform:ID/name]] with html */
    function contentFilter($content)
    {
        if (strpos($content, '[[contactform:') === false) {
            return $content;
        }
        preg_match_all('/\[\[contactform: ?([^\]]*)\]\]/', $content, $matches);
        foreach($matches[1] as $k => $search) {
            /* convert name into ID */
            if (is_numeric($search)) {
                $id = $search;
            } else {
                $form = Jojo::selectRow("SELECT form_id, form_name FROM {form} f WHERE f.form_name = ?", array($search));
                $id = $form['form_id'];
            }
            if (isset($id)) {
                $formhtml = self::getFormHtml($id, $action='submit-form/');
                $content   = str_replace($matches[0][$k], $formhtml, $content);
            }
        }
        return $content;

    }

    /* get the html of the form from an ID */
    function getFormHtml($formID, $action=false)
    {
        global $smarty;
        $smarty->assign('content', '');
        $sent = (boolean)(isset($_SESSION['sendstatus']) || isset($_POST['submit']));
        $smarty->assign('message', (isset($_SESSION['sendstatus']) ? $_SESSION['sendstatus'] : ''));
        $formfields = Jojo::selectQuery("SELECT * FROM {form} f LEFT JOIN {formfield} ff ON ( ff.ff_form_id = f.form_id) WHERE f.form_id = ? ORDER BY ff_order", array($formID));
        $form = $formfields[0];
        $form['form_submit'] = isset($form['form_submit']) && $form['form_submit'] ? $form['form_submit'] : 'Submit';
        $form['form_success_message'] = $form['form_success_message'] ? $form['form_success_message'] : Jojo::getOption('contact_success_message', 'Your message was sent successfully.');
        $hideonsuccess = $form['form_hideonsuccess'];
        $formCaptcha = $form['form_captcha'];

        $fields = Jojo::applyFilter("formfields_first", array(), $formID);
        $f = count($fields);
        foreach ($formfields as $ff) {
            foreach ($ff as $k=>$v) {
                $key = str_replace('ff_', '', $k);
                $fields[$f][$key] = $v;
            }
            $fields[$f]['fieldsetid']       = isset($ff['ff_fieldset']) && $ff['ff_fieldset'] ? Jojo::cleanURL($ff['ff_fieldset']) : '';
            $fields[$f]['field']       = isset($ff['ff_fieldname']) && $ff['ff_fieldname'] ? Jojo::cleanURL($ff['ff_fieldname']) : Jojo::cleanURL($ff['ff_display']);
            $fields[$f]['options']     = explode("\r\n", $ff['ff_options']);
           $f++;
        }
        $fields = Jojo::applyFilter("formfields_last", $fields, $formID);

        /* Choice option */
        /* setup send to choices if it is set in options of the form */
        if ($form['form_choice'] && $form['form_choice_list']) {
            $toAddresses = array();
            $rawList = explode("\r\n", $form['form_choice_list']);
            foreach ($rawList as $k=>$l) {
                $parts = explode(",", $l);
                $toemail =  trim(array_pop($parts));
                $toname  =  trim(implode(',', $parts));
                $toAddresses[$k]['name'] = (htmlspecialchars($toname, ENT_COMPAT, 'UTF-8', false));
                $toAddresses[$k]['email'] = Jojo::cleanURL($toname);
            }
            $smarty->assign('toaddresses', $toAddresses);
        }

        $smarty->assign('posturl', ($action ? (strpos('http', $action)!==false ? _SITEURL . '/' : '') . $action : ''));
        $smarty->assign('form', $form);
        $smarty->assign('fields',$fields);
         /* Captcha Option */
        $smarty->assign('option_form_captcha', $formCaptcha);
        /* Hide form on success Option */
        $smarty->assign('hideonsuccess',$hideonsuccess);

        if ($sent) {
            $smarty->assign('message', ( isset($_SESSION['sendstatus']) && $_SESSION['sendstatus'] ? $formSuccessMessage : 'There was an error sending your message. This error has been logged, so we will attend to this problem as soon as we can.'));
            $smarty->assign('sent', $sent);
        }
        //reset send status
        unset($_SESSION['sendstatus']);

        $formhtml = $smarty->fetch('jojo_contact.tpl');
        return $formhtml;
    }

    /* Create the array for the name and email addresses on the send to options */
    function getToAddresses()
    {
        static $_toAddresses;
        /* Fetch options from database if we don't have them */
        if (!is_array($_toAddresses)) {
            $_toAddresses = array();
            $rawList = explode("\r\n", Jojo::getOption('contact_choice_list'));
            foreach ($rawList as $k=>$l) {
                $parts = explode(",", $l);
                $_toAddresses[$k]['name'] = trim(htmlspecialchars($parts[0], ENT_COMPAT, 'UTF-8', false));
                $_toAddresses[$k]['email'] = trim($_toAddresses[$k]['name'] . ', ' . $parts[1], ',');
            }
        }
        return $_toAddresses;
    }

    function cleanHTML($html, $css='')
    {
        // basic inline styling for supplied content
        $html = str_replace('<p>', '<p style="font-size:13px;' . $css . '">', $html);
        $html = str_replace('<td>', '<td style="font-size:13px;' . $css . '">', $html);
        $html= str_replace(array('<h1>', '<h2>', '<h3>'), '<p style="font-size: 16px;' . $css . '">', $html);
        $html = str_replace(array('<h4>','<h5>', '<h6>'), '<p style="font-size: 14px;' . $css . '">', $html);
        $html = str_replace(array('</h1>', '</h2>', '</h3>', '</h4>','</h5>', '</h6>'), '</p>', $html);
        return $html;
    }

    function personaliseMessage($html, $fields)
    {
        // filter message for personalisation by field display name eg [[From Name]]
        if (strpos($html, '[[')!==false) {
            preg_match_all('/\[\[([^\]]*)\]\]/', $html, $matches);
            foreach($matches[1] as $k => $search) {
                foreach($fields as $f) {
                    if ($f['display'] == $search) {
                        $html  = str_replace($matches[0][$k], $f['value'], $html);
                    }
                }
            }
        }
        return $html;
    }
}
