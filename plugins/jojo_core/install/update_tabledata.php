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

$default_td['tabledata'] = array(
        'td_name' => "tabledata",
        'td_displayname' => "Table Data",
        'td_primarykey' => "tabledataid",
        'td_displayfield' => "td_name",
        'td_rolloverfield' => "td_displayname",
        'td_orderbyfields' => "td_name",
        'td_deleteoption' => "no",
        'td_addsimilar' => "no",
        'td_menutype' => "list",
    );

// ID Field
$default_fd['tabledata']['tabledataid'] = array(
        'fd_name' => "ID",
        'fd_type' => "hidden",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => "1",
    );

// Database Table Name Field
$default_fd['tabledata']['td_name'] = array(
        'fd_name' => "Database Table Name",
        'fd_type' => "readonly",
        'fd_order' => "2",
    );

// Display name Field
$default_fd['tabledata']['td_displayname'] = array(
        'fd_name' => "Display name",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_help' => "The name of the table as you want it displayed to the user",
        'fd_order' => "3",
    );

// Primary key field Field
$default_fd['tabledata']['td_primarykey'] = array(
        'fd_name' => "Primary key field",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_help' => "Primary key of the table. Default is (TABLENAME)id",
        'fd_order' => "4",
    );

// Display field(s) Field
$default_fd['tabledata']['td_displayfield'] = array(
        'fd_name' => "Display field(s)",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_help' => "The field to display in the record list eg name. Feel free to concatenate fields eg CONCAT(firstname, ' ', lastname)",
        'fd_order' => "5",
    );

// Parent Field Field
$default_fd['tabledata']['td_parentfield'] = array(
        'fd_name' => "Parent Field",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_help' => "The name of the parent column if this table references itself through a parent field. This is used to create a fully nested tree structure.",
        'fd_order' => "6",
    );

// Rollover field(s) Field
$default_fd['tabledata']['td_rolloverfield'] = array(
        'fd_name' => "Rollover field(s)",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_help' => "The field to be used in a rollover of the record list tree",
        'fd_order' => "7",
    );

// Order by field(s) Field
$default_fd['tabledata']['td_orderbyfields'] = array(
        'fd_name' => "Order by field(s)",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_help' => "SQL for ordering the record list eg name,date",
        'fd_order' => "8",
    );

// Delete option Field
$default_fd['tabledata']['td_deleteoption'] = array(
        'fd_name' => "Delete option",
        'fd_type' => "checkbox",
        'fd_options' => "yes:Yes\nno:No",
        'fd_default' => "yes",
        'fd_help' => "Displays a delete button in the user interface",
        'fd_order' => "9",
    );

// Add similar option Field
$default_fd['tabledata']['td_addsimilar'] = array(
        'fd_name' => "Add similar option",
        'fd_type' => "checkbox",
        'fd_options' => "yes:Yes\nno:No",
        'fd_default' => "yes",
        'fd_help' => "Displays an add similar button in the user interface",
        'fd_order' => "10",
    );

// Menu type Field
$default_fd['tabledata']['td_menutype'] = array(
        'fd_name' => "Menu type",
        'fd_type' => "list",
        'fd_options' => "tree:Tree\nsearchabletree:Searchable Tree\nlist:List\n",
        'fd_default' => "tree",
        'fd_help' => "The type of list to use in the user interface",
        'fd_order' => "11",
    );

// Description Field
$default_fd['tabledata']['td_help'] = array(
        'fd_name' => "Description",
        'fd_type' => "textarea",
        'fd_rows' => "10",
        'fd_cols' => "40",
        'fd_order' => "12",
    );

// Go Live Field Field
$default_fd['tabledata']['td_golivefield'] = array(
        'fd_name' => "Go Live Field",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_help' => "The name of the UnixDate field that indicates the go live date of this content (optional)",
        'fd_order' => "13",
    );

// Expiry Field Field
$default_fd['tabledata']['td_expiryfield'] = array(
        'fd_name' => "Expiry Field",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_help' => "The name of the UnixDate field that indicates the expiry date of this content (optional)",
        'fd_order' => "14",
    );

// Active Field Field
$default_fd['tabledata']['td_activefield'] = array(
        'fd_name' => "Active Field",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_order' => "15",
    );

// Privacy Field Field
$default_fd['tabledata']['td_privacyfield'] = array(
        'fd_name' => "Privacy Field",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_help' => "The name of the TEXT field that contains privacy data for this record (optional)",
        'fd_order' => "16",
    );

// Language Field Field
$default_fd['tabledata']['td_languagefield'] = array(
        'fd_name' => "Language Field",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_help' => "The name of the TEXT field that contains language data for this record (optional)",
        'fd_order' => "16",
    );

// Auto Update Field
$default_fd['tabledata']['td_autoupdate'] = array(
        'fd_name' => "Auto Update",
        'fd_type' => "radio",
        'fd_options' => "yes:Yes\nno:No",
        'fd_default' => "yes",
        'fd_help' => "Should this table's option be automatically updated from autoupdate_tablename files when setup is run?",
        'fd_order' => "17",
    );

// Primary plugin class name Field
$default_fd['tabledata']['td_plugin'] = array(
        'fd_name' => "Table Plugin Name",
        'fd_type' => "text",
        'fd_help' => "The classname of the main plugin that uses the table",
        'fd_order' => "18",
    );


/* Categories Tab */

// Category table Field
$default_fd['tabledata']['td_categorytable'] = array(
        'fd_name' => "Category table",
        'fd_type' => "text",
        'fd_size' => "30",
        'fd_help' => "Enter the name of another database table the provides categories for rows in this table.",
        'fd_order' => "1",
        'fd_tabname' => "Categories",
    );

// Category field Field
$default_fd['tabledata']['td_categoryfield'] = array(
        'fd_name' => "Category field",
        'fd_type' => "text",
        'fd_size' => "30",
        'fd_help' => "Enter the name of the column in this table that matches up with the Category Table.",
        'fd_order' => "2",
        'fd_tabname' => "Categories",
    );

// Category field Field
$default_fd['tabledata']['td_m2mcategoryfield'] = array(
        'fd_name' => "M2M field",
        'fd_type' => "text",
        'fd_size' => "30",
        'fd_help' => "Enter the name of the column in this table that is the primary Many2Many field.",
        'fd_order' => "3",
        'fd_tabname' => "Categories",
    );


/* Grouping Tab */

// Group 1 field Field
$default_fd['tabledata']['td_group1'] = array(
        'fd_name' => "Group 1 field",
        'fd_type' => "text",
        'fd_size' => "30",
        'fd_help' => "A column name to group results by. This is used to create 1 level tree like structure using only one table.",
        'fd_order' => "1",
        'fd_tabname' => "Grouping",
    );

// Group 2 field Field
$default_fd['tabledata']['td_group2'] = array(
        'fd_name' => "Group 2 field",
        'fd_type' => "text",
        'fd_size' => "30",
        'fd_help' => "A second column name to group results by. This is used along with \"Group 1 field\" to create 2 level tree like structure using only one table.",
        'fd_order' => "2",
        'fd_tabname' => "Grouping",
    );


/* Legacy Options Tab */

// Filter Field
$default_fd['tabledata']['td_filter'] = array(
        'fd_name' => "Filter",
        'fd_type' => "radio",
        'fd_options' => "yes:Yes\nno:No",
        'fd_default' => "no",
        'fd_order' => "1",
        'fd_tabname' => "Legacy Options",
    );

// Filterby Field
$default_fd['tabledata']['td_filterby'] = array(
        'fd_name' => "Filterby",
        'fd_type' => "text",
        'fd_size' => "40",
        'fd_order' => "2",
        'fd_tabname' => "Legacy Options",
    );


/* Table Permissions Tab */

// Defaultpermissions Field
$default_fd['tabledata']['td_defaultpermissions'] = array(
        'fd_name' => "Defaultpermissions",
        'fd_type' => "tablepermissions",
        'fd_order' => "1",
        'fd_tabname' => "Table Permissions",
    );

// Groupowner Field
$default_fd['tabledata']['td_groupowner'] = array(
        'fd_name' => "Groupowner",
        'fd_type' => "text",
        'fd_order' => "2",
        'fd_tabname' => "Table Permissions",
    );

// Userowner Field
$default_fd['tabledata']['td_userowner'] = array(
        'fd_name' => "Userowner",
        'fd_type' => "text",
        'fd_size' => "30",
        'fd_order' => "3",
        'fd_tabname' => "Table Permissions",
    );


