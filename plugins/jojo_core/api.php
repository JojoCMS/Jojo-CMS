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

/* Register URI patterns */
Jojo::registerURI("css/[file:([^\/]*)$]",                                            'Jojo_Plugin_Core_Css');             // "css/something.css" for css files
Jojo::registerURI("json/[file:([^\/]*)$]",                                           'Jojo_Plugin_Core_Json');            // "json/something.php" for json requests
Jojo::registerURI("actions/[file:([^\/]*)$]",                                        'Jojo_Plugin_Core_Action');          // "actions/something.php" for frajax requests
Jojo::registerURI("images/[file:(.*)$]",                                             'Jojo_Plugin_Core_Image');           // "images/somewhere/something.jpg" for image files
Jojo::registerURI("js/[file:(.*)$]",                                                 'Jojo_Plugin_Core_Js');              // "js/something.js" for javascript files
Jojo::registerURI("external/[file:(.*)$]",                                           'Jojo_Plugin_Core_External');        // "external/somewhere/something.ext" for external files
Jojo::registerURI("downloads/[file:(.*)$]",                                          'Jojo_Plugin_Core_Download');        // "download/somewhere/something.ext" for user uploaded files
Jojo::registerURI("files/[file:(.*)$]",                                              'Jojo_Plugin_Core_File');           // "files/somewhere/something.ext" for custom files for a plugin, eg flash
Jojo::registerURI("forgot-password/reset/[reset:([a-f0-9]{40})]",                    'Jojo_Plugin_Forgot_password'); // "forgot-password/reset/21b618b7252f6dbc6744200ced0c44ce3e2664da/" - 40 chars of hex
Jojo::registerURI("forgot-password/reset/[reset:([a-z0-9]{16})]",                    'Jojo_Plugin_Forgot_password'); // "forgot-password/reset/sga4v6wqg6ij65jd/" - a shorter version 16 chars of alpha numeric
Jojo::registerURI("login/[redirect:(.*)]",                                           'Jojo_Plugin_Login');           // "login/page-to-redirect-to-on-success/" for login page

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
        'yesno'         => 'Yes or No',
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
        'Jojo_Plugin_User_profile'        => 'Core - User Profile',
        );

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
    'category'    => 'Site',
    'label'       => 'Enable GZip',
    'description' => 'Gzipping website content is a way of compressing HTML pages so they are faster to download. This should usually be enabled, but is not supported on some web hosts.',
    'type'        => 'radio',
    'default'     => '0',
    'options'     => '0,1',
);


$_options[] = array(
    'id'          => 'googleajaxlibs',
    'category'    => 'Site',
    'label'       => 'Google Hosted Javascript',
    'description' => 'Load jQuery from the Google Ajax Librarys site.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'doctype',
    'category'    => 'Site',
    'label'       => 'Doctype',
    'description' => 'Declare Doctype as XHTML or HTML5.',
    'type'        => 'radio',
    'default'     => 'xhtml',
    'options'     => 'xhtml,html5',
);

$_options[] = array(
    'id'          => 'boilerplate_htmltag',
    'category'    => 'Site',
    'label'       => 'Bolierplate Html tag',
    'description' => 'Add browser conditionals to html tag',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'boilerplate_cssreset',
    'category'    => 'Site',
    'label'       => 'Bolierplate CSS Reset',
    'description' => 'Add Boilerplate CSS (reset, media etc) to style.css',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'modernizr',
    'category'    => 'Site',
    'label'       => 'Use Modernizr',
    'description' => 'Add modernizr script classes to html tag',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'jquery_head',
    'category'    => 'Site',
    'label'       => 'jQuery in head',
    'description' => 'Load jQuery from the head (slows page load but required if in-page scripts are used on the site).',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'commonjs_head',
    'category'    => 'Site',
    'label'       => 'Common.js in head',
    'description' => 'Load common javascript file from the head (slows page load but required if in-page scripts are used on the site).',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'shorttitle',
    'category'    => 'Site',
    'label'       => 'Short Title',
    'description' => 'If there is a logical abbreviation for the site, this can be used instead of the full title where space is limited.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'sitetitle',
    'category'    => 'Site',
    'label'       => 'Site Title',
    'description' => 'The site title is displayed in the title bar of all pages, and is the default for outgoing emails and communications from the site. This should be the name of the website or business.',
    'type'        => 'text',
    'default'     => 'SITETITLE',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'siteurl',
    'category'    => 'Site',
    'label'       => 'Site URL',
    'description' => 'This option is set automatically. This is the URL that is used for the site. Where several domains are available for a site, they will all be redirected to the URL specified here. NO TRAILING SLASH.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'secureurl',
    'category'    => 'Site',
    'label'       => 'Secure URL',
    'description' => 'If there is a SSL version of the site, enter the URL here, including the https:// but not the trailing slash.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'assetdomains',
    'category'    => 'Site',
    'label'       => 'Asset domains',
    'description' => 'A new-line seperated list of domains that can be used for hosting images and CSS. See the documentation for details on Assets.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'secureurl',
    'category'    => 'Site',
    'label'       => 'Secure URL',
    'description' => 'If the site has a SSL version, enter the URL here. DO include the https:// but not the trailing slash. eg https://www.domain.com',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'multilanguage',
    'category'    => 'Config',
    'label'       => 'Multilanguage',
    'description' => 'This will enable additional multilanguage features if set to YES. Please run SETUP after changing this option.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'multilanguage-default',
    'category'    => 'Config',
    'label'       => 'Default Section',
    'description' => 'Default sub-section for this site',
    'type'        => 'select',
    'default'     => 'en',
    'options'     => implode(',', Jojo::selectAssoc("SELECT lc_code, lc_code as name FROM {lang_country} ORDER BY `lc_code`")),
);

$_options[] = array(
    'id'          => 'contentcache',
    'category'    => 'Config',
    'label'       => 'Content cache',
    'description' => 'If enabled, HTML content will be cached for a period of time to reduce server CPU and loading time for visitors.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'contentcachetime',
    'category'    => 'Config',
    'label'       => 'Content cache time',
    'description' => 'The maximum amount of time in seconds content will be cached for, if CONTENTCACHE is enabled. Default 8 hours.',
    'type'        => 'integer',
    'default'     => 28800,
    'options'     => '',
);

$_options[] = array(
    'id'          => 'servertimezone',
    'category'    => 'Config',
    'label'       => 'Server timezone',
    'description' => 'eg 12 for New Zealand. This option is used by some plugins to autocalculate times correctly for users if the server is not in their timezone.',
    'type'        => 'integer',
    'default'     => '0',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'sitetimezone',
    'category'    => 'Config',
    'label'       => 'Site Timezone',
    'description' => 'The timezone of the site will be deployed in. Causes all times (modified, go live etc) to be calculated in this timezone.',
    'type'        => 'text',
    'default'     => 'Pacific/Auckland',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'php_errors',
    'category'    => 'Config',
    'label'       => 'PHP Errors',
    'description' => 'If enabled, PHP warnings and errors will be displayed, even when DEBUG mode is off',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'last_maintenance',
    'category'    => 'System',
    'label'       => 'Last maintenance time',
    'description' => 'The date/time when auto-maintenance was last run on the system',
    'type'        => 'integer',
    'default'     => '1',
    'options'     => ''
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
    'id'          => 'jpeg_quality',
    'category'    => 'Config',
    'label'       => 'JPEG Quality',
    'description' => 'This is the quality percentage used when jpeg images are resized (1-100). Higher quality images are larger and slower to download.',
    'type'        => 'integer',
    'default'     => '85',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'use_secondary_nav',
    'category'    => 'Config',
    'label'       => 'Use secondary navigation',
    'description' => 'Enables / disables the Secondary Nav field on Edit pages. It is recommended this is set to NO unless it is specifically used by the site. Please run SETUP after changing this option.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'captcha_num_chars',
    'category'    => 'Config',
    'label'       => 'CAPTCHA numer of characters',
    'description' => 'Number of characters to display on the CAPTCHA image',
    'type'        => 'integer',
    'default'     => '3',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'templateengine',
    'category'    => 'Config',
    'label'       => 'Template Engine',
    'description' => 'Which template engine to use when rendering pages. Smarty is the older one, Dwoo is newer and faster but some older templates may not work in it.',
    'type'        => 'radio',
    'default'     => 'dwoo',
    'options'     => 'dwoo,smarty',
);

/* Navigation */

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

// HTML Editor
$_options[] = array(
    'id'          => 'preferrededitor',
    'category'    => 'HTML Editor',
    'label'       => 'Preferred Editor',
    'description' => 'The default content editor for new pages.',
    'type'        => 'radio',
    'default'     => 'bbcode',
    'options'     => 'bbcode,wysiwyg',
);

$_options[] = array(
    'id'          => 'wysiwyg',
    'category'    => 'HTML Editor',
    'label'       => 'WYSIWYG Editor',
    'description' => 'If there are several WYSIWYG editors available, this option sets the preference for the site. Currently, only XINHA is available.',
    'type'        => 'radio',
    'default'     => 'xinha',
    'options'     => 'xinha',
);

$_options[] = array(
    'id'          => 'xinha_strip_href',
    'category'    => 'HTML Editor',
    'label'       => 'Strip Base Href in Xinha',
    'description' => 'if yes the baseHref will be removed from links. that means you will get relative links, not absolute-links.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'xinha_allowstyling',
    'category'    => 'HTML Editor',
    'label'       => 'Allow font styling in Xinha',
    'description' => 'if yes the editor wil allow the use of font face/size/colour styling.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'xinha_viewtype',
    'category'    => 'HTML Editor',
    'label'       => 'Default image/file library view',
    'description' => 'Set the default view for image and file libraries to either thumbnail or plain list (much faster for large libraries)',
    'type'        => 'radio',
    'default'     => 'thumbview',
    'options'     => 'thumbview,listview',
);

$_options[] = array(
    'id'          => 'max_fileupload_size',
    'category'    => 'HTML Editor',
    'label'       => 'Maximum File Upload Size',
    'description' => 'sets the maximum file upload size',
    'type'        => 'text',
    'default'     => '5000',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'max_imageupload_size',
    'category'    => 'HTML Editor',
    'label'       => 'Maximum Image Upload Size',
    'description' => 'sets the maximum image upload size',
    'type'        => 'text',
    'default'     => '2000',
    'options'     => '',
);

// CSS
$_options[] = array(
    'id'          => 'css',
    'category'    => 'CSS',
    'label'       => 'CSS',
    'description' => 'Any additional CSS required by the site can be added here. This may be easier and quicker than editing the CSS file and uploading via FTP.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'css-print',
    'category'    => 'CSS',
    'label'       => 'CSS for print',
    'description' => 'Any additional CSS specifically for printouts required by the site can be added here. This may be easier and quicker than editing the CSS file and uploading via FTP.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'css-handheld',
    'category'    => 'CSS',
    'label'       => 'CSS for handhelds',
    'description' => 'Any additional CSS specifically for handheld devices and PDAs required by the site can be added here. This may be easier and quicker than editing the CSS file and uploading via FTP.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
);

// Contacts
$_options[] = array(
    'id'          => 'fromaddress',
    'category'    => 'Contacts',
    'label'       => 'From Address',
    'description' => 'The website will send out various emails - such as followups to comments, and site reports. This address is used when sending mail from the site.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'fromname',
    'category'    => 'Contacts',
    'label'       => 'From Name',
    'description' => 'The name that is used for sending out mail from the website.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'webmasteraddress',
    'category'    => 'Contacts',
    'label'       => 'Webmaster Address',
    'description' => 'The email address of the webmaster. Is displayed on 404 pages and other places on the site. It is usually obfuscated to prevent spam, though some plugins may not do this.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'webmastername',
    'category'    => 'Contacts',
    'label'       => 'Webmaster Name',
    'description' => 'The full name of the webmaster. Is displayed on 404 pages and other places on the site.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'developer',
    'category'    => 'Contacts',
    'label'       => 'Developer',
    'description' => 'The name of the company developing the site. Used in meta data.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'contactaddress',
    'category'    => 'Contacts',
    'label'       => 'Contact address',
    'description' => 'The email address of the person who will be receiving any enquiries from the site. If empty, the values in FROM ADDRESS or WEBMASTER ADDRESS will be used instead.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'contactname',
    'category'    => 'Contacts',
    'label'       => 'Contact name',
    'description' => 'The name of the person who will be receiving any enquiries from the site. If empty, the values in FROM NAME or WEBMASTER NAME will be used instead.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

// Google Analytics
$_options[] = array(
    'id'          => 'analyticscode',
    'category'    => 'Google Analytics',
    'label'       => 'Analytics Code',
    'description' => 'The number provided by Google Analytics for tracking site traffic - eg UA-XXXXX-X',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'analyticscodetype',
    'category'    => 'Google Analytics',
    'label'       => 'Analytics Code Type',
    'description' => 'The type of Google analytics code - new asynchronous or ga.js. If you use the async code, then it will be put into the head and the top/bottom option will be ignored.',
    'type'        => 'radio',
    'default'     => 'ga',
    'options'     => 'async,ga',
);

$_options[] = array(
    'id'          => 'crossdomainanalytics',
    'category'    => 'Google Analytics',
    'label'       => 'Cross Domain Analytics',
    'description' => 'Allow Google analytics to track between http and https servers of the same domain, and between domains.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
  'id'          => 'analyticsposition',
  'category'    => 'Google Analytics',
  'label'       => 'Google analytics at the top or bottom',
  'description' => 'Default at the bottom, but when you need to track events within a page, you need Analytics at the top of the HTML',
  'type'        => 'radio',
  'default'     => 'bottom',
  'options'     => 'top,bottom',
);

// Email
$_options[] = array(
    'id'          => 'smtp_mail_enabled',
    'category'    => 'Email',
    'label'       => 'SMTP Email',
    'description' => 'Enables sending mail using SMTP. If enabled, please ensure other options for host, port etc are set.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'smtp_mail_host',
    'category'    => 'Email',
    'label'       => 'SMTP Email Hostname',
    'description' => 'SMTP Hostname to use for sending mail eg smtp.domain.com',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'smtp_mail_port',
    'category'    => 'Email',
    'label'       => 'SMTP Email Port',
    'description' => 'Port to connect to for sending SMTP mail.',
    'type'        => 'text',
    'default'     => '25',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'smtp_mail_user',
    'category'    => 'Email',
    'label'       => 'SMTP Email Username',
    'description' => 'Username, if required by the SMTP server.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'smtp_mail_pass',
    'category'    => 'Email',
    'label'       => 'SMTP Email Password',
    'description' => 'Password, if required by the SMTP server.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

// Security
$_options[] = array(
    'id'          => 'defaultgroup',
    'category'    => 'Security',
    'label'       => 'Default group',
    'description' => 'If a user registers on the site, they are automatically added to this group. This is used to give some automatic extra permissions to registered users. Available groups: ' . implode(', ',Jojo::selectAssoc("SELECT groupid, groupid as name FROM {usergroups} ORDER BY `groupid`")),
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'users_require_unique_email',
    'category'    => 'Security',
    'label'       => 'Users require unique email address',
    'description' => 'When this option is enabled, all user accounts require a unique email address. When disabled, users can register multiple account s on one email address.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
);

// SEO
$_options[] = array(
    'id'          => 'title_separator',
    'category'    => 'SEO',
    'label'       => 'Title separator',
    'description' => 'This option controls the character used to separate the page title and the company name/branding. Usually a pipe or dash. Do include spaces either side of the character if you need them.',
    'type'        => 'text',
    'default'     => ' | ',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'titlebranding',
    'category'    => 'SEO',
    'label'       => 'Title branding',
    'description' => 'The name of the site is automatically appended to titles of all pages. This can be placed at the start or end of the title - ie branding first, or branding last.',
    'type'        => 'radio',
    'default'     => 'last',
    'options'     => 'first,last',
);

$_options[] = array(
    'id'          => 'shorttitlebranding',
    'category'    => 'SEO',
    'label'       => 'Short title branding',
    'description' => 'If this option is set, the site title will be left off page titles, if adding it would cause the whole title to be longer than the length of a Google title. Enabling this is good for SEO, but can mean some inconsistencies in branding across the titles of the site.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'page_meta_keywords',
    'category'    => 'SEO',
    'label'       => 'Page-specific meta keywords',
    'description' => 'When this option is enabled, meta keywords can be edited via edit pages. Disable this option if you are not going to be editing meta keywords (or consider them worthless)',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'robots_opd',
    'category'    => 'SEO',
    'label'       => 'Open Directory Project',
    'description' => 'If enabled, Search Engines use your description, etc instead of the Open Directory Project description if it exists.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
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
    'options'     => 'yes,no',
);

/* RSS */

$_options[] = array(
    'id'          => 'rss_num_items',
    'category'    => 'RSS',
    'label'       => 'Number of RSS items',
    'description' => 'The number of items to be displayed in the RSS feed (more will use more bandwidth))',
    'type'        => 'integer',
    'default'     => '15',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'rss_full_description',
    'category'    => 'RSS',
    'label'       => 'Full RSS Description',
    'description' => 'If YES, a full copy of the item is provided in the RSS feed. If NO, the RSS feed only includes content before the snip filter tag.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'rss_truncate',
    'category'    => 'RSS',
    'label'       => 'RSS default truncation',
    'description' => 'If Full Description is set to No above, truncate events with no embedded snip filter tag to this length',
    'type'        => 'integer',
    'default'     => '800',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'rss_source_link',
    'category'    => 'RSS',
    'label'       => 'Append source link to RSS feed',
    'description' => 'Appends a source link to the bottom of each item in the RSS feed. This is to ensure scraper sites are providing a link back to the original event.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
);

$_options[] = array(
    'id'          => 'rss_sitedesc',
    'category'    => 'RSS',
    'label'       => 'Site description',
    'description' => 'A one sentence unique description of what the site is about. Included in RSS feeds.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
);

