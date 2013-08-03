<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$default_td['user'] = array(
        'td_name' => "user",
        'td_primarykey' => "userid",
        'td_displayfield' => "if(CHAR_LENGTH(us_login) > 0, us_login, us_email)",
        'td_rolloverfield' => "CONCAT(us_firstname,' ',us_lastname)",
        'td_orderbyfields' => "us_login",
        'td_topsubmit' => "yes",
        'td_menutype' => "list",
    );


/* User Tab */

// Userid Field
$default_fd['user']['userid'] = array(
        'fd_name' => "Userid",
        'fd_type' => "hidden",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => "1",
        'fd_tabname' => "Base",
    );

// Login Field
$default_fd['user']['us_login'] = array(
        'fd_name' => "Login",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "20",
        'fd_help' => "Username for logging into the system",
        'fd_order' => "2",
        'fd_tabname' => "Base",
        'fd_flags' => "REGISTER",
    );

// First Name Field
$default_fd['user']['us_firstname'] = array(
        'fd_name' => "First Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "20",
        'fd_order' => "3",
        'fd_tabname' => "Base",
        'fd_flags' => "REGISTER,PROFILE,PRIVACY",
    );

// Last Name Field
$default_fd['user']['us_lastname'] = array(
        'fd_name' => "Last Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "20",
        'fd_order' => "4",
        'fd_tabname' => "Base",
        'fd_flags' => "REGISTER,PROFILE,PRIVACY",
    );

// Email Field
$default_fd['user']['us_email'] = array(
        'fd_name' => "Email",
        'fd_type' => "email",
        'fd_required' => "yes",
        'fd_order' => "5",
        'fd_tabname' => "Base",
        'fd_flags' => "REGISTER,PROFILE,PRIVACY,PRIVATE",
    );

// Password Field
$default_fd['user']['us_password'] = array(
        'fd_name' => "Password",
        'fd_type' => "password",
        'fd_options' => "us_salt",
        'fd_required' => "yes",
        'fd_size' => "30",
        'fd_help' => "Password must be at least 8 characters and contain at least 1 number",
        'fd_order' => "6",
        'fd_tabname' => "Base",
        'fd_flags' => "REGISTER",
    );

// Password Reminder Field
$default_fd['user']['us_reminder'] = array(
        'fd_name' => "Password Reminder",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_order' => "7",
        'fd_tabname' => "Base",
    );

// Timezone Field
$default_fd['user']['us_timezone'] = array(
        'fd_name' => "Timezone",
        'fd_type' => "integer",
        'fd_default' => "12",
        'fd_size' => "5",
        'fd_help' => "The timezone offset for this user (NZ is 12)",
        'fd_order' => "8",
        'fd_tabname' => "Base",
        'fd_units' => "GMT offset",
    );

// Salt Field
$default_fd['user']['us_salt'] = array(
        'fd_name' => "Salt",
        'fd_type' => "hidden",
        'fd_order' => "9",
        'fd_tabname' => "Base",
    );

// Lastfailure Field
$default_fd['user']['us_lastfailure'] = array(
        'fd_name' => "Lastfailure",
        'fd_type' => "hidden",
        'fd_default' => "0000-00-00 00:00:00",
        'fd_order' => "10",
        'fd_tabname' => "Technical",
    );

// Groups Field
$default_fd['user']['us_groups'] = array(
        'fd_name' => "Groups",
        'fd_type' => "many2many",
        'fd_order' => "11",
        'fd_tabname' => "Base",
        'fd_m2m_linktable' => "usergroup_membership",
        'fd_m2m_linkitemid' => "userid",
        'fd_m2m_linkcatid' => "groupid",
        'fd_m2m_cattable' => "usergroups",
    );

// Failures Field
$default_fd['user']['us_failures'] = array(
        'fd_name' => "Failures",
        'fd_type' => "hidden",
        'fd_default' => "0",
        'fd_order' => "12",
        'fd_tabname' => "Technical",
    );

// Locked Field
$default_fd['user']['us_locked'] = array(
        'fd_name' => "Locked",
        'fd_type' => "radio",
        'fd_options' => "1:Yes\n0:No",
        'fd_default' => "0",
        'fd_order' => "13",
        'fd_tabname' => "Technical",
    );

// Reset Field
$default_fd['user']['us_reset'] = array(
        'fd_name' => "Reset",
        'fd_type' => "hidden",
        'fd_order' => "14",
        'fd_tabname' => "Technical",
    );

