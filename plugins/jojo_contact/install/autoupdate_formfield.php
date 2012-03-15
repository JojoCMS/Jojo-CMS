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

$default_td['formfield'] = array(
        'td_name' => "formfield",
        'td_displayname' => "Form Fields",
        'td_primarykey' => "formfield_id",
        'td_displayfield' => "ff_display",
        'td_categorytable' => "form",
        'td_categoryfield' => "ff_form_id",
        'td_orderbyfields' => "ff_order",
        'td_deleteoption' => "yes",
        'td_menutype' => "tree",
        'td_defaultpermissions' => "everyone.show=1\neveryone.view=1\neveryone.edit=1\neveryone.add=1\neveryone.delete=1\nadmin.show=1\nadmin.view=1\nadmin.edit=1\nadmin.add=1\nadmin.delete=1\nnotloggedin.show=1\nnotloggedin.view=1\nnotloggedin.edit=1\nnotloggedin.add=1\nnotloggedin.delete=1\nregistered.show=1\nregistered.view=1\nregistered.edit=1\nregistered.add=1\nregistered.delete=1\nsysinstall.show=1\nsysinstall.view=1\nsysinstall.edit=1\nsysinstall.add=1\nsysinstall.delete=1\n"
    );

// Formfield_id Field
$default_fd['formfield']['formfield_id'] = array(
        'fd_name' => "Formfield_id",
        'fd_type' => "readonly",
        'fd_order' => "1",
    );

// Form Field
$default_fd['formfield']['ff_form_id'] = array(
        'fd_name' => "Form",
        'fd_type' => "dblist",
        'fd_options' => "form",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The form this formfield belongs to.",
        'fd_order' => "2",
    );

// Display Name Field
$default_fd['formfield']['ff_display'] = array(
        'fd_name' => "Display Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The display name of the formfield.",
        'fd_order' => "3",
    );

// Description Field
$default_fd['formfield']['ff_description'] = array(
        'fd_name' => "Description",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "58",
        'fd_help' => "A short description to be shown below the field, to explain the field to the user.",
        'fd_order' => "4",
    );

// Required Field
$default_fd['formfield']['ff_required'] = array(
        'fd_name' => "Required",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "1",
        'fd_help' => "Is this a required field?",
        'fd_order' => "5",
    );

// Validation Field
$default_fd['formfield']['ff_validation'] = array(
        'fd_name' => "Validation",
        'fd_type' => "list",
        'fd_options' => "email:Email\nurl:Url\ntext:Text\ninteger:Integer",
        'fd_size' => "60",
        'fd_help' => "The type of validation to be used - options are 'email', 'url', 'text', 'integer' or leave blank for no validation.",
        'fd_order' => "6",
    );

// Type Field
$default_fd['formfield']['ff_type'] = array(
        'fd_name' => "Type",
        'fd_type' => "list",
        'fd_options' => "text:Text\ntextarea:Textarea\ncheckboxes:Checkboxes\nradio:Radio Buttons\nselect:Select Box\nlist:List Box\nemailwithconfirmation:Email With Confirmation\nhidden:Hidden\nheading:Heading\nnote:Note",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The type of input - use text (single line), textarea (multiple lines), checkboxes (multiple selections), radio buttons (single selection), select (drop down menu), list (multiple selects), emailwithconfirmation is an email field with a confirmation field below it, heading or note.",
        'fd_order' => "7",
    );

// Options Field
$default_fd['formfield']['ff_options'] = array(
        'fd_name' => "Options",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "58",
        'fd_help' => "An array of options separated by new lines. Required for 'checkboxes' and 'select' types.",
        'fd_order' => "8",
    );

// Default Value Field
$default_fd['formfield']['ff_value'] = array(
        'fd_name' => "Default Value",
        'fd_type' => "text",
        'fd_size' => "60",
        'fd_help' => "A default value if any.",
        'fd_order' => "9",
    );

// Size Field
$default_fd['formfield']['ff_size'] = array(
        'fd_name' => "Size",
        'fd_type' => "integer",
        'fd_required' => "no",
        'fd_default' => "30",
        'fd_help' => "Used for 'text' type fields - the size of the input.",
        'fd_order' => "10",
    );

// Rows (Textarea) Field
$default_fd['formfield']['ff_rows'] = array(
        'fd_name' => "Rows (Textarea)",
        'fd_type' => "integer",
        'fd_help' => "Number of rows - only needed for textareas.",
        'fd_order' => "11",
    );

// Columns  (Textarea) Field
$default_fd['formfield']['ff_cols'] = array(
        'fd_name' => "Columns  (Textarea)",
        'fd_type' => "integer",
        'fd_help' => "Number of columns - only needed for textareas.",
        'fd_order' => "12",
    );

// Order Field
$default_fd['formfield']['ff_order'] = array(
        'fd_name' => "Order",
        'fd_type' => "order",
        'fd_required' => "yes",
        'fd_help' => "The order in which the formfields should be displayed in the form. Lower numbers are displayed before higher numbers",
        'fd_order' => "13",
    );

// Is part of the name? Field
$default_fd['formfield']['ff_is_name'] = array(
        'fd_name' => "Is part of the name?",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "Is this field part of the name that should be used as the senders name?",
        'fd_order' => "14",
    );

// Is E-Mail field? Field
$default_fd['formfield']['ff_is_email'] = array(
        'fd_name' => "Is E-Mail field?",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "Is this field the e-mail address that should be used as the senders e-mail address?",
        'fd_order' => "15",
    );

// Show Label?
$default_fd['formfield']['ff_showlabel'] = array(
        'fd_name' => "Show Label?",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "Show the label name for this field (or just the input)",
        'fd_order' => "16",
    );
