<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_contact
 */

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_contact' => 'Contact - Contact Page'
        );

$_options[] = array(
    'id'          => 'contactcaptcha',
    'category'    => 'Contacts',
    'label'       => 'Contact CAPTCHA',
    'description' => 'A CAPTCHA image helps prevent contact form spam, which is becoming more and more common.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'contact_tracking_code_analytics',
    'category'    => 'Contacts',
    'label'       => 'Google Analytics Goal Settings: Virtual page name for contact thankyou page',
    'description' => 'Since both the contact and thankyou page have the same url, give the thankyou page a virtual page name. Use as "Goal URL". Set-up "Define Funnel" as /contact/ to track  % of people that view your contact page to the number that send a contact.',
    'type'        => 'text',
    'default'     => '/contact-via-contact-form',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'contact_tracking_code',
    'category'    => 'Contacts',
    'label'       => 'Contact conversion tracking code',
    'description' => 'HTML code for conversion tracking enquiries via the contact form. Eg Google Adwords.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'contact_success_message',
    'category'    => 'Contacts',
    'label'       => 'Success message',
    'description' => 'Customize the message that is displayed to the user after a successful contact form submission. Default message is *Your message was sent successfully.*',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'contact_webmaster_copy',
    'category'    => 'Contacts',
    'label'       => 'Send contact emails to webmaster',
    'description' => 'If this option is set, the webmaster will receive a copy of all enquiries from the contact form.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'contact_choice',
    'category'    => 'Contacts',
    'label'       => 'Provide a choice for who the enquiry goes to',
    'description' => 'If yes this will give the contact form a drop down box so the user can choose who the enquiry goes to. This will not work if the Choice List if left blank.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'contact_choice_list',
    'category'    => 'Contacts',
    'label'       => 'Choice List of who enquiry can go to',
    'description' => 'List the people and email addresses of who can be contacted. Enter the name of person then , email address , then next person etc. For Example: Marketing Manager,marketing@domain.com,Sales Manager,sales@domain.com,Customer Support,support@domain.com',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);