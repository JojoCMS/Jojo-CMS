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

// Form Field
$default_fd['formfield']['ff_form_id'] = array(
        'fd_name' => "Form",
        'fd_type' => "dblist",
        'fd_options' => "form",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The form this field belongs to.",
        'fd_order' => $o++
    );

// Order
$default_fd['formfield']['ff_order'] = array(
        'fd_name' => "Display Order",
        'fd_type' => "order",
        'fd_required' => "no",
        'fd_help' => "The order in which the formfields should be displayed in the form. Lower numbers are displayed before higher numbers",
        'fd_order' => $o++
    );

// Display Name Field
$default_fd['formfield']['ff_display'] = array(
        'fd_name' => "Label",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The name of the field, used for display. Use generic terms where possible like 'Firstname','Email','Address' so browsers can autocomplete the fields.",
        'fd_order' => $o++
    );

// Placeholder
$default_fd['formfield']['ff_placeholder'] = array(
        'fd_name' => "Placeholder Text",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_size' => "60",
        'fd_help' => "Placeholder/hint text eg 'John Smith'. Ensure labels are not hidden for screen readers if using placeholders instead of displayed labels",
        'fd_order' => $o++
    );

// Description Field
$default_fd['formfield']['ff_description'] = array(
        'fd_name' => "Help Text",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "58",
        'fd_help' => "A short description to be shown below the field or in a popup, to explain the field to the user.",
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
date:Date
emailwithconfirmation:Email With Confirmation
hidden:Hidden
heading:Heading
note:Note
upload:File Upload
privateupload:Private File Upload
attachment:Attachment",
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
        'fd_help' => "An array of options separated by new lines. Only used for listing checkbox, radio and select field types.",
        'fd_order' => $o++
    );

// Default Value Field
$default_fd['formfield']['ff_value'] = array(
        'fd_name' => "Default Value",
        'fd_type' => "textarea",
        'fd_rows' => "2",
        'fd_cols' => "58",
        'fd_help' => "A default value if any.",
        'fd_order' => $o++
    );

// Required Field
$default_fd['formfield']['ff_required'] = array(
        'fd_name' => "Required",
        'fd_type' => "yesno",
        'fd_required' => "no",
        'fd_default' => "0",
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
        'fd_help' => "Validate the input as: - options are 'email', 'url', 'text', 'integer' or leave blank for no validation.",
        'fd_order' => $o++
    );

// Is part of the name? Field
$default_fd['formfield']['ff_is_name'] = array(
        'fd_name' => "Sender name field?",
        'fd_type' => "yesno",
        'fd_required' => "no",
        'fd_default' => "0",
        'fd_help' => "Is this field part of the name that should be used as the senders name?",
        'fd_order' => $o++
    );

// Is E-Mail field? Field
$default_fd['formfield']['ff_is_email'] = array(
        'fd_name' => "Sender Email field?",
        'fd_type' => "yesno",
        'fd_required' => "no",
        'fd_default' => "0",
        'fd_help' => "Is this field the e-mail address that should be used as the sender?",
        'fd_order' => $o++
    );

/* 
** Layout Tab
*/

$o=0;

// Display Name Field
$default_fd['formfield']['ff_fieldset'] = array(
        'fd_name' => "Fieldset",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_size' => "60",
        'fd_help' => "The tab group this field belongs to. Leave blank unless beginning a new group.",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );

// Prepend Value Field
$default_fd['formfield']['ff_prependvalue'] = array(
        'fd_name' => "Prepend with",
        'fd_type' => "text",
        'fd_size' => "10",
        'fd_help' => "Display in front of the value - eg $",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );

// Append Value Field
$default_fd['formfield']['ff_appendvalue'] = array(
        'fd_name' => "Append with",
        'fd_type' => "text",
        'fd_size' => "10",
        'fd_help' => "Display after the value - eg to show units like cm",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );

// CSS Class
$default_fd['formfield']['ff_class'] = array(
        'fd_name' => "CSS Class",
        'fd_type' => "text",
        'fd_size' => "60",
        'fd_help' => "A style class for the form element (if needed).",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );

// Size Field
$default_fd['formfield']['ff_size'] = array(
        'fd_name' => "Size",
        'fd_type' => "integer",
        'fd_required' => "no",
        'fd_default' => "30",
        'fd_help' => "Used for 'text' type fields - the size of the unstyled input box. Also used for Heading fields to set the size: 3 = h3 and so on",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );


// Rows (Textarea) Field
$default_fd['formfield']['ff_rows'] = array(
        'fd_name' => "Rows (Textarea)",
        'fd_type' => "integer",
        'fd_help' => "Number of rows - only needed for textareas without height css.",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );

// Columns(Textarea)
$default_fd['formfield']['ff_cols'] = array(
        'fd_name' => "Columns  (Textarea)",
        'fd_type' => "integer",
        'fd_help' => "Number of columns - only needed for textareas without width css.",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );

// Display-Only Field
$default_fd['formfield']['ff_displayonly'] = array(
        'fd_name' => "Display online only",
        'fd_type' => "yesno",
        'fd_required' => "no",
        'fd_default' => "0",
        'fd_help' => "Show online but don't add to email text",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );

// Show Label?
$default_fd['formfield']['ff_showlabel'] = array(
        'fd_name' => "Show Label?",
        'fd_type' => "yesno",
        'fd_required' => "no",
        'fd_default' => "1",
        'fd_help' => "Show the label or hide it with the BS sr-only class (so screen readers can still read it)",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );

// Pad Label? - Deprecated - Use class and css instead
$default_fd['formfield']['ff_padlabel'] = array(
        'fd_name' => "Pad (empty) Label?",
        'fd_type' => "hidden",
        'fd_required' => "no",
        'fd_default' => "0",
        'fd_help' => "Pad the label space for this field (if it's set to not show)",
        'fd_tabname' => "Layout / Styling",
        'fd_order' => $o++
    );

/* 
** Technical Tab
*/

$o=0;

// Formfield_id Field
$default_fd['formfield']['formfield_id'] = array(
        'fd_name' => "Field ID",
        'fd_type' => "readonly",
        'fd_tabname' => "Technical",
        'fd_order' => $o++
    );

// Max Length Field
$default_fd['formfield']['ff_maxlength'] = array(
        'fd_name' => "Max Length",
        'fd_type' => "integer",
        'fd_required' => "no",
        'fd_default' => "0",
        'fd_help' => "Restrict the number input charcters allowed. Leave blank for unlimited.",
        'fd_tabname' => "Technical",
        'fd_order' => $o++
    );

// Field Name 
$default_fd['formfield']['ff_fieldname'] = array(
        'fd_name' => "FormField Name",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_size' => "60",
        'fd_help' => "The name value of the formfield. A cleaned version of the Label field will be used if this is left blank.",
        'fd_tabname' => "Technical",
        'fd_order' => $o++
    );



