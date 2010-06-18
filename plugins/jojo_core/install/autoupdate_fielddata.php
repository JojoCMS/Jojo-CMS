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

$default_td['fielddata'] = array(
        'td_name' => "fielddata",
        'td_displayname' => "Field Data",
        'td_primarykey' => "fielddataid",
        'td_displayfield' => "fd_field",
        'td_rolloverfield' => "fd_name",
        'td_orderbyfields' => "fd_table,fd_order,fd_field",
        'td_deleteoption' => "no",
        'td_addsimilar' => "no",
        'td_menutype' => "tree",
        'td_group1' => "fd_table",
        'td_group2' => "fd_tabname",
        'td_defaultpermissions' => "everyone.show=1\neveryone.view=1\neveryone.edit=1\neveryone.add=1\neveryone.delete=1\nadmin.show=1\nadmin.view=1\nadmin.edit=1\nadmin.add=1\nadmin.delete=1\nnotloggedin.show=1\nnotloggedin.view=1\nnotloggedin.edit=1\nnotloggedin.add=1\nnotloggedin.delete=1\nregistered.show=1\nregistered.view=1\nregistered.edit=1\nregistered.add=1\nregistered.delete=1\nsysinstall.show=1\nsysinstall.view=1\nsysinstall.edit=1\nsysinstall.add=1\nsysinstall.delete=1\n",
    );

// ID Field
$default_fd['fielddata']['fielddataid'] = array(
        'fd_name' => "ID",
        'fd_type' => "hidden",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => "1",
    );

// Table Field
$default_fd['fielddata']['fd_table'] = array(
        'fd_name' => "Table",
        'fd_type' => "readonly",
        'fd_order' => "2",
    );

// Field Field
$default_fd['fielddata']['fd_field'] = array(
        'fd_name' => "Field",
        'fd_type' => "readonly",
        'fd_order' => "3",
    );

// Name Field
$default_fd['fielddata']['fd_name'] = array(
        'fd_name' => "Name",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_order' => "4",
    );

// SQL Type Field
$default_fd['fielddata']['fd_sqltype'] = array(
        'fd_name' => "SQL Type",
        'fd_type' => "readonly",
        'fd_order' => "5",
    );

// Type Field
$default_fd['fielddata']['fd_type'] = array(
        'fd_name' => "Type",
        'fd_type' => "fieldtype",
        'fd_default' => "text",
        'fd_order' => "6",
    );

// Options Field
$default_fd['fielddata']['fd_options'] = array(
        'fd_name' => "Options",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "20",
        'fd_order' => "7",
    );

// Tab Name Field
$default_fd['fielddata']['fd_tabname'] = array(
        'fd_name' => "Tab Name",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_order' => "8",
    );

// Required Field
$default_fd['fielddata']['fd_required'] = array(
        'fd_name' => "Required",
        'fd_type' => "radio",
        'fd_options' => "yes:Yes\nno:No",
        'fd_default' => "no",
        'fd_order' => "9",
    );

// Required Field
$default_fd['fielddata']['fd_readonly'] = array(
        'fd_name' => "Read Only",
        'fd_type' => "yesno",
        'fd_default' => "0",
        'fd_order' => "9",
    );

// Default Field
$default_fd['fielddata']['fd_default'] = array(
        'fd_name' => "Default",
        'fd_type' => "text",
        'fd_size' => "30",
        'fd_order' => "10",
    );

// Size Field
$default_fd['fielddata']['fd_size'] = array(
        'fd_name' => "Size",
        'fd_type' => "integer",
        'fd_default' => "0",
        'fd_size' => "5",
        'fd_order' => "11",
    );

// Rows Field
$default_fd['fielddata']['fd_rows'] = array(
        'fd_name' => "Rows",
        'fd_type' => "integer",
        'fd_default' => "0",
        'fd_size' => "5",
        'fd_order' => "12",
    );

// Columns Field
$default_fd['fielddata']['fd_cols'] = array(
        'fd_name' => "Columns",
        'fd_type' => "integer",
        'fd_default' => "0",
        'fd_size' => "5",
        'fd_order' => "13",
    );

// Order Field
$default_fd['fielddata']['fd_order'] = array(
        'fd_name' => "Order",
        'fd_type' => "order",
        'fd_default' => "0",
        'fd_size' => "5",
        'fd_order' => "14",
    );

// Units Field
$default_fd['fielddata']['fd_units'] = array(
        'fd_name' => "Units",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_order' => "15",
    );

// Flags Field
$default_fd['fielddata']['fd_flags'] = array(
        'fd_name' => "Flags",
        'fd_type' => "text",
        'fd_size' => "50",
        'fd_order' => "16",
    );

// Help Info Field
$default_fd['fielddata']['fd_help'] = array(
        'fd_name' => "Help Info",
        'fd_type' => "textarea",
        'fd_rows' => "5",
        'fd_cols' => "40",
        'fd_order' => "17",
    );


/* Advanced Tab */

// Max Size Field
$default_fd['fielddata']['fd_maxsize'] = array(
        'fd_name' => "Max Size",
        'fd_type' => "integer",
        'fd_default' => "0",
        'fd_size' => "5",
        'fd_order' => "1",
        'fd_tabname' => "Advanced",
    );

// Maximum value Field
$default_fd['fielddata']['fd_maxvalue'] = array(
        'fd_name' => "Maximum value",
        'fd_type' => "text",
        'fd_size' => "10",
        'fd_order' => "2",
        'fd_tabname' => "Advanced",
    );

// Minimum value Field
$default_fd['fielddata']['fd_minvalue'] = array(
        'fd_name' => "Minimum value",
        'fd_type' => "text",
        'fd_size' => "10",
        'fd_order' => "3",
        'fd_tabname' => "Advanced",
    );

// Show Label Field
$default_fd['fielddata']['fd_showlabel'] = array(
        'fd_name' => "Show Label",
        'fd_type' => "radio",
        'fd_options' => "yes:Yes\nno:No",
        'fd_default' => "yes",
        'fd_order' => "4",
        'fd_tabname' => "Advanced",
    );

// Nulls Field
$default_fd['fielddata']['fd_nulls'] = array(
        'fd_name' => "Nulls",
        'fd_type' => "radio",
        'fd_options' => "yes:Yes\nno:No",
        'fd_default' => "no",
        'fd_order' => "5",
        'fd_tabname' => "Advanced",
    );

// Auto Update Field
$default_fd['fielddata']['fd_autoupdate'] = array(
        'fd_name' => "Auto Update",
        'fd_type' => "radio",
        'fd_options' => "yes:Yes\nno:No",
        'fd_default' => "yes",
        'fd_order' => "6",
        'fd_tabname' => "Advanced",
    );


/* Many-to-many Tab */

// Link Table Field
$default_fd['fielddata']['fd_m2m_linktable'] = array(
        'fd_name' => "Link Table",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_order' => "1",
        'fd_tabname' => "Many-to-many",
    );

// Link Item Field Field
$default_fd['fielddata']['fd_m2m_linkitemid'] = array(
        'fd_name' => "Link Item Field",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_order' => "2",
        'fd_tabname' => "Many-to-many",
    );

// Link Category Field Field
$default_fd['fielddata']['fd_m2m_linkcatid'] = array(
        'fd_name' => "Link Category Field",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_order' => "3",
        'fd_tabname' => "Many-to-many",
    );

// Link Category Table Field
$default_fd['fielddata']['fd_m2m_cattable'] = array(
        'fd_name' => "Link Category Table",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_order' => "4",
        'fd_tabname' => "Many-to-many",
    );
