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

$default_td['articlecategory'] = array(
        'td_name' => "articlecategory",
        'td_primarykey' => "articlecategoryid",
        'td_displayfield' => "pageid",
        'td_filter' => "yes",
        'td_topsubmit' => "yes",
        'td_addsimilar' => "no",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
        'td_help' => "News Article Categories are managed from here.",
        'td_plugin' => "Jojo_article",
    );

$o = 0;

/* Content Tab */

// Articlecategoryid Field
$default_fd['articlecategory']['articlecategoryid'] = array(
        'fd_name' => "Articlecategoryid",
        'fd_type' => "integer",
        'fd_readonly' => "1",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "advanced",
    );

// Page Field
$default_fd['articlecategory']['pageid'] = array(
        'fd_name' => "Page",
        'fd_type' => "dbpluginpagelist",
        'fd_options' => "jojo_plugin_jojo_article",
        'fd_readonly' => "1",
        'fd_default' => "0",
        'fd_help' => "The artciles page on the site used for this category.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Type Field
$default_fd['articlecategory']['type'] = array(
        'fd_name' => "Type",
        'fd_type' => "radio",
        'fd_options' => "normal:Normal\nparent:Parent\nindex:All Articles",
        'fd_readonly' => "0",
        'fd_default' => "normal",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Sortby Field
$default_fd['articlecategory']['sortby'] = array(
        'fd_name' => "Sortby",
        'fd_type' => "radio",
        'fd_options' => "ar_title asc:Title\nar_date desc:Article Date\nar_livedate desc:Go Live Date",
        'fd_readonly' => "0",
        'fd_default' => "ar_date desc",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Add to Nav 
$default_fd['articlecategory']['addtonav'] = array(
        'fd_name' => "Show Articles in Nav",
        'fd_type' => "yesno",
        'fd_help' => "Add articles to navigation as child pages of this one.",
        'fd_default' => "0",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );
    
// Weighted Index Field
$default_fd['articlecategory']['weighting'] = array(
        'fd_name' => "Weighted Index",
        'fd_type' => "yesno",
        'fd_readonly' => "0",
        'fd_default' => "1",
        'fd_help' => "Weight article index so that first 2 articles are more prominent, after 10 are truncated more and after 20 are reduced to a title link only ",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Snippet Length Field
$default_fd['articlecategory']['snippet'] = array(
        'fd_name' => "Snippet Length",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => "400",
        'fd_help' => "Truncate index snippets to this many characters. Use 'full' for no snipping.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Read more link text 
$default_fd['articlecategory']['readmore'] = array(
        'fd_name' => "Read more link",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => '> Read more',
        'fd_help' => "The link text to read the full article",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Show Date
$default_fd['articlecategory']['showdate'] = array(
        'fd_name' => "Show Post Date",
        'fd_type' => "yesno",
        'fd_readonly' => "0",
        'fd_default' => "1",
        'fd_help' => "Show date added on posts",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Date format Field
$default_fd['articlecategory']['dateformat'] = array(
        'fd_name' => "Date Format",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => "%e %b %Y",
        'fd_help' => "Format the time and/or date according to locale settings. See php.net/strftime for details",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Thumbnail sizing Field
$default_fd['articlecategory']['thumbnail'] = array(
        'fd_name' => "Thumbnail Size",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => "s150",
        'fd_help' => "image thumbnail sizing in index eg: 150x200, h200, v4000",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Main image sizing 
$default_fd['articlecategory']['mainimage'] = array(
        'fd_name' => "Main Image",
        'fd_type' => "text",
        'fd_readonly' => "0",
        'fd_default' => "v60000",
        'fd_help' => "image thumbnail sizing in index eg: 150x200, h200, v4000",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Show Rss link Field
$default_fd['articlecategory']['rsslink'] = array(
        'fd_name' => "Publish to Rss",
        'fd_type' => "yesno",
        'fd_readonly' => "0",
        'fd_default' => "1",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// external rss feed url ie feedburner per category
$default_fd['articlecategory']['externalrsslink'] = array(
        'fd_name' => "External RSS url",
        'fd_type' => "url",
        'fd_readonly' => "0",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );
    
// Allow Comments
$default_fd['articlecategory']['comments'] = array(
        'fd_name' => "Enable comments",
        'fd_type' => "yesno",
        'fd_readonly' => "0",
        'fd_default' => "1",
        'fd_order' => $o++,
        'fd_tabname' => "Content"
    );
