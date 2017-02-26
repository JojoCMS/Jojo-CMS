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
 * @package jojo_core
 */

/* Change pg_link from filenames to class names */
$classes = Jojo::selectQuery('SELECT DISTINCT pg_link AS class FROM {page} WHERE pg_link != ""');
foreach ($classes as $row) {
    $class = strtolower($row['class']);
    if (strpos($class, '.php') === false) {
        /* Already a class name - just lowercase it */
        Jojo::updateQuery("UPDATE {page} SET pg_link = ? WHERE pg_link = ?", array($class, $row['class']));
        continue;
    }

    /* Convert filename to classname */
    $class = ucfirst(str_replace('.php', '', $class));
    $class = str_replace('-', '_', $class);
    $class = 'jojo_plugin_' . $class;

    /* See if a plugin has a correctly names file */
    $filename = str_replace('jojo_plugin_', '',  $class) . '.php';
    if (Jojo::listPlugins($filename)) {
        /* Yes so change the database value to the class name instead of the filename */
        Jojo::updateQuery("UPDATE {page} SET pg_link = ? WHERE pg_link = ?", array($class, $row['class']));
    }
}

// Homepage
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pageid = '1'")) {
    echo "Adding <b>Home</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pageid=1, pg_title = 'Home', pg_body_code = '[editor:html]\nComing soon...', pg_body = 'Coming soon...', pg_link = 'jojo_plugin_Index'");
}

// Admin Root, All admin pages are below this one
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_admin_root'");
if (!$data) {
    echo "Adding <b>Admin Root</b> Page to menu<br />";
    $_ADMIN_ROOT_ID = Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Admin Root', pg_menutitle='Admin', pg_link = 'jojo_plugin_admin_root', pg_url = 'admin/root', pg_order=100, pg_parent=0, pg_mainnav='no', pg_secondarynav='no', pg_footernav='no', pg_breadcrumbnav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_permissions = 'everyone.show = 0\neveryone.view = 0\nadmin.show = 1\nadmin.view = 1'");
} else {
    $_ADMIN_ROOT_ID = $data['pageid'];
}

// Admin
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_admin'");
if (!$data) {
    echo "Adding <b>Admin</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Admin', pg_link = 'jojo_plugin_admin', pg_url = 'admin', pg_parent=?, pg_order=1, pg_mainnav='yes'", array($_ADMIN_ROOT_ID));
}

// Admin Content
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_admin_content'");
if (!$data) {
    echo "Adding <b>Admin Content</b> Page to menu<br />";
    $_ADMIN_CONTENT_ID = Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Content', pg_link = 'jojo_plugin_admin_content', pg_url = 'admin/content', pg_order=2, pg_parent=$_ADMIN_ROOT_ID, pg_mainnav='yes', pg_secondarynav='no', pg_breadcrumbnav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no'");
} else {
    $_ADMIN_CONTENT_ID = $data['pageid'];
}

// Admin Reports
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_admin_reports'");
if (!$data) {
    echo "Adding <b>Admin Reports</b> Page to menu<br />";
    $_ADMIN_REPORTS_ID = Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Reports', pg_link = 'jojo_plugin_admin_reports', pg_url = 'admin/reports', pg_order=3, pg_parent=$_ADMIN_ROOT_ID, pg_mainnav='yes', pg_secondarynav='no', pg_breadcrumbnav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no'");
} else {
    $_ADMIN_REPORTS_ID = $data['pageid'];
}

// Admin Customize
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_admin_customize'");
if (!$data) {
    echo "Adding <b>Admin Customize</b> Page to menu<br />";
    $_ADMIN_CUSTOMIZE_ID = Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Customize', pg_link = 'jojo_plugin_admin_customize', pg_url = 'admin/customize', pg_order=4, pg_parent=$_ADMIN_ROOT_ID, pg_mainnav='yes', pg_secondarynav='no', pg_footernav='no', pg_breadcrumbnav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no'");
} else {
    $_ADMIN_CUSTOMIZE_ID = $data['pageid'];
}

// Admin Configuration
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_admin_configuration'");
if (!$data) {
    echo "Adding <b>Admin Configuration</b> Page to menu<br />";
    $_ADMIN_CONFIGURATION_ID = Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Configuration', pg_link = 'jojo_plugin_admin_configuration', pg_url = 'admin/configuration', pg_order=5, pg_parent=$_ADMIN_ROOT_ID, pg_mainnav='yes', pg_secondarynav='no', pg_footernav='no', pg_breadcrumbnav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no'");
} else {
    $_ADMIN_CONFIGURATION_ID = $data['pageid'];
}

// Edit Pages
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/edit/page'")) {
    echo "Adding <b>Edit Pages</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Edit Pages', pg_link = 'jojo_plugin_admin_edit', pg_url = 'admin/edit/page', pg_parent = ?, pg_order=1", array($_ADMIN_CONTENT_ID));
}

// Edit Snippets
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/edit/snippet'")) {
    echo "Adding <b>Edit Snippets</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Snippets', pg_link = 'jojo_plugin_admin_edit', pg_url = 'admin/edit/snippet', pg_parent = ?, pg_order=5", array($_ADMIN_CONTENT_ID));
}

// Edit Users
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/edit/user'")) {
    echo "Adding <b>Edit Users</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Users', pg_link = 'jojo_plugin_admin_edit', pg_url = 'admin/edit/user', pg_parent = ?, pg_order=12", array($_ADMIN_CONFIGURATION_ID));
}

// Edit Usergroups
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/edit/usergroups'")) {
    echo "Adding <b>Edit Usergroups</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Usergroups', pg_link = 'jojo_plugin_admin_edit', pg_url = 'admin/edit/usergroups', pg_parent = ?, pg_order=13", array($_ADMIN_CONFIGURATION_ID));
}

// View Event log
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_admin_eventlog'")) {
    echo "Adding <b>Event Log</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Event Log', pg_link = 'jojo_plugin_admin_eventlog', pg_url = 'admin/eventlog', pg_parent = ?, pg_order=12", array($_ADMIN_REPORTS_ID));
}

// Manage Plugins
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/plugins'")) {
    echo "Adding <b>Plugins</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Plugins', pg_desc = 'Install plugins to enhance the functionality of the website', pg_link = 'jojo_plugin_admin_plugins', pg_url = 'admin/plugins', pg_parent = ?, pg_order=3", array($_ADMIN_CUSTOMIZE_ID));
}

// Manage Themes
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/themes'")) {
    echo "Adding <b>Manage Themes</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Themes', pg_desc = 'Install themes to customise the look of the website', pg_link = 'jojo_plugin_admin_themes', pg_url = 'admin/themes', pg_parent = ?, pg_order=4", array($_ADMIN_CUSTOMIZE_ID));
}

// Manage Languages
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/edit/language'")) {
    echo "Adding <b>Manage Language</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Languages', pg_desc = 'Manage site languages for multi-language sites', pg_link = 'jojo_plugin_admin_edit', pg_url = 'admin/edit/language', pg_parent = ?, pg_order=5", array($_ADMIN_CUSTOMIZE_ID));
}

// Manage LanguageCountry
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/edit/lang_country'")) {
    echo "Adding <b>Manage Language/Country</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Sub-sections', pg_desc = 'Manage site regions/languages/countries for multi-sectioned sites', pg_link = 'jojo_plugin_admin_edit', pg_url = 'admin/edit/lang_country', pg_parent = ?, pg_order=5", array($_ADMIN_CUSTOMIZE_ID));
}

// Manage Options
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_admin_options'")) {
    echo "Adding <b>Options</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Options', pg_link = 'jojo_plugin_admin_options', pg_url = 'admin/options', pg_parent=?, pg_order=100", array($_ADMIN_CUSTOMIZE_ID));
}

// Login page
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_login'")) {
    echo "Adding <b>login</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Login', pg_url = 'login', pg_link = 'jojo_plugin_login', pg_parent=0, pg_order=100, pg_mainnav='no', pg_secondarynav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no'");
}

// Logout page
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_logout'")) {
    echo "Adding <b>Logout</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Logout', pg_url = 'logout', pg_link = 'jojo_plugin_logout', pg_parent = ?, pg_order=100, pg_index='no', pg_permissions = 'notloggedin.show = 1\neveryone.view = 1'", array($_ADMIN_ROOT_ID));
}

// Edit Tabledata
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/edit/tabledata'")) {
    echo "Adding <b>Edit Tabledata</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Tabledata', pg_link = 'jojo_plugin_admin_edit', pg_url = 'admin/edit/tabledata', pg_parent = ?, pg_order=100", array($_ADMIN_CONFIGURATION_ID));
}

// Edit Fielddata
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/edit/fielddata'")) {
    echo "Adding <b>Edit Fielddata</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Fielddata', pg_link = 'jojo_plugin_admin_edit', pg_url = 'admin/edit/fielddata', pg_parent = ?, pg_order=100", array($_ADMIN_CONFIGURATION_ID));
}

// Not On Menu area - to contain 404 errors, form submisstion handler etc
$data = Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_title = 'Not on Menu'");
if (!$data) {
    echo "Adding <b>Not on Menu</b> Page to menu<br />";
    $_NOT_ON_MENU_ID = Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Not on Menu', pg_link = '', pg_parent=0, pg_order=100, pg_status = 'inactive', pg_mainnav='no', pg_secondarynav='no', pg_breadcrumbnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no'");
} else {
    $_NOT_ON_MENU_ID = $data['pageid'];
}

// Submit Form
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_submit_form'")) {
    echo "Adding <b>Submit Form</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Submit Form', pg_link = 'jojo_plugin_submit_form', pg_url = 'submit-form', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// POST Redirect
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_post_redirect'")) {
    echo "Adding <b>POST Redirect</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Submit Form', pg_link = 'jojo_plugin_post_redirect', pg_url = 'redirect', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// 404
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_404'")) {
    Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link = 'jojo_plugin_404'");
}

// CSS
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_css'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_css' WHERE pg_link = 'jojo_plugin_css'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_css'")) {
    echo "Adding <b>CSS Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'CSS Handler', pg_link = 'jojo_plugin_core_css', pg_url = 'css', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// JSON
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_json'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_json' WHERE pg_link = 'jojo_plugin_json'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_json'")) {
    echo "Adding <b>JSON Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'JSON Handler', pg_link = 'jojo_plugin_core_json', pg_url = 'json', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Frajax Action
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_Action'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_action' WHERE pg_link = 'jojo_plugin_action'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_action'")) {
    echo "Adding <b>Frajax Request Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Frajax Request Handler', pg_link = 'jojo_plugin_core_action', pg_url = 'actions', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Images
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_Image'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_image' WHERE pg_link = 'jojo_plugin_image'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_image'")) {
    echo "Adding <b>Image Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Image Handler', pg_link = 'jojo_plugin_core_image', pg_url = 'images', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// External
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_external'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_external' WHERE pg_link = 'jojo_plugin_external'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_external'")) {
    echo "Adding <b>External File Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'External File Handler', pg_link = 'jojo_plugin_core_external', pg_url = 'external', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Javascript
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_js'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_js' WHERE pg_link = 'jojo_plugin_js'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_Js'")) {
    echo "Adding <b>Javascript Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Javascript Handler', pg_link = 'jojo_plugin_core_js', pg_url = 'js', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Downloads
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_download'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_download' WHERE pg_link = 'jojo_plugin_download'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_download'")) {
    echo "Adding <b>Download Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Download Handler', pg_link = 'jojo_plugin_core_download', pg_url = 'downloads', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Files
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_file'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_file' WHERE pg_link = 'jojo_plugin_file'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_file'")) {
    echo "Adding <b>Plugin File Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Plugin File Handler', pg_link = 'jojo_plugin_core_file', pg_url = 'files', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Inline Files
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_inline'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_inline' WHERE pg_link = 'jojo_plugin_inline'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_Inline'")) {
    echo "Adding <b>Plugin Inline File Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Plugin Inline File Handler', pg_link = 'jojo_plugin_core_inline', pg_url = 'inline', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Favicon
if (Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_favicon'")) {
    Jojo::updateQuery("UPDATE {page} SET pg_link = 'jojo_plugin_core_favicon' WHERE pg_link = 'jojo_plugin_favicon'");
} elseif (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_favicon'")) {
    echo "Adding <b>Favicon Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Favicon Handler', pg_link = 'jojo_plugin_core_favicon', pg_url = 'favicon.ico', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Favicon
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_core_appletouchicon'")) {
    echo "Adding <b>Apple Touch Icon Handler</b> Page<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Apple Touch Icon Handler', pg_link = 'jojo_plugin_core_appletouchicon', pg_url = 'apple-touch-icon.png', pg_parent= ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='yes', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Forgot password
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_forgot_password'")) {
    echo "Adding <b>Forgot Password</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Forgot Password', pg_link = 'jojo_plugin_forgot_password', pg_url = 'forgot-password', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// Change password
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_link = 'jojo_plugin_change_password'")) {
    echo "Adding <b>Change Password</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Change Password', pg_link = 'jojo_plugin_change_password', pg_url = 'change-password', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_footernav='no', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no', pg_body = ''", array($_NOT_ON_MENU_ID));
}

// styleguide
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'styleguide'")) {
    $styleguidehtml = file_get_contents(_BASEPLUGINDIR . '/jojo_core/install/styleguide.html');
    echo "Adding <b>Style Guide</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Style Guide', pg_link = '', pg_url = 'styleguide', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_xmlsitemapnav='no', pg_body = ?, pg_body_code = ?", array($_NOT_ON_MENU_ID, $styleguidehtml, '[editor:html]
    ' . $styleguidehtml));
}

/* Fix problems from Jojo upgrades */
if (Jojo::fieldExists('page', 'pg_noindex')) {
    Jojo::updateQuery("UPDATE {page} SET pg_index='yes' WHERE pg_noindex!='yes'");
    Jojo::updateQuery("UPDATE {page} SET pg_index='no' WHERE pg_noindex='yes'");
}
Jojo::updateQuery("UPDATE {page} SET pg_mainnav='yes' WHERE pg_mainnav='auto'");
Jojo::updateQuery("UPDATE {page} SET pg_secondarynav='no' WHERE pg_secondarynav='auto'");
Jojo::updateQuery("UPDATE {page} SET pg_breadcrumbnav='yes' WHERE pg_breadcrumbnav='auto'");
Jojo::updateQuery("UPDATE {page} SET pg_footernav='yes' WHERE pg_footernav='auto'");
Jojo::updateQuery("UPDATE {page} SET pg_sitemapnav='yes' WHERE pg_sitemapnav='auto'");
Jojo::updateQuery("UPDATE {page} SET pg_xmlsitemapnav ='yes' WHERE pg_xmlsitemapnav ='auto'");


/* Set assets to Index by default or not if option set */
$indexassets = Jojo::getOption('robots_assets', 'yes');
// CSS
    Jojo::updateQuery("UPDATE {page} SET pg_index=? WHERE pg_link='jojo_plugin_core_css'", array($indexassets));
// Image
    Jojo::updateQuery("UPDATE {page} SET pg_index=? WHERE pg_link='jojo_plugin_core_image'", array($indexassets));
// External
    Jojo::updateQuery("UPDATE {page} SET pg_index=? WHERE pg_link='jojo_plugin_core_external'", array($indexassets));
// Javascript
    Jojo::updateQuery("UPDATE {page} SET pg_index=? WHERE pg_link='jojo_plugin_core_js'", array($indexassets));
// Downloads
    Jojo::updateQuery("UPDATE {page} SET pg_index=? WHERE pg_link='jojo_plugin_core_download'", array($indexassets));
// Files
    Jojo::updateQuery("UPDATE {page} SET pg_index=? WHERE pg_link='jojo_plugin_core_file'", array($indexassets));
// Inline Files
    Jojo::updateQuery("UPDATE {page} SET pg_index=? WHERE pg_link='jojo_plugin_core_inline'", array($indexassets));
// Favicon
    Jojo::updateQuery("UPDATE {page} SET pg_index=? WHERE pg_link='jojo_plugin_core_favicon'", array($indexassets));

/* Remove certain pages from XML sitemap */
Jojo::updateQuery("UPDATE {page} SET pg_index='no', pg_xmlsitemapnav='no', pg_followto='no', pg_followfrom='yes' WHERE pg_link='register.php' OR pg_link='user-profile.php' OR pg_link='submit-form.php' OR pg_link='404.php' OR pg_link='forgot-password.php' OR pg_link='change-password.php' or pg_url='styleguide' or pg_url='jojo_plugin_logout'");
/* add back xmlsitemap, was previously excluded in above, which stopped Google from spidering xmlsitemap  */
Jojo::updateQuery("UPDATE {page} SET pg_index='yes' WHERE pg_link='jojo_plugin_jojo_sitemapxml' ");

/* Clear plugin / theme cache files */
if (Jojo::fileExists(_CACHEDIR . '/api.txt')) {
    unlink(_CACHEDIR.'/api.txt');
}
if (Jojo::fileExists(_CACHEDIR . '/listPlugins.txt')) {
    unlink(_CACHEDIR.'/listPlugins.txt');
}
if (Jojo::fileExists(_CACHEDIR . '/listThemes.txt')) {
    unlink(_CACHEDIR.'/listThemes.txt');
}

/* remove php-errors option in favour of php_errors (copy the current value across) */
$phperrors = Jojo::getOption('php-errors');
if (!empty($phperrors)) {
    Jojo::setOption('php_errors', $phperrors);
    Jojo::removeOption('php-errors');
}

/* If the Jojo redirect page still exists and the redirect table still contains data, we can assume the site wants to have the redirect plugin installed */
if (Jojo::tableExists('redirect')) {
    $redirect_pages = Jojo::selectRow("SELECT * FROM {page} WHERE pg_link = 'jojo_plugin_admin_redirects'");
    $redirect_plugins = Jojo::selectRow("SELECT * FROM {plugin} WHERE name = 'jojo_redirect'");
    $redirect_redirects = Jojo::selectRow("SELECT * FROM {redirect} WHERE 1");
    if (count($redirect_pages) && !count($redirect_plugins) && count($redirect_redirects)) {
        Jojo::insertQuery("REPLACE INTO {plugin} SET name='jojo_redirect', active='yes'");
        echo 'Redirect plugin reinstalled. Please run setup again.<br />';
    }
}

/* if the jojo_community plugin is not installed, install the jojo_community_legacy plugin */
$community_plugins = Jojo::selectRow("SELECT * FROM {plugin} WHERE name = 'jojo_community'");
$legacy_plugins = Jojo::selectRow("SELECT * FROM {plugin} WHERE name = 'jojo_community_legacy'");
$legacy_pages = Jojo::selectRow("SELECT * FROM {page} WHERE pg_link = 'jojo_plugin_register' OR pg_link = 'jojo_plugin_user_profile'");
if (!count($community_plugins) && !count($legacy_plugins) && count($legacy_pages) ) {
    Jojo::insertQuery("REPLACE INTO {plugin} SET name='jojo_community_legacy', active='yes'");
    echo 'jojo_community_legacy plugin installed. Please run setup again.<br />';
}

/* check that index.php in the webroot is the same as the Jojo default versions */
if (Jojo::fileExists(_BASEDIR.'/_www/index.php') && Jojo::fileExists(_BASEDIR.'/_www/index_lite.php') && Jojo::fileExists(_WEBDIR.'/index.php')) {
    $jojo_index_hash      = md5(file_get_contents(_BASEDIR.'/_www/index.php'));
    $jojo_index_lite_hash = md5(file_get_contents(_BASEDIR.'/_www/index_lite.php'));
    $live_index_hash      = md5(file_get_contents(_WEBDIR.'/index.php'));

    if (($jojo_index_hash != $live_index_hash) && ($jojo_index_lite_hash != $live_index_hash)) {
        echo 'Your version of index.php may be out of date. Please copy '._BASEDIR.'/www/index.php'.' to '._WEBDIR.'/index.php'.'.<br />';
    }
}

/* uncomment and run setup to convert any content links to an old http siteurl to the https version
if (strpos(_SITEURL, 'https:')!==false) {
  $pages = Jojo::selectQuery("SELECT pageid, pg_body, pg_body_code FROM {page}");
  foreach ($pages as $p) {
    if ($p['pg_body']) {
      $body = Jojo::SSLSITEURLs($p['pg_body']);
      $bodycode = "[editor:html]\n" . $body;
      Jojo::updateQuery("UPDATE {page} SET pg_body=?, pg_body_code=? WHERE pageid=?", array($body, $bodycode, $p['pageid']));
    }
  }
}
*/