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

$o=0;

// Formfield_id Field
$default_fd['formfield']['formfield_id'] = array(
        'fd_name' => "Formfield_id",
        'fd_type' => "readonly",
        'fd_order' => $o++
    );

// Form Field
$default_fd['formfield']['ff_form_id'] = array(
        'fd_name' => "Form",
        'fd_type' => "dblist",
        'fd_options' => "form",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The form this formfield belongs to.",
        'fd_order' => $o++
    );

// Display Name Field
$default_fd['formfield']['ff_fieldset'] = array(
        'fd_name' => "Fieldset",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_size' => "60",
        'fd_help' => "The field group this field belongs to. Leave blank unless beginning a new group.",
        'fd_order' => $o++
    );

// Display Name Field
$default_fd['formfield']['ff_display'] = array(
        'fd_name' => "Display Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The display name of the formfield.",
        'fd_order' => $o++
    );

// Display Name Field
$default_fd['formfield']['ff_fieldname'] = array(
        'fd_name' => "FormField Name",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_size' => "60",
        'fd_help' => "The formname of the formfield - use generic names where possible like 'firstname','email' for users autocomplete to work.",
        'fd_order' => $o++
    );

// Display Name Field
$default_fd['formfield']['ff_placeholder'] = array(
        'fd_name' => "Placeholder Text",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_size' => "60",
        'fd_help' => "Placeholder/hint text eg 'John Smith' - only displayed by some browsers, and possibly of questionable UI value, but there if you want it",
        'fd_order' => $o++
    );

// Description Field
$default_fd['formfield']['ff_description'] = array(
        'fd_name' => "Description",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "58",
        'fd_help' => "A short description to be shown below the field, to explain the field to the user.",
        'fd_order' => $o++
    );

// Required Field
$default_fd['formfield']['ff_required'] = array(
        'fd_name' => "Required",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "1",
        'fd_help' => "Is this a required field?",
        'fd_order' => $o++
    );

// Validation Field
$default_fd['formfield']['ff_validation'] = array(
        'fd_name' => "Validation",
        'fd_type' => "list",
        'fd_options' => "email:Email
url:Url
text:Text
number:Integer
date:Date",
        'fd_size' => "60",
        'fd_help' => "The type of validation to be used - options are 'email', 'url', 'text', 'integer' or leave blank for no validation.",
        'fd_order' => $o++
    );

// Type Field
$default_fd['formfield']['ff_type'] = array(
        'fd_name' => "Type",
        'fd_type' => "list",
        'fd_options' => "text:Text
textarea:Textarea
checkboxes:Checkboxes
radio:Radio Buttons
select:Select Box
list:List Box
emailwithconfirmation:Email With Confirmation
hidden:Hidden
heading:Heading
note:Note
upload:File Upload
privateupload:Private File Upload",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The type of input - use text (single line), textarea (multiple lines), checkboxes (multiple selections), radio buttons (single selection), select (drop down menu), list (multiple selects), emailwithconfirmation is an email field with a confirmation field below it, heading or note.",
        'fd_order' => $o++
    );

// Options Field
$default_fd['formfield']['ff_options'] = array(
        'fd_name' => "Options",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "58",
        'fd_help' => "An array of options separated by new lines. Required for 'checkboxes' and 'select' types.",
        'fd_order' => $o++
    );

// Default Value Field
$default_fd['formfield']['ff_value'] = array(
        'fd_name' => "Default Value",
        'fd_type' => "text",
        'fd_size' => "60",
        'fd_help' => "A default value if any.",
        'fd_order' => $o++
    );

// Size Field
$default_fd['formfield']['ff_size'] = array(
        'fd_name' => "Size",
        'fd_type' => "integer",
        'fd_required' => "no",
        'fd_default' => "30",
        'fd_help' => "Used for 'text' type fields - the size of the input.",
        'fd_order' => $o++
    );

// Rows (Textarea) Field
$default_fd['formfield']['ff_rows'] = array(
        'fd_name' => "Rows (Textarea)",
        'fd_type' => "integer",
        'fd_help' => "Number of rows - only needed for textareas.",
        'fd_order' => $o++
    );

// Columns(Textarea)
$default_fd['formfield']['ff_cols'] = array(
        'fd_name' => "Columns  (Textarea)",
        'fd_type' => "integer",
        'fd_help' => "Number of columns - only needed for textareas.",
        'fd_order' => $o++
    );

// Order
$default_fd['formfield']['ff_order'] = array(
        'fd_name' => "Order",
        'fd_type' => "order",
        'fd_required' => "yes",
        'fd_help' => "The order in which the formfields should be displayed in the form. Lower numbers are displayed before higher numbers",
        'fd_order' => $o++
    );

// CSS Class
$default_fd['formfield']['ff_class'] = array(
        'fd_name' => "CSS Class",
        'fd_type' => "text",
        'fd_size' => "60",
        'fd_help' => "A style class for the form element (if needed).",
        'fd_order' => $o++
    );

// Is part of the name? Field
$default_fd['formfield']['ff_is_name'] = array(
        'fd_name' => "Is part of the name?",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "Is this field part of the name that should be used as the senders name?",
        'fd_order' => $o++
    );

// Is E-Mail field? Field
$default_fd['formfield']['ff_is_email'] = array(
        'fd_name' => "Is E-Mail field?",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "Is this field the e-mail address that should be used as the senders e-mail address?",
        'fd_order' => $o++
    );

// Show Label?
$default_fd['formfield']['ff_showlabel'] = array(
        'fd_name' => "Show Label?",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "Show the label name for this field (or just the input)",
        'fd_order' => $o++
    );
