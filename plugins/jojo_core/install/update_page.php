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

$default_td['page'] = array(
        'td_name' => "page",
        'td_primarykey' => "pageid",
        'td_displayfield' => "IF(pg_menutitle!='',pg_menutitle,pg_title)",
        'td_parentfield' => "pg_parent",
        'td_rolloverfield' => "IF(pg_menutitle!='',pg_title,'')",
        'td_orderbyfields' => "pg_order,pg_title",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "tree",
        'td_help' => "\"Edit pages\" is a core part of the site. Pages can be added and edited, and any changes will appear immediately after saving.<br/><br/>All pages must have a title. The title will be used as the main heading on the page, so it is important the title is relevant and contains the right keywords for the Search Engines. If the title is too long to fit on the menu, then enter a \"Menu Title\" as well, which will be used on the button. The \"SEO Title\" is a search engine optimised title for the blue bar at the top of the screen. It will often be the title that Google uses for it's listings.<br/><br/>Pages can be set to display on the menu, or can be linked to from other pages directly. A privacy policy page is a common example of a page that would not be displayed on the menu, but usually linked from the bottom of the page instead.<br/>",
        'td_golivefield' => "pg_livedate",
        'td_expiryfield' => "pg_expirydate",
        'td_activefield' => "pg_status",
        'td_languagefield' => "pg_language",
        'td_plugin' => "Core",
        'td_defaultpermissions' => "everyone.show=1\neveryone.view=1\nadmin.add=1\nadmin.edit=1\nadmin.delete=1",
    );


/* Content Tab */

// Page Heading Field
$default_fd['page']['pg_title'] = array(
        'fd_name' => "Page Heading",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "The heading is an important page element. As well as being the page H1 heading, it will become the default title, button text and URL if other fields are not specified.",
        'fd_order' => "2",
        'fd_tabname' => "Content",
    );

// Page URL Field
$default_fd['page']['pg_url'] = array(
        'fd_name' => "Page URL",
        'fd_type' => "internalurl",
        'fd_size' => "20",
        'fd_help' => "Set the URL for a page. Choose a URL that is logical and is unlikely to change. If this does change for any reason, be sure to setup a redirect to prevent losing traffic.",
        'fd_order' => "4",
        'fd_tabname' => "Content",
    );

// Body Content Field
$default_fd['page']['pg_body_code'] = array(
        'fd_name' => "Body Content",
        'fd_type' => "texteditor",
        'fd_options' => "pg_body",
        'fd_rows' => "10",
        'fd_cols' => "50",
        'fd_help' => "The page body content in BBCode format. If you prefer to use the WYSIWYG editor, be sure to leave this field blank.",
        'fd_order' => "5",
        'fd_tabname' => "Content",
    );

// Body Content Field
$default_fd['page']['pg_body'] = array(
        'fd_name' => "Body Content",
        'fd_type' => "hidden",
        'fd_showlabel' => "no",
        'fd_order' => "6",
        'fd_tabname' => "Content",
    );


/* Navigation Tab */

// Parent Page Field
$default_fd['page']['pg_parent'] = array(
        'fd_name' => "Parent Page",
        'fd_type' => "dbpagelist",
        'fd_options' => "page",
        'fd_default' => "0",
        'fd_help' => "If this is a sub-item of another page, the parent field indicates where this page will sit on the menu. Top level pages (eg Home) do not need a parent.",
        'fd_order' => "1",
        'fd_tabname' => "Navigation",
    );

// Order Field
$default_fd['page']['pg_order'] = array(
        'fd_name' => "Order",
        'fd_type' => "order",
        'fd_default' => "0",
        'fd_help' => "The order in which the page appears on the menu. A lower number means the page will appear near the top of the list.",
        'fd_order' => "2",
        'fd_tabname' => "Navigation",
    );

// Menu Title Field
$default_fd['page']['pg_menutitle'] = array(
        'fd_name' => "Menu Title",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_help' => "If the page heading is too long to fit on the menu buttons, enter some shorter text here. If this field is left blank, the heading text will be used by default.",
        'fd_order' => "3",
        'fd_tabname' => "Navigation",
    );

// Button Rollover Field
$default_fd['page']['pg_desc'] = array(
        'fd_name' => "Button Rollover",
        'fd_type' => "text",
        'fd_options' => "rollover",
        'fd_size' => "50",
        'fd_help' => "A one-sentence description of the page content. This appears as a tooltip on the navigation, and is helpful to users (and maybe search engines too). It is recommended that this field is completed.",
        'fd_order' => "4",
        'fd_tabname' => "Navigation",
    );

// Main Nav Field
$default_fd['page']['pg_mainnav'] = array(
        'fd_name' => "Main Nav",
        'fd_type' => "checkbox",
        'fd_options' => "yes\nno",
        'fd_default' => "yes",
        'fd_help' => "Will this page show on the main navigation?",
        'fd_order' => "5",
        'fd_tabname' => "Navigation",
    );

// Main Nav Intl Field
$default_fd['page']['pg_mainnavalways'] = array(
        'fd_name' => "Main Nav Intl",
        'fd_type' => "hidden",
        'fd_options' => "yes\nno",
        'fd_default' => "no",
        'fd_help' => "When Internationalisation (Multi Language) is on, show page on all menus.",
        'fd_order' => "6",
        'fd_tabname' => "Navigation",
    );

// 2nd Nav Field
$default_fd['page']['pg_secondarynav'] = array(
        'fd_name' => "2nd Nav",
        'fd_type' => "checkbox",
        'fd_options' => "yes\nno",
        'fd_default' => "no",
        'fd_help' => "Will this page show on the secondary navigation?",
        'fd_order' => "7",
        'fd_tabname' => "Navigation",
    );

// Breadcrumbs Field
$default_fd['page']['pg_breadcrumbnav'] = array(
        'fd_name' => "Breadcrumbs",
        'fd_type' => "checkbox",
        'fd_options' => "yes\nno",
        'fd_default' => "yes",
        'fd_help' => "Will this page show on the breadcrumb trail? (Not all sites have breadcrumb navigation)",
        'fd_order' => "8",
        'fd_tabname' => "Navigation",
    );

// Footer Nav Field
$default_fd['page']['pg_footernav'] = array(
        'fd_name' => "Footer Nav",
        'fd_type' => "checkbox",
        'fd_options' => "yes\nno",
        'fd_default' => "no",
        'fd_help' => "Will this page show on the footer navigation?",
        'fd_order' => "9",
        'fd_tabname' => "Navigation",
    );

// Sitemap Field
$default_fd['page']['pg_sitemapnav'] = array(
        'fd_name' => "Sitemap",
        'fd_type' => "checkbox",
        'fd_options' => "yes\nno",
        'fd_default' => "yes",
        'fd_help' => "Will this page show on the visitor sitemap? (assuming this plugin is installed)",
        'fd_order' => "10",
        'fd_tabname' => "Navigation",
    );


// Permissions Field
$default_fd['page']['pg_permissions'] = array(
        'fd_name' => "Permissions",
        'fd_type' => "permissions",
        'fd_order' => "18",
        'fd_tabname' => "Navigation",
    );

$o=0;
/* SEO Tab */

// SEO Title Field
$default_fd['page']['pg_seotitle'] = array(
        'fd_name' => "SEO Title",
        'fd_type' => "text",
        'fd_options' => "seotitle",
        'fd_size' => "60",
        'fd_help' => "Search Engine Optimised text for the title bar. Start with your preferred search phrase, and try to keep this within 70 characters",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// Meta Description Field
$default_fd['page']['pg_metadesc'] = array(
        'fd_name' => "Meta Description",
        'fd_type' => "textarea",
        'fd_options' => "metadescription",
        'fd_rows' => "3",
        'fd_cols' => "60",
        'fd_help' => "A good sales oriented description of the page for the Search Engine snippet. Try to keep this within 155 characters, as anything larger will be chopped from the snippet.",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// Language/Country Field
$default_fd['page']['pg_language'] = array(
        'fd_name' => "Language/Country",
        'fd_type' => "hidden",
        'fd_options' => "lang_country",
        'fd_default' => "en",
        'fd_help' => "The language/country this page is written for",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// HTML Language Field
$default_fd['page']['pg_htmllang'] = array(
        'fd_name' => "HTML Language",
        'fd_type' => "dblist",
        'fd_options' => "language",
        'fd_default' => "en",
        'fd_help' => "The language this page is written in (if not the default language for the site/section",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );


// META Keywords Field
$default_fd['page']['pg_metakeywords'] = array(
        'fd_name' => "META Keywords",
        'fd_type' => "hidden",
        'fd_rows' => "3",
        'fd_cols' => "60",
        'fd_help' => "A space separated list of keywords / phrases to help search engines index the site. If you leave this field empty, meta keywords are generated automatically, which is the recommended behaviour.",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// XML Sitemap Field
$default_fd['page']['pg_xmlsitemapnav'] = array(
        'fd_name' => "XML Sitemap",
        'fd_type' => "checkbox",
        'fd_options' => "yes:yes:pg_xmlsitemap_lastmod,pg_xmlsitemap_changefreq,pg_xmlsitemap_priority\nno",
        'fd_default' => "yes",
        'fd_help' => "Will this page show on the XML sitemap?",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// XML Sitemap show lastmod? Field
$default_fd['page']['pg_xmlsitemap_lastmod'] = array(
        'fd_name' => "XML Sitemap show lastmod?",
        'fd_type' => "radio",
        'fd_options' => "yes\nno",
        'fd_default' => "yes",
        'fd_help' => "XML Sitemap - show the lastmod date of the page? Some pages may be generated by a plugin and have dynamic content. Best to not show date for these",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// XML Sitemap ChangeFreq Field
$default_fd['page']['pg_xmlsitemap_changefreq'] = array(
        'fd_name' => "XML Sitemap ChangeFreq",
        'fd_type' => "list",
        'fd_options' => "active:default\nalways\nhourly\ndaily\nweekly\nmonthly\nyearly\nnever",
        'fd_help' => "XML Sitemap - how often the content changes - don't show field, or add the options",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// XML Sitemap Priority Field
$default_fd['page']['pg_xmlsitemap_priority'] = array(
        'fd_name' => "XML Sitemap Priority",
        'fd_type' => "list",
        'fd_options' => ":default\n1.0:1.0 highest priority\n0.9\n0.8\n0.7\n0.6\n0.5\n0.4\n0.3\n0.2\n0.1\n0.0:0.0 lowest priority",
        'fd_help' => "XML Sitemap priority of pages ranging 0.0 to 1.0",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// Index Field
$default_fd['page']['pg_index'] = array(
        'fd_name' => "Index",
        'fd_type' => "checkbox",
        'fd_options' => "yes\nno",
        'fd_default' => "yes",
        'fd_help' => "Setting this option to NO will help prevent search engines from indexing this page by using a meta noindex tag and an entry in robots.txt - USE WITH CAUTION!",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// Follow to Field
$default_fd['page']['pg_followto'] = array(
        'fd_name' => "Follow to",
        'fd_type' => "checkbox",
        'fd_options' => "yes\nno",
        'fd_default' => "yes",
        'fd_help' => "Prevent link juice from flowing to this page by nofollowing all navigation links to the page. Useful for low-search-value pages on the site such as privacy policy and terms pages.",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );

// Follow from Field
$default_fd['page']['pg_followfrom'] = array(
        'fd_name' => "Follow from",
        'fd_type' => "checkbox",
        'fd_options' => "yes\nno",
        'fd_default' => "yes",
        'fd_help' => "Prevent spiders from following any links on this page by inserting a nofollow meta tag.",
        'fd_order' => $o++,
        'fd_tabname' => "SEO"
    );


/* Scheduling Tab */

// Go-Live Date Field
$default_fd['page']['pg_livedate'] = array(
        'fd_name' => "Go-Live Date",
        'fd_type' => "unixdate",
        'fd_default' => "0",
        'fd_help' => "The page will not appear on the site until this date",
        'fd_order' => "1",
        'fd_tabname' => "Scheduling",
    );

// Expiry Date Field
$default_fd['page']['pg_expirydate'] = array(
        'fd_name' => "Expiry Date",
        'fd_type' => "unixdate",
        'fd_default' => "0",
        'fd_help' => "The page will be removed from the site after this date",
        'fd_order' => "2",
        'fd_tabname' => "Scheduling",
    );

// Page Status Field
$default_fd['page']['pg_status'] = array(
        'fd_name' => "Page Status",
        'fd_type' => "radio",
        'fd_options' => "active:Active\ninactive:Inactive\nhidden:Hidden",
        'fd_default' => "active",
        'fd_help' => "Inactive pages will not show on the menu or be available via direct navigation",
        'fd_order' => "3",
        'fd_tabname' => "Scheduling",
    );


/* Technical Tab */
$o=0;

// ID Field
$default_fd['page']['pageid'] = array(
        'fd_name' => "ID",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID for this page - automatically assigned by the system",
        'fd_order' => $o++,
        'fd_tabname' => "Technical",
    );


// Last Updated Field
$default_fd['page']['pg_updated'] = array(
        'fd_name' => "Last Updated",
        'fd_type' => "timestamp",
        'fd_default' => "CURRENT_TIMESTAMP",
        'fd_order' => $o++,
        'fd_tabname' => "Technical"
    );


// Plugin Link Field
$default_fd['page']['pg_link'] = array(
        'fd_name' => "Plugin Link",
        'fd_type' => "plugin",
        'fd_size' => "40",
        'fd_help' => "If the page is controlled by a PHP plugin, select the plugin/class here.",
        'fd_order' => $o++,
        'fd_tabname' => "Technical"
    );

// Head content Field
$default_fd['page']['pg_head'] = array(
        'fd_name' => "Head content",
        'fd_type' => "textarea",
        'fd_rows' => "6",
        'fd_cols' => "60",
        'fd_help' => "Any HEAD elements to include in the page, such as Javascript and CSS. For sitewide additions, use a .js or .css file instead.",
        'fd_order' => $o++,
        'fd_tabname' => "Technical"
    );

// SSL Secure Field
$default_fd['page']['pg_ssl'] = array(
        'fd_name' => "SSL Secure",
        'fd_type' => "radio",
        'fd_options' => "yes\nno",
        'fd_default' => "no",
        'fd_help' => "If a secure certificate is installed on the site, this option determines whether a page is served securely or not.",
        'fd_order' => $o++,
        'fd_tabname' => "Technical"
    );

// Content Cache Field
$default_fd['page']['pg_contentcache'] = array(
        'fd_name' => "Content Cache",
        'fd_type' => "radio",
        'fd_options' => "yes\nno\nauto",
        'fd_default' => "auto",
        'fd_help' => "Caches content to improve performance, however pages that change often should not be cached.",
        'fd_order' => $o++,
        'fd_tabname' => "Technical"
    );


$multilanguage = (Jojo::tableExists('option')) ? Jojo::getOption('multilanguage', 'no') : 'no';
$default_fd['page']['pg_language']['fd_type'] = ($multilanguage == 'yes') ? 'dblist' : 'hidden';

$enable_meta_keywords = (Jojo::tableExists('option')) ? Jojo::getOption('page_meta_keywords', 'no') : 'no';
$default_fd['page']['pg_metakeywords']['fd_type'] = ($enable_meta_keywords == 'yes') ? 'textarea' : 'hidden';

$enable_secondary_nav = (Jojo::tableExists('option')) ? Jojo::getOption('use_secondary_nav', 'yes') : 'yes';
$default_fd['page']['pg_secondarynav']['fd_type'] = ($enable_secondary_nav == 'yes') ? 'checkbox' : 'hidden';
