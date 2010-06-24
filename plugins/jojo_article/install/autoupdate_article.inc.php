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
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

if (!defined('_MULTILANGUAGE')) {
    define('_MULTILANGUAGE', Jojo::getOption('multilanguage', 'no') == 'yes');
}

$default_td['article'] = array(
        'td_name' => "article",
        'td_primarykey' => "articleid",
        'td_displayfield' => "ar_title",
        'td_categorytable' => "articlecategory",
        'td_categoryfield' => "ar_category",
        'td_rolloverfield' => "ar_date",
        'td_filter' => "yes",
        'td_orderbyfields' => "ar_date DESC, articleid DESC",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "tree",
        'td_help' => "News Articles are managed from here. Depending on the exact configuration, the most recent 5 articles may be shown on the homepage or sidebar, or they may be listed only on the news page. All News Articles have their own \"full info\" page, which has a unique URL for the search engines. This is based on the title of the article, so please do not change the title of an article unless absolutely necessary, as the PageRank of the article may suffer. The system will comfortably take many hundreds of articles, but you may want to manually delete anything that is no longer relevant, or correct.",
        'td_golivefield' => "ar_livedate",
        'td_expiryfield' => "ar_expirydate",
        'td_plugin' => "Jojo_article",
    );

$o= 0;
/* Content Tab */

// Articleid Field
$default_fd['article']['articleid'] = array(
        'fd_name' => "Articleid",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

$defaultcat = Jojo::selectRow("SELECT articlecategoryid FROM {articlecategory} ");
$defaultcat = isset($defaultpage['articlecategoryid']) ? $defaultpage['articlecategoryid'] : 1;
// Category Field
$default_fd['article']['ar_category'] = array(
        'fd_name' => "Article Page",
        'fd_type' => "dblist",
        'fd_options' => "articlecategory",
        'fd_default' => $defaultcat,
        'fd_size' => "20",
        'fd_help' => "If applicable, the page the Article belongs to",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// Title Field
$default_fd['article']['ar_title'] = array(
        'fd_name' => "Title",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "60",
        'fd_help' => "Title of the Article. This will be used for the URL, headings and titles. Because the URL is based on this field, avoid changing this if possible.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// SEO Title Field
$default_fd['article']['ar_seotitle'] = array(
        'fd_name' => "SEO Title",
        'fd_type' => "text",
        'fd_options' => "seotitle",
        'fd_size' => "60",
        'fd_help' => "Title of the Article - it may be worth including your search phrase at the beginning of the title to improve rankings for that phrase.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "standard",
    );

// Date Field
$default_fd['article']['ar_date'] = array(
        'fd_name' => "Date",
        'fd_type' => "date",
        'fd_default' => "NOW()",
        'fd_help' => "Date the article was published (defaults to Today)",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "standard",
    );

// Description Field
$default_fd['article']['ar_desc'] = array(
        'fd_name' => "Description",
        'fd_type' => "text",
        'fd_size' => "60",
        'fd_help' => "A one sentence description of the article. Used for rollover text on links, which enhances usability",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// URL Field
$default_fd['article']['ar_url'] = array(
        'fd_name' => "URL",
        'fd_type' => "internalurl",
        'fd_options' => class_exists('Jojo_Plugin_Jojo_article') ? Jojo_Plugin_Jojo_article::_getPrefix() : '',
        'fd_size' => "20",
        'fd_help' => "A customized URL - leave blank to create a URL from the title of the article",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "standard",
    );

// Article Content Field
$default_fd['article']['ar_bbbody'] = array(
        'fd_name' => "Article Content",
        'fd_type' => "texteditor",
        'fd_options' => "ar_body",
        'fd_rows' => "10",
        'fd_cols' => "50",
        'fd_help' => "The body of the article. Try to summarise the article in the first paragraph as this will be used for the snippet",
        'fd_order' => "7",
        'fd_tabname' => "Content",
    );

// Body Field
$default_fd['article']['ar_body'] = array(
        'fd_name' => "Body",
        'fd_type' => "hidden",
        'fd_rows' => "10",
        'fd_cols' => "50",
        'fd_help' => "The body of the article. Try to summarise the article in the first paragraph as this will be used for the snippet",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// Comments Field
$default_fd['article']['ar_comments'] = array(
        'fd_name' => "Comments enabled",
        'fd_type' => "radio",
        'fd_options' => "yes\nno",
        'fd_default' => "yes",
        'fd_help' => "Whether comments are allowed for this article",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// Author Field
$default_fd['article']['ar_author'] = array(
        'fd_name' => "Author",
        'fd_type' => "text",
        'fd_size' => "20",
        'fd_help' => "The author of the article, used with syndicated content",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// Source Field
$default_fd['article']['ar_source'] = array(
        'fd_name' => "Source",
        'fd_type' => "url",
        'fd_size' => "20",
        'fd_help' => "The original source of the article (Syndicated articles only). This field is not displayed, but is for internal use.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// META Description Field
$default_fd['article']['ar_metadesc'] = array(
        'fd_name' => "META Description",
        'fd_type' => "textarea",
        'fd_options' => "metadescription",
        'fd_rows' => "4",
        'fd_cols' => "60",
        'fd_help' => "A META Description for the article. By default, a meta description is auto-generated, but hand-written descriptions are always better. This is a recommended field.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// Image Field
$default_fd['article']['ar_image'] = array(
        'fd_name' => "Image",
        'fd_type' => "fileupload",
        'fd_help' => "An image for the article, if  available",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "standard",
    );

// Language Field
$default_fd['article']['ar_language'] = array(
        'fd_name' => "Language/Country",
        'fd_type' => "dblist",
        'fd_options' => "lang_country",
        'fd_default' => "en",
        'fd_size' => "20",
        'fd_help' => "The language or country of the article",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// HTMLLang Field (to allow user to override the default language for the language/country chosen.
$default_fd['article']['ar_htmllang'] = array(
        'fd_name' => "HTML Language",
        'fd_type' => "dblist",
        'fd_options' => "language",
        'fd_default' => "",
        'fd_size' => "20",
        'fd_help' => "The language of the article - if different from the default language for the language/country chosen above.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );


/* Scheduling Tab */

// Go Live Date Field
$default_fd['article']['ar_livedate'] = array(
        'fd_name' => "Go Live Date",
        'fd_type' => "unixdate",
        'fd_default' => "NOW()",
        'fd_help' => "The article will not appear on the site until this date",
        'fd_order' => "1",
        'fd_tabname' => "Scheduling",
        'fd_mode' => "standard",
    );

// Expiry Date Field
$default_fd['article']['ar_expirydate'] = array(
        'fd_name' => "Expiry Date",
        'fd_type' => "unixdate",
        'fd_default' => "NOW()",
        'fd_help' => "The page will be removed from the site after this date",
        'fd_order' => "2",
        'fd_tabname' => "Scheduling",
        'fd_mode' => "standard",
    );
