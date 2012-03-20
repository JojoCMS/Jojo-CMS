<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007 Harvey Kane <code@ragepank.com>
 * Copyright 2007 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <code@gardyneholt.co.nz>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$table = 'form';
$query = "
    CREATE TABLE {form} (
        `form_id` int(11) NOT NULL auto_increment,
        `form_name` varchar(255) NOT NULL,
        `form_subject` varchar(255) NOT NULL,
        `form_page_id` int(11),
        `form_captcha` tinyint(1) NOT NULL default '1',
        `form_submit` varchar(255) NOT NULL,
        `form_tracking_code_analytics` text NOT NULL,
        `form_tracking_code` text NOT NULL,
        `form_success_message` text NOT NULL,
        `form_webmaster_copy` tinyint(1) NOT NULL default '1',
        `form_to` varchar(255) NOT NULL default '',
        `form_choice` tinyint(1) NOT NULL default '0',
        `form_choice_list` text NOT NULL,
        `form_autoreply` tinyint(1) NOT NULL default '0',
        `form_autoreply_body` text NULL,
        `form_autoreply_bodycode` text NULL,
        `form_action_url` varchar(255) NULL,
        `form_redirect_url` varchar(255) NULL,
        `form_autoreply_css` varchar(255) NOT NULL,
        `form_hideonsuccess` tinyint(1) NOT NULL default '0',
        `form_thank_you_uri` varchar(255) NOT NULL,
        PRIMARY KEY  (`form_id`)
        ) ENGINE=InnoDB  ;
    ";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Jojo_Plugin_Jojo_Contact: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Jojo_Plugin_Jojo_Contact: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);

$table = 'formfield';
$query = "
    CREATE TABLE {formfield} (
        `formfield_id` int(11) NOT NULL auto_increment,
        `ff_form_id` int(11) NOT NULL,        
        `ff_display` varchar(255) NOT NULL,
        `ff_required` tinyint(1) NOT NULL default '0',   
        `ff_validation` enum('email','url','text','integer') NOT NULL,
        `ff_type` ENUM('text','textarea','checkboxes','radio','select','list','emailwithconfirmation','hidden','heading','note') NOT NULL,
        `ff_size` int(11) NOT NULL,
        `ff_value` text NOT NULL,   
        `ff_options` text NOT NULL,                    
        `ff_rows` int(11) NOT NULL,
        `ff_cols` int(11) NOT NULL,  
        `ff_description` text NOT NULL,
        `ff_class` varchar(100) NOT NULL default '',   
        `ff_is_email` tinyint(1) NOT NULL default '0',
        `ff_is_name` tinyint(1) NOT NULL default '0',        
        `ff_showlabel` tinyint(1) NOT NULL default '1',        
        `ff_order` int(11) NOT NULL,                                       
        PRIMARY KEY  (`formfield_id`)
        ) ENGINE=InnoDB  AUTO_INCREMENT=1000;
    ";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Jojo_Plugin_Jojo_Contact: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Jojo_Plugin_Jojo_Contact: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);

$table = 'formsubmission';
$query = "
    CREATE TABLE {formsubmission} (
        `formsubmissionid` int(11) NOT NULL auto_increment,
        `form_id` int(11) NOT NULL,
        `submitted` int(11) NOT NULL,
        `success` tinyint(1) NOT NULL,
        `to_name` varchar(255) NOT NULL,
        `to_email` varchar(255) NOT NULL,
        `subject` varchar(255) NOT NULL,
        `from_name` varchar(255) NOT NULL,
        `from_email` varchar(255) NOT NULL,
        `content` text NOT NULL,
        PRIMARY KEY  (`formsubmissionid`)
        ) ENGINE=InnoDB  AUTO_INCREMENT=1000;
    ";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("Jojo_Plugin_Jojo_Contact: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("Jojo_Plugin_Jojo_Contact: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);

