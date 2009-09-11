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

$default_td['language'] = array(
        'td_name' => "language",
        'td_displayname' => "Language",
        'td_primarykey' => "languageid",
        'td_displayfield' => "IF(english_name!='',english_name,name)",
        'td_orderbyfields' => "english_name, name",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
    );

// Lang_htmllanguage Field
$default_fd['language']['lang_htmllanguage'] = array(
        'fd_name' => "Lang_htmllanguage",
        'fd_type' => "text",
        'fd_order' => "1",
    );

// ID Field
$default_fd['language']['languagetableid'] = array(
        'fd_name' => "ID",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID for this langauge - automatically assigned by the system",
        'fd_order' => "2",
    );

// Short Code Field
$default_fd['language']['languageid'] = array(
        'fd_name' => "Short Code",
        'fd_type' => "text",
        'fd_help' => "The two letter code for this language",
        'fd_order' => "3",
    );

// Long Code Field
$default_fd['language']['longcode'] = array(
        'fd_name' => "Long Code",
        'fd_type' => "text",
        'fd_help' => "Optional long code for this language",
        'fd_order' => "4",
    );

// Local Name Field
$default_fd['language']['name'] = array(
        'fd_name' => "Local Name",
        'fd_type' => "text",
        'fd_help' => "The localised name for this langauge",
        'fd_order' => "5",
    );

// English Name Field
$default_fd['language']['english_name'] = array(
        'fd_name' => "English Name",
        'fd_type' => "text",
        'fd_help' => "The name of the language in English",
        'fd_order' => "6",
    );

// Display Order Field
$default_fd['language']['displayorder'] = array(
        'fd_name' => "Display Order",
        'fd_type' => "integer",
        'fd_default' => "0",
        'fd_help' => "Order in which the language appears in the listing",
        'fd_order' => "7",
    );

// Character Set Field
$default_fd['language']['charset'] = array(
        'fd_name' => "Character Set",
        'fd_type' => "text",
        'fd_default' => "utf-8",
        'fd_order' => "8",
    );

// Text Direction Field
$default_fd['language']['direction'] = array(
        'fd_name' => "Text Direction",
        'fd_type' => "radio",
        'fd_options' => "ltr:Left To Right\nrtl:Right to Left",
        'fd_default' => "ltr",
        'fd_help' => "Text Direction",
        'fd_order' => "9",
    );

// Home Page Field
$default_fd['language']['home'] = array(
        'fd_name' => "Home Page",
        'fd_type' => "dblist",
        'fd_options' => "page",
        'fd_default' => "1",
        'fd_help' => "The home page for this langauge",
        'fd_order' => "10",
    );

// Root Page Field
$default_fd['language']['root'] = array(
        'fd_name' => "Root Page",
        'fd_type' => "dblist",
        'fd_options' => "page",
        'fd_default' => "0",
        'fd_help' => "The root page for this langauge, this is normal the parent of the home page",
        'fd_order' => "11",
    );

// Active Field
$default_fd['language']['active'] = array(
        'fd_name' => "Active",
        'fd_type' => "radio",
        'fd_options' => "yes:Yes\nno:No",
        'fd_default' => "no",
        'fd_help' => "Is this language active and in use?",
        'fd_order' => "12",
    );