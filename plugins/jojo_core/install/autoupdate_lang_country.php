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

$default_td['lang_country'] = array(
        'td_name' => "lang_country",
        'td_displayname' => "Site Sub-Section",
        'td_primarykey' => "lc_code",
        'td_displayfield' => "IF(lc_englishname!='',lc_englishname,lc_name)",
        'td_orderbyfields' => "displayorder, lc_englishname, lc_name",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
    );

// ID Field
$default_fd['lang_country']['lc_id'] = array(
        'fd_name' => "ID",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID for this section - automatically assigned by the system",
        'fd_order' => "1",
        'fd_tabname' => "Content",
    );

// Short Code Field
$default_fd['lang_country']['lc_code'] = array(
        'fd_name' => "Short Code",
        'fd_type' => "text",
        'fd_help' => "The url code for this section (eg: en, fr, ca, nz )",
        'fd_order' => "2",
        'fd_tabname' => "Content",
    );

// Long Code Field
$default_fd['lang_country']['lc_longcode'] = array(
        'fd_name' => "Long Code",
        'fd_type' => "text",
        'fd_help' => "Optional long url code for this section",
        'fd_order' => "3",
        'fd_tabname' => "Content",
    );

// Local Name Field
$default_fd['lang_country']['lc_name'] = array(
        'fd_name' => "Local Name",
        'fd_type' => "text",
        'fd_help' => "The localised name for this section (used for display)",
        'fd_order' => "4",
        'fd_tabname' => "Content",
    );

// English Name Field
$default_fd['lang_country']['lc_englishname'] = array(
        'fd_name' => "English Name",
        'fd_type' => "text",
        'fd_help' => "The name of the section in English",
        'fd_order' => "5",
        'fd_tabname' => "Content",
    );

// Home Page Field
$default_fd['lang_country']['lc_home'] = array(
        'fd_name' => "Home Page",
        'fd_type' => "dblist",
        'fd_options' => "page",
        'fd_default' => "1",
        'fd_help' => "The home page for this section",
        'fd_order' => "6",
        'fd_tabname' => "Content",
    );

// Root Page Field
$default_fd['lang_country']['lc_root'] = array(
        'fd_name' => "Root Page",
        'fd_type' => "dblist",
        'fd_options' => "page",
        'fd_help' => "The root page for this section, this is normally the parent of the home page",
        'fd_order' => "7",
        'fd_tabname' => "Content",
    );

// Display Order Field
$default_fd['lang_country']['displayorder'] = array(
        'fd_name' => "Display Order",
        'fd_type' => "integer",
        'fd_default' => "0",
        'fd_help' => "Order in which this section appears in the listing",
        'fd_order' => "8",
        'fd_tabname' => "Content",
    );

// Default Language Field
$default_fd['lang_country']['lc_defaultlang'] = array(
        'fd_name' => "Default Language",
        'fd_type' => "dblist",
        'fd_options' => "language",
        'fd_help' => "The default language to use for this section",
        'fd_order' => "9",
        'fd_tabname' => "Content",
    );

// Active Field
$default_fd['lang_country']['default'] = array(
        'fd_name' => "Default",
        'fd_type' => "yesno",
        'fd_options' => '',
        'fd_default' => 0,
        'fd_help' => "Is this the default section for the whole site? THERE CAN BE ONLY ONE",
        'fd_order' => "12",
        'fd_tabname' => "Content",
    );

// Active Field
$default_fd['lang_country']['active'] = array(
        'fd_name' => "Active",
        'fd_type' => "yesno",
        'fd_options' => '',
        'fd_default' => 0,
        'fd_help' => "Is this section active and in use?",
        'fd_order' => "12",
        'fd_tabname' => "Content",
    );
