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
        'td_displayname' => "Language/Country",
        'td_primarykey' => "lc_code",
        'td_displayfield' => "IF(lc_englishname!='',lc_englishname,lc_name)",
        'td_orderbyfields' => "lc_englishname, lc_name",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
    );

// ID Field
$default_fd['lang_country']['lc_id'] = array(
        'fd_name' => "ID",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID for this language/country - automatically assigned by the system",
        'fd_order' => "1",
    );

// Short Code Field
$default_fd['lang_country']['lc_code'] = array(
        'fd_name' => "Short Code",
        'fd_type' => "text",
        'fd_help' => "The two letter code for this language/country",
        'fd_order' => "2",
    );

// Long Code Field
$default_fd['lang_country']['lc_longcode'] = array(
        'fd_name' => "Long Code",
        'fd_type' => "text",
        'fd_help' => "Optional long code for this language/country",
        'fd_order' => "3",
    );

// Local Name Field
$default_fd['lang_country']['lc_name'] = array(
        'fd_name' => "Local Name",
        'fd_type' => "text",
        'fd_help' => "The localised name for this langauge/country",
        'fd_order' => "4",
    );

// English Name Field
$default_fd['lang_country']['lc_englishname'] = array(
        'fd_name' => "English Name",
        'fd_type' => "text",
        'fd_help' => "The name of the language/country in English",
        'fd_order' => "5",
    );

// Home Page Field
$default_fd['lang_country']['lc_home'] = array(
        'fd_name' => "Home Page",
        'fd_type' => "dblist",
        'fd_options' => "page",
        'fd_default' => "1",
        'fd_help' => "The home page for this language/country",
        'fd_order' => "6",
    );

// Root Page Field
$default_fd['lang_country']['lc_root'] = array(
        'fd_name' => "Root Page",
        'fd_type' => "dblist",
        'fd_options' => "page",
        'fd_help' => "The root page for this langauge/country, this is normally the parent of the home page",
        'fd_order' => "7",
    );

// Default Language Field
$default_fd['lang_country']['lc_defaultlang'] = array(
        'fd_name' => "Default Language",
        'fd_type' => "dblist",
        'fd_options' => "language",
        'fd_help' => "The default language to use for this language/country",
        'fd_order' => "8",
    );

