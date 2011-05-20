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

Jojo::updateQuery("UPDATE {page} SET pg_link='jojo_plugin_jojo_contact' WHERE pg_link='jojo_contact.php'");

/* Add "Edit Form Options" to Content menu */
$editFormsPage = 0;
$data = JOJO::selectQuery("SELECT * FROM {page} WHERE pg_url='admin/edit/form'");
if (count($data) == 0) {
    echo "Jojo_Plugin_Jojo_Contact: Adding <b>Forms</b> Page to Content menu<br />";
    $editFormsPage = Jojo::insertQuery("INSERT INTO {page} SET pg_title='Forms', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/form', pg_parent=". Jojo::clean($_ADMIN_CONTENT_ID)  .", pg_order=3");
}

/* Add "Edit Formfields" to Content menu */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_url='admin/edit/formfield'");
if (count($data) == 0) {
    echo "Jojo_Plugin_Jojo_Contact: Adding <b>Formfields</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Form Fields', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/formfield', pg_parent=". $editFormsPage .", pg_order=1");
}

// View Contact log
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link='Jojo_Plugin_Admin_Contactlog'")) {
    echo "Adding <b>Contact Log</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Contact Log', pg_link='Jojo_Plugin_Admin_Contactlog', pg_url = 'admin/contactlog', pg_parent = ?, pg_order=12", array($_ADMIN_REPORTS_ID));
}

/* Check if the installation uses the old contact plugin that uses the jojo_contact_fields.php file
 * If that is the case, transfer the file into the new database tables, transfer the options into the
 * database table  */

if(!Jojo::tableExists('form') || !Jojo::selectRow("SELECT * FROM {form}")){

    $pageIDQuery = Jojo::selectRow("SELECT pageid FROM `page` WHERE pg_link='jojo_plugin_jojo_contact'");
    $pageID = $pageIDQuery['pageid'];
     
    /* Read options and add a form table */
    echo "Jojo_Plugin_Jojo_Contact: Reading jojo_contact options and transfering into form table <br />";
    $insertQuery = "INSERT INTO {form} (
                        `form_name`,
                        `form_to`,
                        `form_page_id` ,
                        `form_captcha` ,
                        `form_tracking_code_analytics` ,
                        `form_tracking_code` ,
                        `form_success_message` ,
                        `form_webmaster_copy` ,
                        `form_choice` ,
                        `form_choice_list`
                        )
                        VALUES (
                        	'Contact Form',
                        	'". Jojo::getOption('contactaddress') ."',
                        	'" . $pageID . "',
                        	'". (Jojo::getOption('contactcaptcha') == 'yes' ? 1 : 0) ."',
                        	'". Jojo::getOption('contact_tracking_code_analytics') ."',
                        	'". Jojo::getOption('contact_tracking_code') ."',
                        	'". Jojo::getOption('contact_success_message') ."',
                        	'". (Jojo::getOption('contact_webmaster_copy') != 'no' ? 1 : 0) ."',
                        	'". (Jojo::getOption('contact_choice') == 'yes' ? 1 : 0) ."',
                        	'". Jojo::getOption('contact_choice_list') ."'
                        );";

    $formID = Jojo::insertQuery($insertQuery);

    /* Read fields from file and add to formfield table */
    echo "Jojo_Plugin_Jojo_Contact: Reading jojo_contact_fields.php and transfering into formfield table <br />";

    /* Fields from jojo_contact_fields.php in any plugin or theme */
    $file = array_pop(Jojo::listPlugins('jojo_contact_fields.php'));
    include $file;

    $orderCounter = 0;
    foreach ($fields as $field) {

        $insertQuery = "INSERT INTO {formfield} (
                                    `formfield_id` ,
                                    `ff_form_id` ,
                                    `ff_display` ,
                                    `ff_required` ,
                                    `ff_validation` ,
                                    `ff_type` ,
                                    `ff_size` ,
                                    `ff_value` ,
                                    `ff_options` ,
                                    `ff_rows` ,
                                    `ff_cols` ,
                                    `ff_description` ,
                                    `ff_is_email` ,
                                    `ff_is_name` ,
                                    `ff_order`
                                    )
                                    VALUES (
                                    NULL ,
                                    '". $formID ."' ,
                                    '". $field['display'] ."' ,
                                    '". ($field['required']?1:0) ."' ,
                                    '". $field['validation'] ."' ,
                                    '". $field['type'] ."' ,
                                    '". $field['size'] ."' ,
                                    '". $field['value'] ."' ,
                                    '". implode("\r\n", $field['options']) ."' ,
                                    '". $field['rows'] ."' ,
                                    '". $field['cols'] ."' ,
                                    '". $field['description'] ."' ,
                                    '". ($field['field'] == $from_email_field?1:0) ."',
                                    '". (in_array($field['field'], $from_name_fields)?1:0) ."',                                    
                                    '". $orderCounter ."'                                     
                                    );";  
        Jojo::insertQuery($insertQuery);
        $orderCounter++;
    }
}

/* If there is no form, create a standard contact form */
$data = Jojo::selectQuery("SELECT * FROM {form}");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Contact: Adding a standard contact form<br />";
    
    /* add contact page */
    $data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link='jojo_plugin_jojo_contact'");
    if (!count($data)) {
        echo "Jojo_Plugin_Jojo_Contact: Adding <b>Contact</b> Page to menu<br />";
        $pageID = Jojo::insertQuery("INSERT INTO {page} SET pg_title='Contact', pg_link='jojo_plugin_jojo_contact', pg_url='contact'");
    } else {
        $pageID = $data['pageid'];
    }

    $insertQuery = "INSERT INTO {form} (
                    `form_id` ,
                    `form_name` ,
                    `form_page_id` ,
                    `form_captcha` ,
                    `form_tracking_code_analytics` ,
                    `form_tracking_code` ,
                    `form_success_message` ,
                    `form_webmaster_copy` ,
                    `form_choice` ,
                    `form_choice_list`
                    )
                    VALUES (
                    	NULL ,
                    	'Contact Form',
                    	'" . $pageID . "',
                    	'1',
                    	'/contact-via-contact-form',
                    	'',
                    	'',
                    	'1',
                    	'0',
                    	''
                    );";
     
    $formID = Jojo::insertQuery($insertQuery);
    
    // Prepare formfields in array
    $fields[0]['display']     = 'Required Note';
    $fields[0]['required']    = false;
    $fields[0]['validation']  = '';
    $fields[0]['type']        = 'note';
    $fields[0]['size']        = 30;
    $fields[0]['value']       = '';
    $fields[0]['description'] = 'Required fields are marked *';  
    
    $fields[1]['display']     = 'First Name';
    $fields[1]['required']    = true;
    $fields[1]['validation']  = '';
    $fields[1]['type']        = 'text';
    $fields[1]['size']        = 30;
    $fields[1]['value']       = '';
    $fields[1]['description'] = '';
    $fields[1]['is_name']     = true;    
    
    $fields[2]['display']     = 'Last Name';
    $fields[2]['required']    = true;
    $fields[2]['validation']  = '';
    $fields[2]['type']        = 'text';
    $fields[2]['size']        = 30;
    $fields[2]['value']       = '';
    $fields[2]['description'] = '';
    $fields[2]['is_name']     = true;    
        
    $fields[3]['display']     = 'Email';
    $fields[3]['required']    = true;
    $fields[3]['validation']  = 'email';
    $fields[3]['type']        = 'text';
    $fields[3]['size']        = 30;
    $fields[3]['value']       = '';
    $fields[3]['description'] = '';
    $fields[3]['is_email']    = true;        
    
    $fields[4]['display']     = 'Message';
    $fields[4]['required']    = true;
    $fields[4]['validation']  = 'text';
    $fields[4]['type']        = 'textarea';
    $fields[4]['rows']        = '15';
    $fields[4]['cols']        = '29';
    $fields[4]['value']       = '';
    $fields[4]['description'] = '';
   
    // Add formfields in array to form
    $orderCounter = 0;
    foreach ($fields as $field) {
        $insertQuery = "INSERT INTO {formfield} (
                                    `ff_form_id` ,
                                    `ff_display` ,
                                    `ff_required` ,
                                    `ff_validation` ,
                                    `ff_type` ,
                                    `ff_size` ,
                                    `ff_value` ,
                                    `ff_options` ,
                                    `ff_rows` ,
                                    `ff_cols` ,
                                    `ff_description` ,
                                    `ff_is_email` ,
                                    `ff_is_name` ,
                                    `ff_order`
                                    )
                                    VALUES (
                                    '". $formID ."' ,
                                    '". $field['display'] ."' ,
                                    '". ($field['required']?1:0) ."' ,
                                    '". $field['validation'] ."' ,
                                    '". $field['type'] ."' ,
                                    '". $field['size'] ."' ,
                                    '". $field['value'] ."' ,
                                    '". implode("\r\n", $field['options']) ."' ,
                                    '". $field['rows'] ."' ,
                                    '". $field['cols'] ."' ,
                                    '". $field['description'] ."' ,
                                    '". ($field['is_email']?1:0) ."',
                                    '". ($field['is_name']?1:0) ."',                                    
                                    '". $orderCounter ."'                                     
                                    );";  
        Jojo::insertQuery($insertQuery);
        $orderCounter++;
    }
}