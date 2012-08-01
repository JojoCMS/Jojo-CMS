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
$o=0;

$default_td['form'] = array(
        'td_name' => "form",
        'td_displayname' => "Forms",
        'td_primarykey' => "form_id",
        'td_displayfield' => "form_name",
        'td_orderbyfields' => "form_id",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
        'td_plugin' => "Jojo_contact",
        'td_defaultpermissions' => "everyone.show=1\neveryone.view=1\neveryone.edit=1\neveryone.add=1\neveryone.delete=1\nadmin.show=1\nadmin.view=1\nadmin.edit=1\nadmin.add=1\nadmin.delete=1\nnotloggedin.show=1\nnotloggedin.view=1\nnotloggedin.edit=1\nnotloggedin.add=1\nnotloggedin.delete=1\nregistered.show=1\nregistered.view=1\nregistered.edit=1\nregistered.add=1\nregistered.delete=1\nsysinstall.show=1\nsysinstall.view=1\nsysinstall.edit=1\nsysinstall.add=1\nsysinstall.delete=1\n",
    );

// ID Field
$default_fd['form']['form_id'] = array(
        'fd_name' => "ID",
        'fd_type' => "readonly",
        'fd_order' => $o++,
        'fd_tabname' => 'Details'
    );

// Form Name Field
$default_fd['form']['form_name'] = array(
        'fd_name' => "Form Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The name of the form.",
        'fd_order' => $o++,
        'fd_tabname' => 'Details'
    );

// Form Page Field
$default_fd['form']['form_page_id'] = array(
        'fd_name' => "Form Page",
        'fd_type' => "dbpluginpagelist",
        'fd_options' => "jojo_plugin_jojo_contact",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The page the form should be displayed on.",
        'fd_order' => $o++,
        'fd_tabname' => 'Details'
    );

// Destination
$default_fd['form']['form_to'] = array(
        'fd_name' => "Destination Email",
        'fd_type' => "email",
        'fd_default' => Jojo::getOption('contactaddress', ''),
        'fd_size' => "60",
        'fd_help' => "What email address should this form be sent to",
        'fd_order' => $o++,
        'fd_tabname' => "Details",
    );

// Destination
$default_fd['form']['form_send'] = array(
        'fd_name' => "Send",
        'fd_type' => "yesno",
        'fd_default' => 1,
        'fd_help' => "Send the form on submit (or just store it in the database)?",
        'fd_order' => $o++,
        'fd_tabname' => "Details",
    );

// Contact CAPTCHA Field
$default_fd['form']['form_captcha'] = array(
        'fd_name' => "Contact CAPTCHA",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "1",
        'fd_help' => "A CAPTCHA image helps prevent contact form spam, which is becoming more and more common.",
        'fd_order' => $o++,
        'fd_tabname' => 'Details'
    );

// Fieldsets
$default_fd['form']['form_fieldsets'] = array(
        'fd_name' => "Show Fieldset Names",
        'fd_type' => "yesno",
        'fd_required' => "no",
        'fd_default' => "1",
        'fd_help' => "Display Fieldset names (if used)",
        'fd_order' => $o++,
        'fd_tabname' => 'Details'
    );

// Form Name Field
$default_fd['form']['form_submit'] = array(
        'fd_name' => "Submit Button",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_default' => "Submit",
        'fd_size' => "30",
        'fd_help' => "The text to use on the submit button.",
        'fd_order' => $o++,
        'fd_tabname' => 'Details'
    );

// Contact CAPTCHA Field
$default_fd['form']['form_submit_label'] = array(
        'fd_name' => "Put submit button label padding in?",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "1",
        'fd_help' => "Do you want a label element put before the submit button? Pick no if you are using placeholder instead of labels for your fields.",
        'fd_order' => $o++,
        'fd_tabname' => 'Details'
    );

$o=0;
// Success message Field
$default_fd['form']['form_success_message'] = array(
        'fd_name' => "Success message",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "57",
        'fd_help' => "Customize the message that is displayed to the user after a successful contact form submission. Default message is *Your message was sent successfully.*",
        'fd_order' => $o++,
        'fd_tabname' => 'Response'
    );

// Form Name Field
$default_fd['form']['form_subject'] = array(
        'fd_name' => "Form Subject",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_size' => "60",
        'fd_help' => "The subject line to use in emails.",
        'fd_order' => $o++,
        'fd_tabname' => 'Response'
    );

// Send autoreply
$default_fd['form']['form_autoreply'] = array(
        'fd_name' => "Send confirmation email",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "If this option is set, the enquirer will recieve a confirmation email.",
        'fd_order' => $o++,
        'fd_tabname' => 'Response'
    );

// Autoreply Content Field
$default_fd['form']['form_autoreply_body'] = array(
        'fd_name' => "Autoreply Content",
        'fd_type' => "texteditor",
        'fd_options' => "form_autoreply_bodycode",
        'fd_rows' => "10",
        'fd_cols' => "50",
        'fd_help' => "The text for the autoreply email.",
        'fd_order' => $o++,
        'fd_tabname' => 'Response'
    );


// Code Field
$default_fd['form']['form_autoreply_bodycode'] = array(
        'fd_name' => "Body",
        'fd_type' => "hidden",
        'fd_order' => $o++,
        'fd_tabname' => 'Response'
    );

// Reply Style
$default_fd['form']['form_autoreply_css'] = array(
        'fd_name' => "Form CSS",
        'fd_type' => "text",
        'fd_required' => "no",
        'fd_size' => "60",
        'fd_help' => "The CSS formatting style to use in the email.",
        'fd_order' => $o++,
        'fd_tabname' => 'Response'
    );


/* Advanced Tab */
$o=0;

// Send contact emails to webmaster Field
$default_fd['form']['form_webmaster_copy'] = array(
        'fd_name' => "Send contact emails to webmaster",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "1",
        'fd_help' => "If this option is set, the webmaster will receive a copy of all enquiries from the contact form.",
        'fd_order' => $o++,
        'fd_tabname' => 'Xtras'
    );

// Hide form and just display sucess message on success
$default_fd['form']['form_hideonsuccess'] = array(
        'fd_name' => "Hide form on submit",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "If yes this will hide the form after submit and just display the success message.",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );

// Provide a choice for who the enquiry goes to Field
$default_fd['form']['form_choice'] = array(
        'fd_name' => "Provide a choice of destinations",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "If yes this will give the contact form a drop down box so the user can choose who the enquiry goes to. This will not work if the Choice List if left blank.",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );

// Choice List of who enquiry can go to Field
$default_fd['form']['form_choice_list'] = array(
        'fd_name' => "Choice List of who enquiry can go to",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "58",
        'fd_help' => "List the people and email addresses of who can be contacted. Enter the name of person then , email address , then next person etc. For Example: Marketing Manager,marketing@domain.com\nSales Manager,sales@domain.com\nCustomer Support,support@domain.com",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );

// Google Analytics Goal Settings: Virtual page name for contact thankyou page Field
$default_fd['form']['form_tracking_code_analytics'] = array(
        'fd_name' => "Google Analytics Goal Settings: Virtual page name for contact thankyou page",
        'fd_type' => "text",
        'fd_options' => "formfield",
        'fd_default' => "/contact-via-contact-form",
        'fd_size' => "60",
        'fd_help' => "Since both the contact and thankyou page have the same url, give the thankyou page a virtual page name. Use as \"Goal URL\". Set-up \"Define Funnel\" as /contact/ to track  % of people that view your contact page to the number that send a contact.",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );

$default_fd['form']['form_thank_you_uri'] = array(
        'fd_name' => "Thank you page URI",
        'fd_type' => "internalurl",
        'fd_options' => "",
        'fd_default' => "",
        'fd_size' => "",
        'fd_help' => "If entered, the visitor will be redirected to this URI after a successful form submission.",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );

// Contact conversion tracking code Field
$default_fd['form']['form_tracking_code'] = array(
        'fd_name' => "Contact conversion tracking code",
        'fd_type' => "textarea",
        'fd_rows' => "4",
        'fd_cols' => "58",
        'fd_help' => "HTML code for conversion tracking enquiries via the contact form. Eg Google Adwords.",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );

// Form Submit URL Redirect
$default_fd['form']['form_action_url'] = array(
        'fd_name' => "Submit form to",
        'fd_type' => "text",
        'fd_options' => "formfield",
        'fd_size' => "60",
        'fd_help' => "Url that the form will be submitted to. Defaults to self or 'submit-form/' for filtered forms.",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );


// Form Submit URL Redirect
$default_fd['form']['form_redirect_url'] = array(
        'fd_name' => "Redirect here after processing",
        'fd_type' => "text",
        'fd_options' => "formfield",
        'fd_size' => "60",
        'fd_help' => "Url that will be redirected to after processing. Defaults to self or referring url for filtered forms.",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );

// Multi-page form
$default_fd['form']['form_multipage'] = array(
        'fd_name' => "Display fieldsets as multiple pages with nav",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "0",
        'fd_help' => "If yes, the form will use js to hide all but the first fieldset and add navigation to show the rest.",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );

// Multi-page form
$default_fd['form']['form_submit_end'] = array(
        'fd_name' => "Show submit button with final page",
        'fd_type' => "yesno",
        'fd_required' => "yes",
        'fd_default' => "1",
        'fd_help' => "If yes this only show the submit button when the final page is viewable (only applies to on non-multipage forms).",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );

// Form Upload Folder
$default_fd['form']['form_uploadfolder'] = array(
        'fd_name' => "Upload folder",
        'fd_type' => "text",
        'fd_size' => "60",
        'fd_help' => "Put files uploaded from the form in a folder called this (will use the form ID if left blank)",
        'fd_order' => $o++,
        'fd_tabname' => "Xtras"
    );
