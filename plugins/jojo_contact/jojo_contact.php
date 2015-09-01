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

    public static function sendEnquiry($formID=false)
    {
        global $smarty;
        /* Check for form injection attempts */
        Jojo::noFormInjection();

        $fields = array();
        $attachments = array();
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
            //$pageID = $this->page['pageid'];
            global $page;
            $pageID = $page->page['pageid'];
            $formfields = Jojo::selectQuery("SELECT * FROM {form} f LEFT JOIN {formfield} ff ON ( ff.ff_form_id = f.form_id) WHERE f.form_page_id = ? ORDER BY ff_order", array($pageID));
        }


        /* check if it's spammy before doing anything else */
        $errors = self::isSpam('', $formfields[0]['form_captcha']);

        /* Find the form that belongs to the current page id and get all the formfields that belong to that form */
        $form = $formfields[0];
        $formID = $form['form_id'];
        $formSend = isset($form['form_send']) ? $form['form_send'] : 1;
        $formName = $form['form_name'];
        $formSubject = $form['form_subject'];
        $formAnalytics = $form['form_tracking_code_analytics'];
        $formTrackingcode = $form['form_tracking_code'];
        $formSuccessMessage = $form['form_success_message'] ?: ( Jojo::getOption('contact_success_message', '') ?: 'Your message was sent successfully.' );

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

        $to_email       =  empty($form['form_to']) ? Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS) : $form['form_to'];
        $to_name       =  Jojo::either(_FROMNAME, _WEBMASTERNAME);

        if ($form['form_choice'] && $form['form_choice_list'] && isset($_POST['form_sendto'])) {
            $formchoices = Jojo::ta2kv($form['form_choice_list'], ',');
            foreach ($formchoices as $k=>$v) {
                if (Jojo::cleanURL($k)==$_POST['form_sendto']) {
                    $to_email =  $v;
                    $to_name  =  str_replace('#', '', $k);
                    break;
                }
            }
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
            } elseif (Jojo::getFormData('form_' . $field['field'], '')) {
                $field['value'] = ($field['type']!='heading') ? Jojo::getFormData('form_' . $field['field'], '') : $field['value'];
            } else {
                $field['value'] = ($field['type']!='heading') ? Jojo::getFormData($field['field'], '') : $field['display'];
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
            if ($field['required'] && empty($field['value']) && $field['value']!=='0') {
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
                        if (!is_numeric($field['value'])) {$errors[] = $field['display'] . ' is not a number';}
                        break;
                }
            }

            /* if field is confirmation field then need to check both fields match */
            if($field['type'] == 'emailwithconfirmation') {
                $confirmation = $_POST[$field['field'] . '_confirmation'];
                if($field['value'] != $confirmation) {
                    $errors[] = $field['display'] . ' and confirmation email fields must match';
                }
            }
            /* if field is file upload field then need to check for a file and transfer it to the folder */
            if ($field['type'] == 'upload' || $field['type'] == 'privateupload' || $field['type'] == 'attachment') {
                $field['filelink'] = '';
                if (!isset($_FILES["FILE_".$field['field']])) continue;
                $file = $_FILES["FILE_".$field['field']];
                $uploadresponse = self::upload($file, $field, $form);
                if ($uploadresponse['errors']) $errors[] = $uploadresponse['errors'];
                if ($field['type'] == 'attachment') {
                    $field['value'] = $uploadresponse['filename'];
                    $attachments[] = $uploadresponse['filepath'];
               } else {
                    $field['filelink'] = $uploadresponse['filelink'];
                }
            }
       }

       if(!$errors){
            /* run further validation hook */
            $validationReturn = Jojo::runHook('contact_form_validation_success', array($errors, $fields));
            $errors = $validationReturn[0];
            /* filter to set destination dynamically based on submitted form content */
            $to_email       = Jojo::applyFilter("form_destination", $to_email, $fields);
        }

        unset($field);

        $from_name = $from_name ?: Jojo::getOption('sitetitle');
        $from_email = $from_email ?: Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS);
        $subject  = $formSubject ?: 'Message from the ' . $formName . ' form';
        $subject = mb_convert_encoding($subject, 'HTML-ENTITIES', 'UTF-8');
        $smarty->assign('subject', $subject);

        $message  = '';
        foreach ($fields as $f) {
            if (isset($f['displayonly']) && $f['displayonly']==1) {
                continue;
            } elseif ($f['type'] == 'note') {
                continue;
            } elseif ($f['type'] == 'heading') {
                $message .=  "\r\n<b>" . $f['value'] . "</b>\r\n";
            } elseif ($f['type'] == 'upload' || $f['type'] == 'privateupload') {
                $message .= $f['display'] . ($f['filelink'] ? ' ' . _SITEURL . '/downloads' . $f['filelink'] : '') . "\r\n";
            } else {
                $message .= (isset($f['showlabel']) && $f['showlabel'] ? $f['display'] . ': ' : '' ) . ($f['type'] == 'textarea' || $f['type'] == 'checkboxes' ? "\r\n" . $f['value'] . "\r\n\r\n" : $f['value'] . "\r\n" );
            }
        }
        $message .= Jojo::emailFooter();

        /* check if message is link spam */
        self::isSpam($message);

        $messagefields = '';
        foreach ($fields as $f) {
            if ((isset($f['displayonly']) && $f['displayonly']==1) || $f['type'] == 'note') { continue; };
            if ($f['type'] == 'heading') {
                $messagefields .=  '<h' . ($f['size'] ?: '3') . '>' . $f['value'] . '</h' . ($f['size'] ?: '3') . '>';
            } elseif ($f['type'] == 'upload' || $f['type'] == 'privateupload') {
                $messagefields .= '<p>' . $f['display'] . ($f['filelink'] ? ' <a href="' . _SITEURL . '/downloads' . $f['filelink'] . '">' . _SITEURL . '/downloads' . $f['filelink'] .'</a>' : '') .'</p>';
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
        $css = Jojo::getOption('css-email', '');
        $autoreply =  0;
        if (isset($form['form_autoreply']) && $form['form_autoreply'] ) {
            $autoreply = 1;
            $replymessage .=  $messagefields;
            $replymessage =  self::personaliseMessage($replymessage, $fields);
            $smarty->assign('htmlmessage', $replymessage);
            $replymessage  = $smarty->fetch('jojo_contact_autoreply.tpl');
            $replymessage = $css ? Jojo::inlineStyle($replymessage, $css) : $replymessage;
        }

        $htmlmessage =  $messagefields . '<p>' . nl2br(Jojo::emailFooter()) . '</p>';
        $smarty->assign('htmlmessage', $htmlmessage);
        $htmlmessage  = $smarty->fetch('email.tpl');
        $htmlmessage = $css ? Jojo::inlineStyle($htmlmessage, $css) : $htmlmessage;
        $res = false;

        if (!$errors) {
            if (($formSend && Jojo::simpleMail($to_name, $to_email, $subject, $message, $from_name, $from_email, $htmlmessage, $from_name . '<' . $sender_email . '>', $attachments)) || !$formSend) {

                /* success */
                $responsemessage = $formSuccessMessage;
                $smarty->assign('contactFrom_tracking_analytics', $formAnalytics);

                /* send a confirmation to the enquirer */
                if ($autoreply && $from_email!=$sender_email) {
                    Jojo::simpleMail($from_name, $from_email, $subject, $message, $to_name, $to_email, $replymessage);
                }

                /* send a copy to the main email as well the multi-choice one (if option is set) */
               if ($form['form_choice'] && $form['form_choice_list'] && isset($_POST['form_sendto']) && isset($form['form_choice_cc']) && $form['form_choice_cc'] && $form['form_to'] && $to_email != $form['form_to']) {
                    Jojo::simpleMail(Jojo::either(_FROMNAME, _WEBMASTERNAME), $form['form_to'], $subject, $message, $from_name, $from_email, $htmlmessage, $from_name . '<' . $sender_email . '>', $attachments);
                }

                /* send a copy to the webmaster */
               if ($form['form_webmaster_copy'] && $to_email != _WEBMASTERADDRESS) {
                    Jojo::simpleMail(_WEBMASTERNAME, _WEBMASTERADDRESS, $subject, $message, $from_name, $from_email, $htmlmessage, $from_name . '<' . $sender_email . '>', $attachments);
                }

                /* store a copy of the message in the database*/
                $res = Jojo::insertQuery("INSERT INTO {formsubmission} (`form_id`,`submitted`,`success`,`to_name`,`to_email`,`subject`,`from_name`,`from_email`,`content`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($formID, time(), 1, $to_name, $to_email, $subject, $from_name, $from_email, serialize($fields)) );

                /* add formSubmissionID for use in the template if ever needed (eg paypal form in response) */
                $smarty->assign('formSubmissionID', $res);

                $success = true;

            } else {
                $responsemessage = 'There was an error sending your message. This error has been logged, so we will attend to this problem as soon as we can.';
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
            $responsemessage =  implode("<br />\n", $errors);
            $success = false;
            $smarty->assign('fields', $fields);
        }
        $_SESSION['sendstatus'] = $responsemessage;
        /* run success if we are successful hook */
        if ($success) {
            Jojo::runHook('contact_form_success', array($formID, $res));
        }
        return array('id'=>'form' . $formID, 'sent'=>$success, 'responsemessage'=>$responsemessage, 'hideonsuccess'=>$form['form_hideonsuccess']);
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

        $sent = false;
        if (isset($_POST['contactsubmit'])) {
            $response = $this->sendEnquiry($formID);
            $sent = $response['sent'];
            /* redirect visitor to thank you page if one has been configured */
            if ($sent && !empty($form['form_thank_you_uri'])) {
                Jojo::redirect(_SITEURL . '/' . $form['form_thank_you_uri'], 302);
            }
        }
        $smarty->assign('sent', $sent);

        $formhtml = self::getFormHtml($formID, $this->getCorrectUrl());
        
        if (strpos($this->page['pg_body'], '[[contactform]]')===false) {
            $content['content']  = $this->page['pg_body'] . $formhtml;
        } else {
            $this->page['pg_body'] = str_replace(array('<p>[[contactform]]</p>','<p>[[contactform]]&nbsp;</p>'), '[[contactform]]', $this->page['pg_body']);
            $content['content']    = str_replace('[[contactform]]', $formhtml, $this->page['pg_body']);
        }
        $content['javascript'] = $smarty->fetch('jojo_contact_js.tpl');

        return $content;
    }

    /* content filter to replace [[contactform:ID/name]] with html */
    public static function contentFilter($content)
    {

        if (strpos($content, '[[contactform:') === false) {
            return $content;
        }
        preg_match_all('/\[\[contactform: ?([^\]]*)\]\]/', $content, $matches);
        foreach($matches[1] as $k => $search) {
            /* convert name into ID */
            if (is_numeric(trim($search))) {
                $id = $search;
            } else {
                $form = Jojo::selectRow("SELECT form_id, form_name FROM {form} f WHERE f.form_name = ?", array(trim($search)));
                $id = $form ? $form['form_id'] : '';
            }
            if (isset($id) && $id) {
                $formhtml = self::getFormHtml($id, $action='submit-form/', $js=true);
                $content   = str_replace($matches[0][$k], $formhtml, $content);
            }
        }
        return $content;
    }

    /* get the html of the form from an ID */
    public static function getFormHtml($formID, $action=false, $js=false)
    {
        global $smarty;
        $smarty->assign('content', '');
        $sent = (boolean)(isset($_SESSION['sendstatus']) || isset($_POST['contactsubmit']));
        $smarty->assign('message', (isset($_SESSION['sendstatus']) ? $_SESSION['sendstatus'] : ''));
        $formfields = Jojo::selectQuery("SELECT * FROM {form} f LEFT JOIN {formfield} ff ON ( ff.ff_form_id = f.form_id) WHERE f.form_id = ? ORDER BY ff_order", array($formID));
        $form = $formfields[0];
        $form['form_submit'] = isset($form['form_submit']) && $form['form_submit'] ? $form['form_submit'] : 'Submit';
        $form['form_success_message'] = $form['form_success_message'] ?: Jojo::getOption('contact_success_message', 'Your message was sent successfully.');
        $hideonsuccess = $form['form_hideonsuccess'];
        $formCaptcha = $form['form_captcha'];

        $fields = Jojo::applyFilter("formfields_first", array(), $formID);
        $f = count($fields);
        foreach ($formfields as $ff) {
            foreach ($ff as $k=>$v) {
                $key = str_replace('ff_', '', $k);
                $fields[$f][$key] = nl2br($v);
            }
            $fields[$f]['fieldsetid']   = isset($ff['ff_fieldset']) && $ff['ff_fieldset'] ? Jojo::cleanURL($ff['ff_fieldset']) : '';
            $fields[$f]['field']        = isset($ff['ff_fieldname']) && $ff['ff_fieldname'] ? Jojo::cleanURL($ff['ff_fieldname']) : Jojo::cleanURL($ff['ff_display']);
            $fields[$f]['options']      = explode("\r\n", $ff['ff_options']);
            $fields[$f]['prependvalue'] = isset($ff['ff_prependvalue']) ? $ff['ff_prependvalue'] : '';
            $fields[$f]['appendvalue']  = isset($ff['ff_appendvalue']) ? $ff['ff_appendvalue'] : '';
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
            $smarty->assign('form_choice_multiple', (boolean)(isset($form['form_choice_multiple']) && $form['form_choice_multiple']));
        }

        $smarty->assign('posturl', ($action ? (strpos('http', $action)!==false ? _SITEURL . '/' : '') . $action : ''));
        $smarty->assign('form', $form);
        $smarty->assign('fields',$fields);
         /* Captcha Option */
        $smarty->assign('option_form_captcha', $formCaptcha);
        /* Hide form on success Option */
        $smarty->assign('hideonsuccess',$hideonsuccess);
        /* Use Anytime datepicker for date fields if option set */
        $smarty->assign('anytime', (boolean)(Jojo::getOption('jquery_useanytime', 'no')=='yes'));
        if ($sent) {
            $smarty->assign('message', ( isset($_SESSION['sendstatus']) && $_SESSION['sendstatus'] ? $_SESSION['sendstatus'] : 'There was an error sending your message. This error has been logged, so we will attend to this problem as soon as we can.'));
            $smarty->assign('sent', $sent);
            Jojo::noCache(true);
        }
        //reset send status
        unset($_SESSION['sendstatus']);

        $formhtml = $smarty->fetch('jojo_contact.tpl');
        if ($js) {
            $js =  '<script type="text/javascript">' . $smarty->fetch('jojo_contact_js.tpl') . '</script>'."\n";
            $formhtml = $formhtml . "\n" . $js;
        }
        return $formhtml;
    }

    /* Create the array for the name and email addresses on the send to options */
    static function getToAddresses()
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

    static function personaliseMessage($html, $fields)
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

    public static function isSpam($content=false, $captcha=false) {
        
        if ($content && substr_count($content, 'http://')>Jojo::getOption('spam_links', 3)) {
            header("HTTP/1.0 404 Not Found");
            ob_end_flush(); // Send the output and turn off output buffering
            exit;
        }
        /* Check CAPTCHA is entered correctly */
        if ($captcha) {
            $errors=array();
            if (Jojo::getOption('captcha_recaptcha', 'no')=='yes') {
               $captcharesponse = Jojo::getFormData('g-recaptcha-response','');
               $secretkey = Jojo::getOption('captcha_secretkey', '');
               $url = 'https://www.google.com/recaptcha/api/siteverify';
                $data = array('secret' => $secretkey, 'response' => $captcharesponse);
                // use key 'http' even if you send the request to https://...
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'POST',
                        'content' => http_build_query($data),
                    ),
                );
                $context  = stream_context_create($options);
                $result = json_decode(file_get_contents($url, false, $context), true);
                /* failures on reCaptcha can 404 because real users will have feedback on success before submitting but bots won't */
               if (!$result['success']) {
                    header("HTTP/1.0 404 Not Found.");
                    ob_end_flush(); // Send the output and turn off output buffering
                    exit;
                }
            } else {
                $captchacode = Jojo::getFormData('CAPTCHA','');
                if (!PhpCaptcha::Validate($captchacode)) {
                    $errors[] = 'Incorrect Spam Prevention Code entered';
                    return $errors;
                }
            }
        }
        return false;

    }


     public static function footjs()
     {

    }

     private static function upload($file, $field, $form)
     {
        $filename = str_replace(' ', '_', str_replace(array('?','&',"'",',','[',']'), '', stripslashes($file['name'])));
        $tmpfilename = $file['tmp_name'];
        $error = '';
       /* Check error codes */
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE: //1
                $error = 'The uploaded file exceeds the maximum size allowed in PHP.INI';
                break;
            case UPLOAD_ERR_FORM_SIZE: //2
                $error = 'The uploaded file exceeds the maximum size allowed (' . 1000 * Jojo::getOption('max_fileupload_size','5000') .')';
                break;
            case UPLOAD_ERR_PARTIAL: //3
                $error = 'The file has only been partially uploaded. There may have been an error in transfer, or the server may be having technical problems.';
                break;

            case UPLOAD_ERR_NO_FILE: //4 - this is only a problem if it's a required field
                //remember, a required field only needs to be set the first time, perhaps its better to check this somewhere else
                break;

            case 6: // UPLOAD_ERR_NO_TMP_DIR - for some odd reason the constant wont work
                $error = 'There is no temporary folder on the server - please contact the webmaster ('._WEBMASTERADDRESS.')';
                break;

            case UPLOAD_ERR_OK: //0
                /* check for empty file */
                if($file['size'] == 0) {
                    $error = 'The uploaded file is empty.';
                    break;
                }

                /* ensure file is uploaded correctly */
                if (!is_uploaded_file($tmpfilename)) {
                    /* improve this code when you have time - will work, but needs fleshing out */
                    $error = 'Possible hacking attempt. Script will now halt.';
                    die($error);
                }

                $folder = $field['type'] == 'privateupload' || $field['type'] == 'attachment' ? 'private/' : '';
                $folder .= $form['form_uploadfolder'] ?: $form['form_id'];
                /* All appears good, so prepare to move file to final resting place */
                $destination = _DOWNLOADDIR . '/uploads/' . $folder . '/' . basename($filename);

                /* create the folder if it does not already exist */
                Jojo::RecursiveMkdir(dirname($destination));

                /* Ensure file does not already exist on server, rename if it does */
                $newname = '';
                $i = 1;
                $newMD5 = md5_file($tmpfilename);
                while (file_exists($destination) && $newMD5 != md5_file($destination)){
                    $newname = ++$i . "_" . $filename;
                    $destination = _DOWNLOADDIR . "/uploads/" . $folder . '/' . $newname;
                }

                /* move to final location */
                if (!(file_exists($destination) || move_uploaded_file($tmpfilename, $destination))) {
                    $error = "Possible hacking attempt. Script will now halt.";
                    die($error);
                }
                break;
            default:
                /* this code shouldn't execute - 0 should be the default */
                $error = 'An unknown error occurred - please contact the webmaster ('._WEBMASTERADDRESS.')';
        }
        $reponse['errors'] = $error;
        $reponse['filepath'] = $destination;
        $reponse['filelink'] = str_replace(_DOWNLOADDIR, '', $destination);
        $reponse['filename'] = $newname ? basename($newname) : basename($filename);
        return $reponse;
    }

    /* block private uploads from being downloaded without login */
    public static function downloadFile($filename)
    {
        global $_USERGROUPS;
        if (strpos($filename, "/uploads/private") !== false && !in_array('admin', $_USERGROUPS)) {
            header("HTTP/1.0 404 Not Found", true, 404);
            echo "You must be logged in as an administrator to download this file.";
            exit;
        }
        return true;
    }
}
