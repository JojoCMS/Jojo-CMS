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
        $errors = array();
        $from_email = '';
        $from_name = '';
        $to_email = '';
        $formSubject = '';
        $formID;

        $files = Jojo::listPlugins('jojo_contact_fields.php');
        $optionNewDatabaseMethod = (boolean)(Jojo::tableExists('form'));

        /* If the "jojo_contact_fields.php" file is found, do it the old way */
        /* If there is no such file, get the form out of the database */
        if($optionNewDatabaseMethod){
            /* Get current page id */
            $pageID = $this->page['pageid'];

            /* Find the form that belongs to the current page id and get all the formfields that belong to that form */
            $formfields = Jojo::selectQuery("SELECT * FROM {form} f LEFT JOIN {formfield} ff ON ( ff.ff_form_id = f.form_id) WHERE f.form_page_id = ? ORDER BY ff_order", array($pageID));
            $form = $formfields[0];
            $formID = $form['form_id'];
            $formSubject = $form['form_subject'];
            $formTo = $form['form_to'];
            $formCaptcha = $form['form_captcha'];
            $formChoice = $form['form_choice'];
            $formChoiceOptions = $form['form_choice_list'];
            $formSuccessMessage = $form['form_success_message'];
            $formWebmasterCopy = $form['form_webmaster_copy'];

            $f = 0;
            foreach ($formfields as $ff) {                
                $f = $ff['ff_order'];  
                $fieldName = Jojo::cleanURL($ff['ff_display']);
                $fields[$f]['field']       = $fieldName;
                $fields[$f]['display']     = $ff['ff_display'];
                $fields[$f]['required']    = $ff['ff_required'];
                $fields[$f]['validation']  = $ff['ff_validation'];
                $fields[$f]['type']        = $ff['ff_type']; 
                $fields[$f]['size']        = $ff['ff_size'];              
                $fields[$f]['value']       = $ff['ff_value'];
                $fields[$f]['options']     = explode("\r\n", $ff['ff_options']);  
                $fields[$f]['rows']        = $ff['ff_rows'];
                $fields[$f]['cols']        = $ff['ff_cols'];                              
                $fields[$f]['description'] = $ff['ff_description'];
                $fields[$f]['is_email']    = $ff['ff_is_email'];
                $fields[$f]['is_name']     = $ff['ff_is_name'];                                  
                $fields[$f]['showlabel']     = $ff['ff_showlabel'];                                  
            }

            if ($formCaptcha){
                $captchacode = Jojo::getFormData('CAPTCHA','');
                if (!PhpCaptcha::Validate($captchacode)) {
                    $errors[] = 'Incorrect Spam Prevention Code entered';
                }
            }
            

            if ($formChoice && $formChoiceOptions) {
                print_r($to);
                $to       =  explode(",", $_POST['form_sendto']);
                $to_name  =  trim($to[0]);
                $to_email =  trim($to[1]);
            } else {
                $to_email       =  empty($formTo) ? Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS) : $formTo;
                $to_name       =  Jojo::either(_FROMNAME, _WEBMASTERNAME);
            }

        } else {
            /* Fields from jojo_contact_fields.php in any plugin or theme */
            include array_pop(Jojo::listPlugins('jojo_contact_fields.php'));

            if (Jojo::getOption('contactcaptcha') == 'yes') {
                $captchacode = Jojo::getFormData('CAPTCHA','');
                if (!PhpCaptcha::Validate($captchacode)) {
                    $errors[] = 'Incorrect Spam Prevention Code entered';
                }
            }

            if ((Jojo::getOption('contact_choice') == 'yes') && (Jojo::getOption('contact_choice_list') != '')) {
                $to       =  explode(",", $_POST['form_sendto']);
                $to_name  =  trim($to[0]);
                $to_email =  trim($to[1]);
            } else {
                $to_email       =  empty($to_email) ? Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS) : $to_email;
                $to_name       =  Jojo::either(_FROMNAME, _WEBMASTERNAME);
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
            } else {
                $field['value'] = ($field['type']!='heading') ? Util::getFormData('form_' . $field['field'], '') : $field['value'];
            }
            /* set the fromemail value if appropriate */
            
            if($optionNewDatabaseMethod){
                 if($field['is_email']){
                    $from_email = $field['value'];                     
                 }
                 if($field['is_name']){
                    $from_name .= $field['value'];
                    $from_name .= ' '; 
                 }                 
            } else {
                if (isset($from_email_field) && $field['field'] == $from_email_field) {
                    $from_email = $field['value'];
                }                
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
        $smarty->assign('fields', $fields);

        if(!$optionNewDatabaseMethod){
            $from_name = '';
            foreach ($from_name_fields as $fromfieldname) {
                foreach ($fields as $field) {
                    if ($fromfieldname == $field['field']) {
                        $from_name .= $field['value'];
                    } else {
                        $from_name .= ' ';
                    }
                }
            }            
        }     
        
        $from_name = empty($from_name) ? Jojo::getOption('sitetitle') : $from_name;
        $from_email = empty($from_email) ? Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS) : $from_email;
        $subject  = $formSubject ? $formSubject : 'Message from ' . Jojo::getOption('sitetitle') . ' website';
        $subject = mb_convert_encoding($subject, 'HTML-ENTITIES', 'UTF-8');

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
                $message .= ($f['showlabel'] ? $f['display'] . ': ' : '' ) . ($f['type'] == 'textarea' || $f['type'] == 'checkboxes' ? "\r\n" . $f['value'] . "\r\n\r\n" : $f['value'] . "\r\n" );
            }
        }
        $message .= Jojo::emailFooter();
        
        $htmlmessage = isset($form['form_autoreply_bodycode']) && $form['form_autoreply_bodycode'] ? $form['form_autoreply_bodycode'] :  '';
        $htmlcss = isset($form['form_autoreply_css']) ? $form['form_autoreply_css'] : '';
        $autoreply = isset($form['form_autoreply']) ? $form['form_autoreply'] : 0;
        
        foreach ($fields as $f) {
            if (isset($f['displayonly']) || $f['type'] == 'note') { continue; };
            if ($f['type'] == 'heading') { 
                $htmlmessage .=  '<h' . ($f['size'] ? $f['size'] : '3') . '>' . $f['value'] . '</h' . ($f['size'] ? $f['size'] : '3') . '>';
            } else {
                $htmlmessage .= ($f['showlabel'] ? '<p>' . $f['display'] . ': ' : '' ) . ($f['type'] == 'textarea' || $f['type'] == 'checkboxes' ? '<br>' . nl2br($f['value']) . '</p>' : $f['value'] . '</p>' );
            }
        }
        // basic inline styling for supplied content
        $htmlmessage = str_replace('<p>', '<p style="font-size:13px;' . $htmlcss . '">', $htmlmessage);
        $htmlmessage = str_replace('<td>', '<td style="font-size:13px;' . $htmlcss . '">', $htmlmessage);
        $htmlmessage= str_replace(array('<h1>', '<h2>', '<h3>'), '<p style="font-size: 16px;' . $htmlcss . '">', $htmlmessage);
        $htmlmessage = str_replace(array('<h4>','<h5>', '<h6>'), '<p style="font-size: 14px;' . $htmlcss . '">', $htmlmessage);
        $htmlmessage = str_replace(array('</h1>', '</h2>', '</h3>', '</h4>','</h5>', '</h6>'), '</p>', $htmlmessage);

        // filter message for personalisation by field display name eg [[From Name]]
        if (strpos($htmlmessage, '[[')!==false) {
            preg_match_all('/\[\[([^\]]*)\]\]/', $htmlmessage, $matches);
            foreach($matches[1] as $k => $search) {
                foreach($fields as $f) {
                    if ($f['display'] == $search) {
                        $htmlmessage  = str_replace($matches[0][$k], $f['value'], $htmlmessage);
                    }
                }
            }
        }
        
        // wrap content in template (only supports one template for all forms)
        $smarty->assign('htmlmessage', $htmlmessage);
        $smarty->assign('from_name', $from_name);
        $htmlmessage  = $smarty->fetch('jojo_contact_autoreply.tpl');
        // convert all internal links to external (no support for embedded images)
        $htmlmessage = Jojo::relative2absolute($htmlmessage, _SITEURL);
        
        if (!count($errors)) {
            if (Jojo::simpleMail($to_name, $to_email, $subject, $message, $from_name, $from_email, $htmlmessage)) {

                /* success */
                $successMessage = "";
                $successMessage = $optionNewDatabaseMethod ? $formSuccessMessage : Jojo::getOption('contact_success_message');
                $smarty->assign('message', Jojo::either($successMessage, 'Your message was sent successfully.'));
                
                /* send a confirmation to the enquirer */
                if ($autoreply) {
                    Jojo::simpleMail($from_name, $from_email, $subject, $message, $to_name, $to_email, $htmlmessage);
                }

                /* send a copy to the webmaster */
               if ($optionNewDatabaseMethod && $formWebmasterCopy && $to_email != _WEBMASTERADDRESS) {
                    Jojo::simpleMail(_WEBMASTERNAME, _WEBMASTERADDRESS, $subject, $message, $from_name, $from_email);
                } elseif (!$optionNewDatabaseMethod && Jojo::getOption('contact_webmaster_copy') != 'no' AND $to_email != _WEBMASTERADDRESS) { //note the !='no' which will ensure the default is to send the email (for installations where setup has not been run recently)
                    Jojo::simpleMail(_WEBMASTERNAME, _WEBMASTERADDRESS, $subject, $message, $from_name, $from_email);
                }
                 if ($optionNewDatabaseMethod) {
                    /* store a copy of the message in the database*/
                    Jojo::insertQuery("INSERT INTO {formsubmission} (`form_id`,`submitted`,`success`,`to_name`,`to_email`,`subject`,`from_name`,`from_email`,`content`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($formID, time(), 1, $to_name, $to_email, $subject, $from_name, $from_email, serialize($fields)) );
                } else {
                    /* log a copy of the message */
                    $log             = new Jojo_Eventlog();
                    $log->code       = 'enquiry';
                    $log->importance = 'normal';
                    $log->shortdesc  = 'Enquiry from ' . $from_name . ' (' . $from_email . ') to ' . $to_name . ' (' . $to_email . ')';
                    $log->desc       = $message;
                    $log->savetodb();
                    unset($log);
                }

                return true;

            } else {
                $smarty->assign('message', 'There was an error sending your message. This error has been logged, so we will attend to this problem as soon as we can.');

                if ($optionNewDatabaseMethod) {
                    /* store a copy of the message in the database*/
                    Jojo::insertQuery("INSERT INTO {formsubmission} (`form_id`,`submitted`,`success`,`to_name`,`to_email`,`subject`,`from_name`,`from_email`,`content`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array($formID, time(), 0, $to_name, $to_email, $subject, $from_name, $from_email, serialize($fields)) );
                }
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
        }
        return false;
    }

    function _getContent()
    {
        global $smarty;
        $content = array();
        $fields = array();
        $optionNewDatabaseMethod = (boolean)(Jojo::tableExists('form'));

        $smarty->assign('option_new_database_method', $optionNewDatabaseMethod);

        /* If the "jojo_contact_fields.php" file is found, do it the old way */
        /* If there is no such file, get the form out of the database */
        if($optionNewDatabaseMethod){
            
            /* Get current page id */ 
            $pageID = $this->page['pageid'];
            $formfields = Jojo::selectQuery("SELECT * FROM {form} f LEFT JOIN {formfield} ff ON ( ff.ff_form_id = f.form_id) WHERE f.form_page_id = ? ORDER BY ff_order", array($pageID));
            $form = $formfields[0];
            $formID = $form['form_id'];
            $formTo = $form['form_to'];
            $formCaptcha = $form['form_captcha'];
            $formChoice = $form['form_choice'];
            $formChoiceOptions = $form['form_choice_list'];
            $formSuccessMessage = $form['form_success_message'];
            $formWebmasterCopy = $form['form_webmaster_copy'];
            $hideonsuccess = $form['form_hideonsuccess'];

            $f = 0;
            foreach ($formfields as $ff) {                
                $fieldName = Jojo::cleanURL($ff['ff_display']);
                $fields[$f]['field']       = $fieldName;
                $fields[$f]['display']     = $ff['ff_display'];
                $fields[$f]['required']    = $ff['ff_required'];
                $fields[$f]['validation']  = $ff['ff_validation'];
                $fields[$f]['type']        = $ff['ff_type'];
                $fields[$f]['size']        = $ff['ff_size'];
                $fields[$f]['value']       = $ff['ff_value'];
                $fields[$f]['options']     = explode("\r\n", $ff['ff_options']);
                $fields[$f]['rows']        = $ff['ff_rows'];
                $fields[$f]['cols']        = $ff['ff_cols'];
                $fields[$f]['description'] = $ff['ff_description'];
                $fields[$f]['showlabel'] = $ff['ff_showlabel'];
               $f++;
            }

            /* Choice option */
            /* setup send to choices if it is set in options of the form */
            if ($formChoice && $formChoiceOptions) {
                $toAddresses = array();
                $rawList = explode("\r\n", $formChoiceOptions);
                foreach ($rawList as $k=>$l) {
                    $parts = explode(",", $l);
                    $toAddresses[$k]['name'] = trim(htmlspecialchars($parts[0], ENT_COMPAT, 'UTF-8', false));
                    $toAddresses[$k]['email'] = trim($parts[1], ' ,');
                }
                $smarty->assign('toaddresses', $toAddresses);
            }

            /* Captcha Option */
            $smarty->assign('option_form_captcha', $formCaptcha);
            
            /* Hide form on success Option */
            $smarty->assign('hideonsuccess',$hideonsuccess);
            
        } else {
            
            /* Fields from jojo_contact_fields.php in any plugin or theme */
            include array_pop(Jojo::listPlugins('jojo_contact_fields.php'));

            /* setup send to choices if it is set in options */
            if ((Jojo::getOption('contact_choice') == 'yes') && (Jojo::getOption('contact_choice_list') != '')) {
                $toaddresses = self::getToAddresses();
                $smarty->assign('toaddresses',$toaddresses);
            }  
        }

        $smarty->assign('posturl', $this->getCorrectUrl());
        $smarty->assign('fields',$fields);

        $sent = false;
        if (isset($_POST['submit'])) {
            $sent = $this->sendEnquiry();
        }
        $smarty->assign('sent', $sent);
        if (strpos($this->page['pg_body'], '[[contactform]]')===false) {
            $smarty->assign('content', $this->page['pg_body']);
            $content['content']    = $smarty->fetch('jojo_contact.tpl');
        } else {
            $formhtml = $smarty->fetch('jojo_contact.tpl');
            $content['content']    = str_replace('[[contactform]]', $formhtml, $this->page['pg_body']);
        }
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
            $rawList = explode("\r\n", Jojo::getOption('contact_choice_list'));
            foreach ($rawList as $k=>$l) {
                $parts = explode(",", $l);
                $_toAddresses[$k]['name'] = trim(htmlspecialchars($parts[0], ENT_COMPAT, 'UTF-8', false));
                $_toAddresses[$k]['email'] = trim($_toAddresses[$k]['name'] . ', ' . $parts[1], ',');
            }
        }
        return $_toAddresses;
    }

}
