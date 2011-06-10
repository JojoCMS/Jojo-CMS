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

if(Jojo::tableExists('formfield') && !Jojo::selectRow("SELECT * FROM {formfield}")){

    $pageIDQuery = Jojo::selectRow("SELECT pageid FROM `page` WHERE pg_link='jojo_plugin_jojo_contact'");
    $pageID = $pageIDQuery['pageid'];
     
    /* process existing contact_choice_list if it exists */
    $contact_list = '';
    if (Jojo::getOption('contact_choice_list')!='') {
    	$raw_contact_list = Jojo::getOption('contact_choice_list');
    	$raw_contacts = explode(",", $raw_contact_list);
    	foreach ($raw_contacts as $i => $c) {
    		if ($i%2 == 0) {
    			$contact_list .= trim($c).",";
    		} else {
    			$contact_list .= trim($c)."\r\n";
    		}
    	}
    }
    
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
                        	'". addslashes(Jojo::getOption('contact_success_message')) ."',
                        	'". (Jojo::getOption('contact_webmaster_copy') != 'no' ? 1 : 0) ."',
                        	'". (Jojo::getOption('contact_choice') == 'yes' ? 1 : 0) ."',
                        	'". Jojo::clean($contact_list) ."'
                        );";

    $formID = Jojo::insertQuery($insertQuery);

    /* Read fields from file and add to formfield table */
    echo "Jojo_Plugin_Jojo_Contact: Reading jojo_contact_fields.php and transfering into formfield table <br />";

    /* Fields from jojo_contact_fields.php in any plugin or theme */
    $from_email_field = '';
    $from_name_fields = array();
    $file = array_pop(Jojo::listPlugins('jojo_contact_fields.php'));
    include $file;

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
                                    '". (isset($field['display']) ? $field['display'] : '' ) ."' ,
                                    '". (isset($field['required']) && $field['required'] ? 1 : 0 )."' ,
                                    '". (isset($field['validation']) ? $field['validation'] : '' ) ."' ,
                                    '". (isset($field['type']) ? $field['type'] : '' ) ."' ,
                                    '". (isset($field['size']) ? $field['size'] : 0 ) ."' ,
                                    '". (isset($field['value']) ? $field['value'] : '' )."' ,
                                    '". (isset($field['options']) ? implode("\r\n", $field['options']) : '' ) ."' ,
                                    '". (isset($field['rows']) ? $field['rows'] : 0 ) ."' ,
                                    '". (isset($field['cols']) ? $field['cols'] : 0 ) ."' ,
                                    '". (isset($field['description']) ? $field['description'] : '' ) ."' ,
                                    '". (isset($field['field']) && $field['field'] == $from_email_field ? 1 : 0 ) ."',
                                    '". (isset($field['field']) && in_array($field['field'], $from_name_fields)? 1 : 0 ) ."',                                    
                                    '". $orderCounter ."'                                     
                                    );";  
        Jojo::insertQuery($insertQuery);
        $orderCounter++;
    }
}
