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
 * @package Core
 */

/* Replaces content variables with values */
Jojo::addFilter('content', 'applyContentVars', 'Core');

/* Replaces template/content snippets */
Jojo::addFilter('output', 'getSnippet', 'Core', 99);

/* replaces <a href="#top"> with <a href="page/you/are/on/#top"> so it will work as expected */
Jojo::addFilter('content', 'fixAnchorLinks', 'Core');

/* nofollows any links to domains in the nofollow_list option */
Jojo::addFilter('content', 'nofollowList', 'Core');

/* Unescape any escaped square brackets */
Jojo::addFilter('content', 'unescapeSquareBrackets', 'Core', 100);

/* Sitemap filter */
Jojo::addFilter('jojo_sitemap', 'sitemap', 'Core');

/* XML filter */
Jojo::addFilter('jojo_xml_sitemap', 'xmlsitemap', 'Core');

/* Search Filter */
Jojo::addFilter('jojo_search', 'search', 'Core');

/* Debug Mode Hook */
Jojo::addHook('foot', 'debugmodestatus', 'Core');

/* SystemInstaller Menu Hook */

Jojo::addHook('foot', 'systeminstaller_menu', 'Core');



Jojo::addHook('admin_action_after_save_page', 'admin_action_after_save_page', 'Core');

/* Register URI patterns */
Jojo::registerURI("css/[file:([^\/]*)$]",                                 'Jojo_Plugin_Core_Css');             // "css/something.css" for css files
Jojo::registerURI("json/[file:([^\/]*)$]",                                 'Jojo_Plugin_Core_Json');            // "json/something.php" for json requests
Jojo::registerURI("actions/[file:([^\/]*)$]",                             'Jojo_Plugin_Core_Action');          // "actions/something.php" for frajax requests
Jojo::registerURI("images/[file:(.*)$]",                                   'Jojo_Plugin_Core_Image');           // "images/somewhere/something.jpg" for image files
Jojo::registerURI("js/[file:(.*)$]",                                        'Jojo_Plugin_Core_Js');              // "js/something.js" for javascript files
Jojo::registerURI("external/[file:(.*)$]",                                  'Jojo_Plugin_Core_External');        // "external/somewhere/something.ext" for external files
Jojo::registerURI("downloads/[file:(.*)$]",                                'Jojo_Plugin_Core_Download');        // "download/somewhere/something.ext" for user uploaded files
Jojo::registerURI("files/[file:(.*)$]",                                     'Jojo_Plugin_Core_File');            // "files/somewhere/something.ext" for custom files for a plugin, eg flash
Jojo::registerURI("inline/[file:(.*)$]",                                    'Jojo_Plugin_Core_Inline');          // "inline/somewhere/something.ext" any file in the downloads folder to be served inline
Jojo::registerURI("forgot-password/reset/[reset:([a-f0-9]{40})]",    'Jojo_Plugin_Forgot_password'); // "forgot-password/reset/21b618b7252f6dbc6744200ced0c44ce3e2664da/" - 40 chars of hex
Jojo::registerURI("forgot-password/reset/[reset:([a-z0-9]{16})]",    'Jojo_Plugin_Forgot_password'); // "forgot-password/reset/sga4v6wqg6ij65jd/" - a shorter version 16 chars of alpha numeric
Jojo::registerURI("login/[redirect:(.*)]",                                 'Jojo_Plugin_Login');           // "login/page-to-redirect-to-on-success/" for login page

$_provides['fieldTypes'] = array(
        'birthday'         => 'Birthday',
        'checkbox'         => 'Check Box',
        'checkboxes'       => 'Check Boxes',
        'date'             => 'MySQL Date',
        'dblist'           => 'Database List',
        'dbpagelist'       => 'Database List For Pages',
        'dbpluginpagelist'  => 'Database List For Plugin Pages',
        'decimal'          => 'Decimal',
        'email'            => 'Email',
        'fieldtype'        => 'Field Type Dropdown',
        'fileupload'       => 'File Upload',
        'hidden'           => 'Hidden',
        'integer'          => 'Integer',
        'internalurl'      => 'Internal URL',
        'list'             => 'List',
        'many2many'        => 'Many 2 Many List',
        'order'            => 'Order',
        'password'         => 'Password',
        'permissions'      => 'Permissions',
        'plugin'           => 'Plugin',
        'privacy'          => 'Privacy data',
        'radio'            => 'Radio Group',
        'readonly'         => 'Read Only',
        'tablepermissions' => 'Table Permissions',
        'text'             => 'Text',
        'encryptedtext'    => 'Text (Encrypted)',
        'textarea'         => 'Textarea',
        'texteditor'       => 'Text Editor',
        'timestamp'        => 'Timestamp',
        'url'              => 'URL',
        'unixdate'         => 'Unix Date',
        'yesno'         => 'Yes or No'
        );

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_404'                 => 'Core - 404 Page Handler',
        'Jojo_Plugin_Core_Css'            => 'Core - CSS File Handler',
        'Jojo_Plugin_Core_Json'           => 'Core - JSON Request Handler',
        'Jojo_Plugin_Core_Action'         => 'Core - Frajax Request Handler',
        'Jojo_Plugin_Core_Js'             => 'Core - Javascript Request Handler',
        'Jojo_Plugin_Core_Image'          => 'Core - Image Handler',
        'Jojo_Plugin_Core_External'       => 'Core - External File Handler',
        'Jojo_Plugin_Core_Download'       => 'Core - Download File Handler',
        'Jojo_Plugin_Core_Files'          => 'Core - Plugin File Handler',
        'Jojo_Plugin_Core_Favicon'        => 'Core - Favicon Handler',
        'Jojo_Plugin_Admin'               => 'Core - Admin',
        'Jojo_Plugin_Admin_configuration' => 'Core - Admin Configuration',
        'Jojo_Plugin_Admin_content'       => 'Core - Admin Content',
        'Jojo_Plugin_Admin_customize'     => 'Core - Admin Customize',
        'Jojo_Plugin_Admin_edit'          => 'Core - Admin Edit Table Contents',
        'Jojo_Plugin_Admin_eventlog'      => 'Core - Admin Event Log Viewer',
        'Jojo_Plugin_Admin_options'       => 'Core - Admin Options',
        'Jojo_Plugin_Admin_plugins'       => 'Core - Admin Manage plugins',
        'Jojo_Plugin_Admin_redirects'     => 'Core - Admin Redirects',
        'Jojo_Plugin_Admin_reports'       => 'Core - Admin Reports',
        'Jojo_Plugin_Admin_root'          => 'Core - Admin Root',
        'Jojo_Plugin_Admin_themes'        => 'Core - Admin Manage Themes',
        'Jojo_Plugin_Change_password'     => 'Core - Change Password',
        'Jojo_Plugin_Forgot_password'     => 'Core - Forgot Password',
        'Jojo_Plugin_Index'               => 'Core - Index/Home Page',
        'Jojo_Plugin_Login'               => 'Core - Login',
        'Jojo_Plugin_Logout'              => 'Core - Logout',
        'Jojo_Plugin_Register'            => 'Core - User Registration',
        'Jojo_Plugin_Submit_form'         => 'Core - Submit Form Handler',
        'Jojo_Plugin_User_profile'        => 'Core - User Profile'
        );

/* Config */

$_options[] = array(
    'id'          => 'eventlog_email_notification',
    'category'    => 'Config',
    'label'       => 'Eventlog email notification',
    'description' => 'A daily email will be sent to the webmaster detailing any high priority eventlog entries in the last 24 hours.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'enablegzip',
    'category'    => 'Config',
    'label'       => 'Enable GZip',
    'description' => 'Gzipping website content is a way of compressing HTML pages so they are faster to download. This should usually be enabled, but is not supported on some web hosts.',
    'type'        => 'radio',
    'default'     => '0',
    'options'     => '0,1'
);

$_options[] = array(
    'id'          => 'doctype',
    'category'    => 'Config',
    'label'       => 'Doctype',
    'description' => 'Declare Doctype as XHTML or HTML5.',
    'type'        => 'radio',
    'default'     => 'html5',
    'options'     => 'xhtml,html5'
);

$_options[] = array(
    'id'          => 'boilerplate_htmltag',
    'category'    => 'Config',
    'label'       => 'Boilerplate Html tag',
    'description' => 'Add browser conditionals to html tag',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'siteurl',
    'category'    => 'Config',
    'label'       => 'Site URL',
    'description' => 'This option is set automatically. This is the URL that is used for the site. Where several domains are available for a site, they will all be redirected to the URL specified here. NO TRAILING SLASH.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'secureurl',
    'category'    => 'Config',
    'label'       => 'Secure URL',
    'description' => 'If there is a SSL version of the site, enter the URL here, including the https:// but not the trailing slash.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'assetdomains',
    'category'    => 'Config',
    'label'       => 'Asset domains',
    'description' => 'A new-line seperated list of domains that can be used for hosting images and CSS. See the documentation for details on Assets.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'customhead',
    'category'    => 'Config',
    'label'       => 'Global customhead',
    'description' => 'Anything added here will be added to the end of the document head on all pages',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'customfoot',
    'category'    => 'Config',
    'label'       => 'Global customfoot',
    'description' => 'Anything added here will be added to the end of the document foot on all pages',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'multilanguage',
    'category'    => 'Config',
    'label'       => 'Multilanguage',
    'description' => 'This will enable additional multilanguage features if set to YES. Please run SETUP after changing this option.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'multilanguage-default',
    'category'    => 'Config',
    'label'       => 'Default Section',
    'description' => 'Default sub-section for this site',
    'type'        => 'select',
    'default'     => 'en',
    'options'     => implode(',', Jojo::selectAssoc("SELECT lc_code, lc_code as name FROM {lang_country} ORDER BY `lc_code`"))
);

$_options[] = array(
    'id'          => 'contentcache',
    'category'    => 'Config',
    'label'       => 'Content cache',
    'description' => 'If enabled, HTML content will be cached for a period of time to reduce server CPU and loading time for visitors.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'columnbreaks',
    'category'    => 'Config',
    'label'       => 'Columns filter',
    'description' => 'If enabled, content will be checked for columnbreak filters and split into fluid spans.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'contentcachetime',
    'category'    => 'Config',
    'label'       => 'Content cache time',
    'description' => 'The maximum amount of time in seconds content will be cached for, if CONTENTCACHE is enabled. Default 8 hours.',
    'type'        => 'integer',
    'default'     => 28800,
    'options'     => ''
);

$_options[] = array(
    'id'          => 'contentcachetime_resources',
    'category'    => 'Config',
    'label'       => 'Resources cache time',
    'description' => 'The maximum amount of time in seconds images, js and css will be cached for. Default 7 days.',
    'type'        => 'integer',
    'default'     => 604800,
    'options'     => ''
);

$_options[] = array(
    'id'          => 'servertimezone',
    'category'    => 'Config',
    'label'       => 'Server timezone',
    'description' => 'eg 12 for New Zealand. This option is used by some plugins to autocalculate times correctly for users if the server is not in their timezone.',
    'type'        => 'integer',
    'default'     => '0',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'sitetimezone',
    'category'    => 'Config',
    'label'       => 'Site Timezone',
    'description' => 'The timezone of the site will be deployed in. Causes all times (modified, go live etc) to be calculated in this timezone.',
    'type'        => 'text',
    'default'     => 'Pacific/Auckland',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'php_errors',
    'category'    => 'Config',
    'label'       => 'PHP Errors',
    'description' => 'If enabled, PHP warnings and errors will be displayed, even when DEBUG mode is off',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'dev_domains',
    'category'    => 'Config',
    'label'       => 'Development domains',
    'description' => 'A newline separated list of local development domains. Certain functions of Jojo will be disabled (such as pinging external services) when you are developing on a test server. Please include http://',
    'type'        => 'textarea',
    'default'     => "http://localhost\nhttp://127.0.0.1",
    'options'     => ''
);

$_options[] = array(
    'id'          => 'captcha_num_chars',
    'category'    => 'Config',
    'label'       => 'CAPTCHA numer of characters',
    'description' => 'Number of characters to display on the CAPTCHA image',
    'type'        => 'integer',
    'default'     => '3',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'templateengine',
    'category'    => 'Config',
    'label'       => 'Template Engine',
    'description' => 'Which template engine to use when rendering pages. Smarty is the older one, Dwoo is newer and faster but some older templates may not work in it.',
    'type'        => 'radio',
    'default'     => 'dwoo',
    'options'     => 'dwoo,smarty'
);

$_options[] = array(
    'id'          => 'lowercase_internalurl',
    'category'    => 'Config',
    'label'       => 'Internal URLs lowercase',
    'description' => 'Force lowercase of internal URLs',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);


/* Site */

$_options[] = array(
    'id'          => 'shorttitle',
    'category'    => 'Site',
    'label'       => 'Short Title',
    'description' => 'If there is a logical abbreviation for the site, this can be used instead of the full title where space is limited.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'sitetitle',
    'category'    => 'Site',
    'label'       => 'Site Title',
    'description' => 'The site title is displayed in the title bar of all pages, and is the default for outgoing emails and communications from the site. This should be the name of the website or business.',
    'type'        => 'text',
    'default'     => 'SITETITLE',
    'options'     => ''
);


$_options[] = array(
    'id'          => 'site_logo',
    'category'    => 'Site',
    'label'       => 'Site Logo',
    'description' => 'the url for the site logo',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'site_geolocation',
    'category'    => 'Site',
    'label'       => 'Geo Coordinates',
    'description' => 'the latitude,longitude of this location (if applicable)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'site_street_address',
    'category'    => 'Site',
    'label'       => 'Street Address',
    'description' => 'Street address of this location (if applicable)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'site_locality',
    'category'    => 'Site',
    'label'       => 'Locality',
    'description' => 'Locality/Suburb/City of this location (if applicable)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'site_region',
    'category'    => 'Site',
    'label'       => 'Region',
    'description' => 'Region/State of this location (if applicable)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'site_postal_code',
    'category'    => 'Site',
    'label'       => 'Post Code',
    'description' => 'Post Code of this location (if applicable)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'site_country_name',
    'category'    => 'Site',
    'label'       => 'Country',
    'description' => 'Country of this location (if applicable)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'site_email',
    'category'    => 'Site',
    'label'       => 'Email',
    'description' => 'Publishable contact email for the site (if applicable)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'site_phone_number',
    'category'    => 'Site',
    'label'       => 'Phone Number',
    'description' => 'Publishable contact phone number for the site (if applicable)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'site_fax_number',
    'category'    => 'Site',
    'label'       => 'Fax Number',
    'description' => 'Publishable contact fax number for the site (if applicable)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

/* System */

$_options[] = array(
    'id'          => 'last_maintenance',
    'category'    => 'System',
    'label'       => 'Last maintenance time',
    'description' => 'The date/time when auto-maintenance was last run on the system',
    'type'        => 'integer',
    'default'     => '1',
    'options'     => ''
);


/* Navigation */

$_options[] = array(
    'id'          => 'breadcrumbs_sep',
    'category'    => 'Navigation',
    'label'       => 'Breadcrumb separation character',
    'description' => 'Default is to use Bootstrap styling which includes its own separator. Include a custom separator here if needed',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'nav_mainnav',
    'category'    => 'Navigation',
    'label'       => 'Mainnav levels',
    'description' => 'How many levels of sub-navigation to include below the mainnav. 0 = just the top level. Disables separate subnav if >0 ',
    'type'        => 'integer',
    'default'     => '0',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'nav_subnav',
    'category'    => 'Navigation',
    'label'       => 'Subnav levels',
    'description' => 'How many levels of sub-navigation to include below the subnav. 0 = just the top level. Disabled if mainnav not 0',
    'type'        => 'integer',
    'default'     => '2',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'nav_footernav',
    'category'    => 'Navigation',
    'label'       => 'Footernav levels',
    'description' => 'How many levels of sub-navigation to include below the footernav. 0 = just the top level',
    'type'        => 'integer',
    'default'     => '0',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'use_secondary_nav',
    'category'    => 'Navigation',
    'label'       => 'Use secondary navigation',
    'description' => 'Enables / disables the Secondary Nav field on Edit pages. It is recommended this is set to NO unless it is specifically used by the site. Please run SETUP after changing this option.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

/* Images */

$_options[] = array(
    'id'          => 'jpeg_quality',
    'category'    => 'Images',
    'label'       => 'JPEG Quality',
    'description' => 'This is the quality percentage used when jpeg images are resized (1-100). Higher quality images are larger and slower to download.',
    'type'        => 'integer',
    'default'     => '85',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'image_sharpen',
    'category'    => 'Images',
    'label'       => 'Sharpen',
    'description' => 'Sharpen images after auto-resizing. 20=light, 10=aggressive, 0=none',
    'type'        => 'integer',
    'default'     => '18',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'image_padbackground',
    'category'    => 'Images',
    'label'       => 'Pad Background',
    'description' => 'Background colour for padded images as r,g,b -  integers between 0 and 255 or hexadecimals between 0x00 and 0xFF',
    'type'        => 'text',
    'default'     => '0xFF,0xFF,0xFF',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'max_imageupload_size',
    'category'    => 'Images',
    'label'       => 'Maximum Image Upload Size',
    'description' => 'sets the maximum image upload size',
    'type'        => 'text',
    'default'     => '3000',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'allowed_imageupload_extensions',
    'category'    => 'Images',
    'label'       => 'Image Upload Types',
    'description' => 'sets the allowed image upload file types - comma separated list of file extensions',
    'type'        => 'text',
    'default'     => 'jpg,gif,png,jpeg',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'image_filters',
    'category'    => 'Images',
    'label'       => 'Image Filters',
    'description' => 'A newline separated list of php imagefilters in the format [name(a-z)]:[image_filter name],[arg1],[arg2].. e.g. blueduo:IMG_FILTER_DUOTONE,0,121,193 images can then be called by url including the filter name images/[resize][filtername]/[filepath] e.g. images/w220blueduo/logo.png',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

/* HTML Editor */

$_options[] = array(
    'id'          => 'preferrededitor',
    'category'    => 'HTML Editor',
    'label'       => 'Preferred Editor',
    'description' => 'The default content editor for new pages.',
    'type'        => 'radio',
    'default'     => 'wysiwyg',
    'options'     => 'bbcode,wysiwyg'
);

$_options[] = array(
    'id'          => 'pseudobreaks',
    'category'    => 'HTML Editor',
    'label'       => 'Convert // pseudo-breaks in headings etc to <br />',
    'description' => 'if yes headings will be checked for //, converted for inline use and stripped for SEO titles',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'wysiwyg',
    'category'    => 'HTML Editor',
    'label'       => 'WYSIWYG Editor',
    'description' => 'If there are several WYSIWYG editors available, this option sets the preference for the site. Currently, only XINHA is available.',
    'type'        => 'radio',
    'default'     => 'xinha',
    'options'     => 'xinha'
);

$_options[] = array(
    'id'          => 'wysiwyg_style',
    'category'    => 'HTML Editor',
    'label'       => 'WYSIWYG Editor Style',
    'description' => 'Show the WYSIWYG editor inline, or as a popup window (does not apply to BB Editor).',
    'type'        => 'radio',
    'default'     => 'inline',
    'options'     => 'inline,popup'
);

$_options[] = array(
    'id'          => 'xinha_strip_href',
    'category'    => 'HTML Editor',
    'label'       => 'Strip Base Href in Xinha',
    'description' => 'if yes the baseHref will be removed from links. that means you will get relative links, not absolute-links.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'xinha_allowstyling',
    'category'    => 'HTML Editor',
    'label'       => 'Allow font styling in Xinha',
    'description' => 'if yes the editor wil allow the use of font face/size/colour styling.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'xinha_viewtype',
    'category'    => 'HTML Editor',
    'label'       => 'Default image/file library view',
    'description' => 'Set the default view for image and file libraries to either thumbnail or plain list (much faster for large libraries)',
    'type'        => 'radio',
    'default'     => 'thumbview',
    'options'     => 'thumbview,listview'
);

$_options[] = array(
    'id'          => 'max_fileupload_size',
    'category'    => 'HTML Editor',
    'label'       => 'Maximum File Upload Size',
    'description' => 'sets the maximum file upload size',
    'type'        => 'text',
    'default'     => '5000',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'allowed_fileupload_extensions',
    'category'    => 'HTML Editor',
    'label'       => 'File Upload Types',
    'description' => 'sets the allowed file link file types - comma separated list of file extensions',
    'type'        => 'text',
    'default'     => 'jpg,jpeg,gif,pdf,ip,txt,doc,docx,ppt,pptx,psd,png,html,swf,mp3,mp4,xml,xls',
    'options'     => ''
);


/* CSS */

$_options[] = array(
    'id'          => 'css',
    'category'    => 'CSS',
    'label'       => 'CSS',
    'description' => 'Any additional CSS required by the site can be added here. This may be easier and quicker than editing the CSS file and uploading via FTP.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'css-print',
    'category'    => 'CSS',
    'label'       => 'CSS for print',
    'description' => 'Any additional CSS specifically for printouts required by the site can be added here. This may be easier and quicker than editing the CSS file and uploading via FTP.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'css-handheld',
    'category'    => 'CSS',
    'label'       => 'CSS for handhelds',
    'description' => 'Any additional CSS specifically for handheld devices and PDAs required by the site can be added here. This may be easier and quicker than editing the CSS file and uploading via FTP.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'css-email',
    'category'    => 'CSS',
    'label'       => 'Inline CSS for email',
    'description' => 'CSS styles to be applied to emails',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'css_typekit',
    'category'    => 'CSS',
    'label'       => 'Typekit ID',
    'description' => 'Typekit ID for loading TypeKit webfonts (inserts asynchronous loading script in the head). use .wf-loading css to hide FOUT.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'css_imports',
    'category'    => 'CSS',
    'label'       => 'Server side CSS Imports',
    'description' => 'Have Jojo include CSS @imports on the server to save the browser using extra HTTP requests.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'normalize_cssreset',
    'category'    => 'CSS',
    'label'       => 'Normalize CSS Reset',
    'description' => 'Add normalize.css (reset, media etc) to style.css',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_variables',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Base',
    'description' => 'Bootstap\'s base files: variables, mixins, scaffolding and utilities.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_scaffolding_grid',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Grid System',
    'description' => 'A responsive mobile-first grid system.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_bass_type',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Headings, body, etc',
    'description' => 'Typography formatting for headings, lists, code etc',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_bass_labels',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Labels and badges',
    'description' => 'Highlight items by adding a badge or label to links, navs, and more',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_bass_tables',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Tables',
    'description' => 'Table formatting - used by Cart etc. Can be combined with Panels',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_bass_forms',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Forms & Buttons',
    'description' => 'Form element and button formatting - used by Contact, Cart etc',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);


$_options[] = array(
    'id'          => 'tbootstrap_bass_media',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Media',
    'description' => 'A layout contruct for snippets - used by Article indexes, Search results etc',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_bass_sprites',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Icons',
    'description' => 'Glyphicons icon set',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_components_buttongroups',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Button groups',
    'description' => 'Group a series of buttons together on a single line with the button group. Add on optional radio and checkbox style behavior with the Buttons JS option.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_components_navs',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Navs, tabs, and pills',
    'description' => 'Include Twitter Bootstap\'s navs.less file',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_components_navbar',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Navbar',
    'description' => 'Include Twitter Bootstap\'s navbar.less file',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_components_breadcrumbs',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Breadcrumbs',
    'description' => 'Breadcrumb formatting, with CSS separators',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_components_pagination',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Pagination',
    'description' => 'Pagination styling for Articles etc. Includes standard and "Pager" formats',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_components_thumbnails',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Thumbnails',
    'description' => 'Display grids of images, videos, text, and more.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_components_alerts',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Alerts',
    'description' => 'Include Twitter Bootstap\'s alerts.less file',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_components_progressbars',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Progress bars',
    'description' => 'Include Twitter Bootstap\'s progress-bars.less file',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_components_herounit',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Hero unit',
    'description' => 'Now called a Jumbotron.. A lightweight, flexible component that can optionally extend the entire viewport to showcase key content on your site.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_miscellaneous_listgroups',
    'category'    => 'CSS',
    'label'       => 'Bootstrap List Groups',
    'description' => 'Advanced list styling - can be combined with Panels',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_miscellaneous_panels',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Panels',
    'description' => 'Block content formatting - also used for accordians (with Collapse)',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_miscellaneous_wells',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Wells',
    'description' => 'A simple content block style.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_miscellaneous_close',
    'category'    => 'CSS',
    'label'       => 'Bootstrap Close icon',
    'description' => 'Close X icon for use with Modals, Alerts etc',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

/* JS */

$_options[] = array(
    'id'          => 'googleajaxlibs',
    'category'    => 'JS',
    'label'       => 'Google Hosted Javascript',
    'description' => 'Load jQuery from the Google Ajax Librarys site.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'modernizr',
    'category'    => 'JS',
    'label'       => 'Use Modernizr',
    'description' => 'Add modernizr script classes to html tag. Yes uses v2.6.2 minimal included in Jojo. Custom uses modernizr.min.js in the theme/external directory',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no,custom'
);

$_options[] = array(
    'id'          => 'jojo_corejs',
    'category'    => 'JS',
    'label'       => 'Jojo Core JS',
    'description' => "Include Jojo Core js functions.",
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'jojo_corefrajax',
    'category'    => 'JS',
    'label'       => 'Jojo Core Frajax',
    'description' => "Include Jojo Core frajax functions.",
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);
$_options[] = array(
    'id'          => 'jquery_head',
    'category'    => 'JS',
    'label'       => 'jQuery in head',
    'description' => "Load jQuery from the head (slows page load but required if in-page scripts are used on the site), foot (no), or don't load it at all (neither).",
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no,neither'
);

$_options[] = array(
    'id'          => 'jquery_version',
    'category'    => 'JS',
    'label'       => 'jQuery version',
    'description' => 'Which version of jQ to use.',
    'type'        => 'radio',
    'default'     => '1.9.1',
    'options'     => '1.7.2,1.9.1,1.11.1'
);

$_options[] = array(
    'id'          => 'jquery_touch',
    'category'    => 'JS',
    'label'       => 'jQuery Mobile Touch',
    'description' => 'Load jQuery Mobile touch event code (for triggering swipe and tap events etc). Requires jQuery 1.8+',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'jquery_ui',
    'category'    => 'JS',
    'label'       => 'jQuery UI',
    'description' => 'Load jQuery UI code.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'jquery_useanytime',
    'category'    => 'JS',
    'label'       => 'jQuery Anytime Datepicker',
    'description' => 'Load jQuery Anytime for datepicker popups.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'commonjs_head',
    'category'    => 'JS',
    'label'       => 'Common.js in head',
    'description' => 'Load common javascript file from the head (slows page load but required if in-page scripts are used on the site).',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no,neither'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_modal',
    'category'    => 'JS',
    'label'       => 'Bootstrap Modals',
    'description' => 'Include Twitter Bootstap\'s bootstrap-modal.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_dropdown',
    'category'    => 'JS',
    'label'       => 'Bootstrap Dropdowns',
    'description' => 'Include Twitter Bootstap\'s bootstrap-dropdown.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_scrollspy',
    'category'    => 'JS',
    'label'       => 'Bootstrap Scrollspy',
    'description' => 'Include Twitter Bootstap\'s bootstrap-scrollspy.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_tab',
    'category'    => 'JS',
    'label'       => 'Bootstrap Togglable tabs',
    'description' => 'Include Twitter Bootstap\'s bootstrap-tab.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_tooltip',
    'category'    => 'JS',
    'label'       => 'Bootstrap Tooltips',
    'description' => 'Include Twitter Bootstap\'s bootstrap-tooltip.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_popover',
    'category'    => 'JS',
    'label'       => 'Bootstrap Popovers',
    'description' => 'Include Twitter Bootstap\'s bootstrap-popover.js file into common.js, this will also include  Tooltips',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_affix',
    'category'    => 'JS',
    'label'       => 'Bootstrap Affix plugin',
    'description' => 'Include Twitter Bootstap\'s bootstrap-affix.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_alert',
    'category'    => 'JS',
    'label'       => 'Bootstrap Alert messages',
    'description' => 'Include Twitter Bootstap\'s bootstrap-alert.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_button',
    'category'    => 'JS',
    'label'       => 'Bootstrap Buttons',
    'description' => 'Include Twitter Bootstap\'s bootstrap-button.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_collapse',
    'category'    => 'JS',
    'label'       => 'Bootstrap Collapse',
    'description' => 'Include Twitter Bootstap\'s bootstrap-collapse.js file into common.js. This will automatically be included if CSS Navbar and responsive navbar are included',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_carousel',
    'category'    => 'JS',
    'label'       => 'Bootstrap Carousel',
    'description' => 'Include Twitter Bootstap\'s bootstrap-carousel.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'tbootstrap_js_typeahead',
    'category'    => 'JS',
    'label'       => 'Bootstrap Typeahead',
    'description' => 'Include Twitter Bootstap\'s bootstrap-typeahead.js file into common.js',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);


/* Contacts */

$_options[] = array(
    'id'          => 'fromaddress',
    'category'    => 'Contacts',
    'label'       => 'From Address',
    'description' => 'The website will send out various emails - such as followups to comments, and site reports. This address is used when sending mail from the site.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'fromname',
    'category'    => 'Contacts',
    'label'       => 'From Name',
    'description' => 'The name that is used for sending out mail from the website.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'webmasteraddress',
    'category'    => 'Contacts',
    'label'       => 'Webmaster Address',
    'description' => 'The email address of the webmaster. Is displayed on 404 pages and other places on the site. It is usually obfuscated to prevent spam, though some plugins may not do this.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'webmastername',
    'category'    => 'Contacts',
    'label'       => 'Webmaster Name',
    'description' => 'The full name of the webmaster. Is displayed on 404 pages and other places on the site.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'developer',
    'category'    => 'Contacts',
    'label'       => 'Developer',
    'description' => 'The name of the company developing the site. Used in meta data.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'contactaddress',
    'category'    => 'Contacts',
    'label'       => 'Contact address',
    'description' => 'The email address of the person who will be receiving any enquiries from the site. If empty, the values in FROM ADDRESS or WEBMASTER ADDRESS will be used instead.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'contactname',
    'category'    => 'Contacts',
    'label'       => 'Contact name',
    'description' => 'The name of the person who will be receiving any enquiries from the site. If empty, the values in FROM NAME or WEBMASTER NAME will be used instead.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

/* Google Analytics */
$_options[] = array(
    'id'          => 'analyticscode',
    'category'    => 'Google Analytics',
    'label'       => 'Analytics Code',
    'description' => 'The number provided by Google Analytics for tracking site traffic - eg UA-XXXXX-X',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'analyticscodetype',
    'category'    => 'Google Analytics',
    'label'       => 'Analytics Code Type',
    'description' => 'The type of Google analytics code - universal, asynchronous or ga.js. If you use the async code, then it will be put into the head and the top/bottom option will be ignored.',
    'type'        => 'radio',
    'default'     => 'universal',
    'options'     => 'universal,async,ga'
);

$_options[] = array(
    'id'          => 'crossdomainanalytics',
    'category'    => 'Google Analytics',
    'label'       => 'Cross Domain Analytics',
    'description' => 'Allow Google analytics to track between http and https servers of the same domain, and between domains.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
  'id'          => 'analyticsposition',
  'category'    => 'Google Analytics',
  'label'       => 'Google analytics at the top or bottom',
  'description' => 'Default at the bottom, but when you need to track events within a page, you need Analytics at the top of the HTML',
  'type'        => 'radio',
  'default'     => 'bottom',
  'options'     => 'top,bottom'
);

/* Email */
$_options[] = array(
    'id'          => 'smtp_mail_enabled',
    'category'    => 'Email',
    'label'       => 'SMTP Email',
    'description' => 'Enables sending mail using SMTP. If enabled, please ensure other options for host, port etc are set.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'smtp_mail_host',
    'category'    => 'Email',
    'label'       => 'SMTP Email Hostname',
    'description' => 'SMTP Hostname to use for sending mail eg smtp.domain.com',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'smtp_mail_port',
    'category'    => 'Email',
    'label'       => 'SMTP Email Port',
    'description' => 'Port to connect to for sending SMTP mail.',
    'type'        => 'text',
    'default'     => '25',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'smtp_mail_user',
    'category'    => 'Email',
    'label'       => 'SMTP Email Username',
    'description' => 'Username, if required by the SMTP server.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'smtp_mail_pass',
    'category'    => 'Email',
    'label'       => 'SMTP Email Password',
    'description' => 'Password, if required by the SMTP server.',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

/* Security */

$_options[] = array(
    'id'          => 'defaultgroup',
    'category'    => 'Security',
    'label'       => 'Default group',
    'description' => 'If a user registers on the site, they are automatically added to this group. This is used to give some automatic extra permissions to registered users. Available groups: ' . implode(', ',Jojo::selectAssoc("SELECT groupid, groupid as name FROM {usergroups} ORDER BY `groupid`")),
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'users_require_unique_email',
    'category'    => 'Security',
    'label'       => 'Users require unique email address',
    'description' => 'When this option is enabled, all user accounts require a unique email address. When disabled, users can register multiple account s on one email address.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'password_cost',
    'category'    => 'Security',
    'label'       => 'Password Cost',
    'description' => 'The cost of hashing user passwords. Higher is harder to crack, but will cause a slight delay as users sign in',
    'type'        => 'text',
    'default'     => '10',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'password_email',
    'category'    => 'Security',
    'label'       => 'Email Passwords',
    'description' => 'If enabled, new passwords set using the Change Password form will be emailed to the user.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

/* SEO */

$_options[] = array(
    'id'          => 'title_separator',
    'category'    => 'SEO',
    'label'       => 'Title separator',
    'description' => 'This option controls the character used to separate the page title and the company name/branding. Usually a pipe or dash. Do include spaces either side of the character if you need them.',
    'type'        => 'text',
    'default'     => ' | ',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'titlebranding',
    'category'    => 'SEO',
    'label'       => 'Title branding',
    'description' => 'The name of the site is automatically appended to titles of all pages. This can be placed at the start or end of the title - ie branding first, or branding last.',
    'type'        => 'radio',
    'default'     => 'last',
    'options'     => 'first,last'
);

$_options[] = array(
    'id'          => 'shorttitlebranding',
    'category'    => 'SEO',
    'label'       => 'Short title branding',
    'description' => 'If this option is set, the site title will be left off page titles, if adding it would cause the whole title to be longer than the length of a Google title. Enabling this is good for SEO, but can mean some inconsistencies in branding across the titles of the site.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'page_meta_keywords',
    'category'    => 'SEO',
    'label'       => 'Page-specific meta keywords',
    'description' => 'When this option is enabled, meta keywords can be edited via edit pages. Disable this option if you are not going to be editing meta keywords (or consider them worthless)',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'ogdata',
    'category'    => 'SEO',
    'label'       => 'OpenGraph meta tags',
    'description' => 'When this option is enabled meta og:: tags, used by Facebook et al, will be included in the head',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'robots_odp',
    'category'    => 'SEO',
    'label'       => 'Open Directory Project',
    'description' => 'If enabled, Search Engines use your description, etc instead of the Open Directory Project description if it exists.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'nofollow_list',
    'category'    => 'SEO',
    'label'       => 'Nofollow list',
    'description' => 'A newline separated list of domains that will be nofollowed. Any links within the site to any of the domains in this list will be automatically nofollowed. Please include http://',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'robots_ydir',
    'category'    => 'SEO',
    'label'       => 'Yahoo Directory',
    'description' => 'If enabled, Search Engines use your description, etc instead of the Yahoo Directory description if it exists.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'robots_assets',
    'category'    => 'SEO',
    'label'       => 'Allow asset access to bots',
    'description' => 'Set css, js, images, files and externals to Index on setup and not excluded by default in robots.txt (recommended by Google).',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

/* RSS */

$_options[] = array(
    'id'          => 'rss_num_items',
    'category'    => 'RSS',
    'label'       => 'Number of RSS items',
    'description' => 'The number of items to be displayed in the RSS feed (more will use more bandwidth))',
    'type'        => 'integer',
    'default'     => '15',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'rss_full_description',
    'category'    => 'RSS',
    'label'       => 'Full RSS Description',
    'description' => 'If YES, a full copy of the item is provided in the RSS feed. If NO, the RSS feed only includes content before the snip filter tag.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'rss_truncate',
    'category'    => 'RSS',
    'label'       => 'RSS default truncation',
    'description' => 'If Full Description is set to No above, truncate events with no embedded snip filter tag to this length',
    'type'        => 'integer',
    'default'     => '800',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'rss_source_link',
    'category'    => 'RSS',
    'label'       => 'Append source link to RSS feed',
    'description' => 'Appends a source link to the bottom of each item in the RSS feed. This is to ensure scraper sites are providing a link back to the original event.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'rss_sitedesc',
    'category'    => 'RSS',
    'label'       => 'Site description',
    'description' => 'A one sentence unique description of what the site is about. Included in RSS feeds.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => ''
);

/* Social Networking */

$_options[] = array(
    'id'          => 'facebook_link',
    'category'    => 'Social Networking',
    'label'       => 'Facebook link',
    'description' => 'the full url of your Facebook page',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'facebook_admins',
    'category'    => 'Social Networking',
    'label'       => 'Facebook Admin Ids',
    'description' => 'Comma separated list of Facebook Admin Ids (used for registering your site on your FB page)',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'googleplus_link',
    'category'    => 'Social Networking',
    'label'       => 'Google+ link',
    'description' => 'the full url of your Google+ page',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'linkedin_link',
    'category'    => 'Social Networking',
    'label'       => 'LinkedIn link',
    'description' => 'the full url of your LinkedIn page',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'twitter_id',
    'category'    => 'Social Networking',
    'label'       => 'Twitter ID',
    'description' => 'Your Twitter ID',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'youtube_link',
    'category'    => 'Social Networking',
    'label'       => 'YouTube link',
    'description' => 'the full url of your Youtube channel',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'instagram_link',
    'category'    => 'Social Networking',
    'label'       => 'Instagram link',
    'description' => 'the full url of your Instagram channel',
    'type'        => 'text',
    'default'     => '',
    'options'     => ''
);

$_options[] = array(
    'id'          => 'allow_email_login',
    'category'    => 'Security',
    'label'       => 'Allow login with email',
    'description' => 'If YES, allows users to login with their email address or username.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'mobile_site',
    'category'    => 'Config',
    'label'       => 'Mobile site enabled',
    'description' => 'Serve .mob.tpl files (if available) to mobile browsers. Note your theme will need to support this.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

$_options[] = array(
    'id'          => 'initialscale',
    'category'    => 'Config',
    'label'       => 'Initial Scale',
    'description' => 'Include "Initial Scale = 1" in the head. Set to yes for sites you don\'t want scaled to fit on smaller screens.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no'
);

