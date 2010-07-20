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
 * @package jojo_article
 */


/* Articles */
$data = Jojo::selectRow("SELECT * FROM {page}  WHERE pg_link LIKE 'jojo_plugin_jojo_article'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Article: Adding <b>Articles</b> Page to menu<br />";
    $articlespage = Jojo::insertQuery("INSERT INTO {page} SET pg_title='Articles', pg_link='jojo_plugin_jojo_article', pg_url='articles'");
    // add a corresponding category
    Jojo::insertQuery("INSERT INTO {articlecategory} SET pageid='$articlespage'");
}

/* Edit Articles */
$data = Jojo::selectRow("SELECT * FROM {page}  WHERE pg_url='admin/edit/article'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Article: Adding <b>Edit Articles</b> Page to menu<br />";
    $editarticlepage = Jojo::insertQuery("INSERT INTO {page} SET pg_title='Edit Articles', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/article', pg_parent=?, pg_order=2", array($_ADMIN_CONTENT_ID));
} else {
    $editarticlepage = $data['pageid'];
}

/* Edit Article Page Options */
$data = Jojo::selectRow("SELECT pg_url FROM {page} WHERE pg_url='admin/edit/articlecategory'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Article: Adding <b>Article Page Options</b> Page to Edit Content menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Article Page Options', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/articlecategory', pg_parent=?, pg_order=3", $editarticlepage);
}

/* Remove any old Article RSS pages */
$data = Jojo::selectQuery("SELECT pageid FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_article_rss'");
if (count($data)) {
    foreach ($data as $p ) {
        Jojo::deleteQuery("DELETE FROM {page} WHERE pageid = ? ", $p['pageid']);
    }
    echo "Jojo_Plugin_Jojo_Article: Remove old RSS pages<br />";
}

/* Ensure there is a folder for uploading article images */
$res = Jojo::RecursiveMkdir(_DOWNLOADDIR . '/articles');
if ($res === true) {
    echo "Jojo_Plugin_Jojo_Article: Created folder: " . _DOWNLOADDIR . '/articles';
} elseif($res === false) {
    echo 'Jojo_Plugin_Jojo_Article: Could not automatically create ' .  _DOWNLOADDIR . '/articles' . 'folder on the server. Please create this folder and assign 777 permissions.';
}

/* If no articles, add a sample article */
$data = Jojo::selectRow("SELECT * FROM {article}");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_Article: Added sample article.<br/>";
    Jojo::insertQuery("INSERT INTO {article} ( `ar_title` , `ar_url`, `ar_desc` , `ar_body` , `ar_bbbody` , `ar_category` , `ar_date` , `ar_image` , `ar_author` , `ar_source` , `ar_seotitle` , `ar_metadesc` , `ar_language` )
    VALUES (
    'Welcome to JojoCMS', 'test', 'A test article', 'Welcome to the articles section of your JojoCMS site. This part of the site is under construction. Article plugin powered by <a href=\"http://www.jojocms.org\">Jojo CMS</a>.', '[editor:bb]Welcome to the articles section of your JojoCMS site. This part of the site is under construction. Article plugin powered by [url=http://www.jojocms.org]Jojo CMS[/url].', '1', NULL , '', '', '', '', '', 'en'
    );");
}

/* Regenerating HTML cache for Article */
$articles = Jojo::selectQuery("SELECT * FROM {article} WHERE ar_bbbody != ''");
if (count($articles)) {
    echo 'Jojo_Plugin_Jojo_Article: Regenerating HTML cache for Articles<br />';
    $n = count($articles);
    for ($i=0; $i<$n; $i++) {
        $bbcode = $articles[$i]['ar_bbbody'];
        $cache = '';
        if (strpos($bbcode, '[editor:bb]') !== false) {
            /* BB Code field */
            $bbcode = preg_replace('/\\[editor:bb\\][\\r\\n]{0,2}(.*)/si', '$1', $bbcode);
            $bb = new bbconverter;
            $bb->truncateurl = 30;
            $bb->imagedropshadow = true; // Jojo::yes2true(Jojo::getOption('imagedropshadow'));
            $bb->setBBCode($bbcode);
            $cache = $bb->convert('bbcode2html');
        } elseif (strpos($bbcode, '[editor:html]') !== false) {
            $cache = str_replace('[editor:html]', '', $bbcode);
        }
        if ($cache){
            /* Update DB with the cached HTML data */
            Jojo::updateQuery("UPDATE {article} SET ar_body = ? WHERE articleid = ? LIMIT 1", array($cache, $articles[$i]['articleid']));
        }
    }
}

//script to force articles into categories - should only run once
if (Jojo::getOption('article_enable_categories')) {
    $categories = Jojo::selectQuery("SELECT articlecategoryid, ac_pageid, ac_url FROM {articlecategory}");
    //run through the categories and ensure each of them is tied to a pageid, grabbing the first one it finds for multiple page with the same url
    if ($categories) {
        foreach ($categories as $c) {
            if (!$c['ac_pageid']) {
                $articlespage = Jojo::selectRow("SELECT pageid, pg_url FROM {page} WHERE pg_link = 'jojo_plugin_jojo_article' AND pg_url = ? ", array($c['ac_url']));
                if (count($articlespage)) {
                    Jojo::updateQuery("UPDATE {articlecategory} SET ac_pageid = ? WHERE articlecategoryid = ? ", array($articlespage['pageid'], $c['articlecategoryid']));
                }
            }
        }
    }
    $categories = jojo::selectAssoc("SELECT ac_pageid AS id, articlecategoryid, ac_pageid, ac_url FROM {articlecategory}");
    $articles = Jojo::selectQuery("SELECT articleid, ar_category, ar_language FROM {article}");
    $articlepages = Jojo::selectQuery("SELECT pageid, pg_url, pg_language FROM {page} WHERE pg_link LIKE 'jojo_plugin_jojo_article'"); 
    if (Jojo::getOption('article_enable_categories')=='no') {
        //1st case - no categories and no multilanguage
        if (Jojo::getOption('multilanguage', '') == 'no') {
            /* should only be one articles page in this case, but you never know.. 
            if there is more than one, only the first page will end up with the categorized articles in it
            but make categories for any others found anyway so they can be populated manually if desired */
            foreach($articlepages as $k => $page) {
               $pageid = $page['pageid'];
               $pageurl = $page['pg_url'];
                // if no category for this page id
                if (!count($categories) || !isset($categories[$pageid])) { 
                    $catid[$k] = Jojo::insertQuery("INSERT INTO {articlecategory} (ac_pageid, ac_url) VALUES ('$pageid', '$pageurl')");
                // category is set for this page id, check to see if the url needs updating
                } elseif (isset($categories[$pageid]) && $categories[$pageid]['ac_url'] != $pageurl ) {
                    jojo::updateQuery("UPDATE {articlecategory} SET ac_url = ? WHERE ac_pageid = ? ", array($pageurl, $pageid));
                    $catid[$k] = $categories[$pageid]['articlecategoryid'];
                } else {
                    $catid[$k] = $categories[$pageid]['articlecategoryid'];        
                }
            }
            //update all articles with the first pageid found
            if ($articles) {
                $cat = array_shift($catid);
                Jojo::updateQuery("UPDATE {article} SET ar_category = ? ", array($cat));
            }
        // 2nd case - no categories but multilanguage
        } else {
            /* find each articles page in whatever language,
            make a separate category for it,
            and assign all articles in that language to that category */
            foreach($articlepages as $k => $page) {
               $pageid = $page['pageid'];
               $pageurl = $page['pg_url'];
               $pagelanguage = $page['pg_language'];
                // if no category for this page id
                if (!count($categories) || !isset($categories[$pageid])) { 
                    $catid = Jojo::insertQuery("INSERT INTO {articlecategory} (ac_pageid, ac_url) VALUES ('$pageid', '$pageurl')");
                // category is set for this page id, check to see if the url needs updating
                } else {
                    if ($categories[$pageid]['ac_url'] != $pageurl) {
                        jojo::updateQuery("UPDATE {articlecategory} SET ac_url = ? WHERE ac_pageid = ? ", array($pageurl, $pageid));
                    }
                    $catid = $categories[$pageid]['articlecategoryid'];
                } 
                //update all articles with the pageid found for that language
                if ($articles) {
                    foreach ($articles as $a) {
                        Jojo::updateQuery("UPDATE {article} SET ar_category = ? WHERE ar_language = ? ", array($catid, $pagelanguage));
                    }
                }
            }            
        }
    } else {
        //3rd case - categories enabled and no multilanguage
        if (Jojo::getOption('multilanguage', '') == 'no') {
            /* check if there are articles pages that don't have a category set ,
            set a category for them and add any category-less articles to that category,
            make sure that the pageids have been saved into existing categories */
            foreach($articlepages as $k => $page) {
               $pageid = $page['pageid'];
               $pageurl = $page['pg_url'];
                // category is set for this page id, check to see if the url needs updating
                if (isset($categories[$pageid]) && $categories[$pageid]['ac_url'] != $pageurl ) {
                    jojo::updateQuery("UPDATE {articlecategory} SET ac_url = ? WHERE ac_pageid = ? ", array($pageurl, $pageid));
                // no category is set for this page id
                } elseif  (!isset($categories[$pageid])){
                     $catid = Jojo::insertQuery("INSERT INTO {articlecategory} (ac_pageid, ac_url) VALUES ('$pageid', '$pageurl')");
                    //update all articles with no category to use this one
                    if ($articles) {
                        foreach ($articles as $a) {
                            Jojo::updateQuery("UPDATE {article} SET ar_category = ? WHERE ar_category = '0' ", array($catid));
                        }
                    }
                } 
            }
        //4th case - categories enabled and multilanguage
        } else {
            /* check if there are articles pages that don't have a category set ,
            set a category for them and add any category-less articles to that category,
            make sure that the pageids have been saved into existing categories */
            foreach($articlepages as $k => $page) {
                $catid = '';
               $pageid = $page['pageid'];
               $pageurl = $page['pg_url'];
               $pagelanguage = $page['pg_language'];
                // category is set for this page id, check to see if the url needs updating
                if (isset($categories[$pageid])) {
                    if ($categories[$pageid]['ac_url'] != $pageurl ) {
                        Jojo::updateQuery("UPDATE {articlecategory} SET ac_url = ? WHERE ac_pageid = ? ", array($pageurl, $pageid));
                    }
                // no category is set for this page id
                } else{
                    $catid = Jojo::insertQuery("INSERT INTO {articlecategory} (ac_pageid, ac_url) VALUES ('$pageid', '$pageurl')");
                }
                //update all articles with the pageid found for that language
                if ($articles && $catid) {
                    foreach ($articles as $a) {
                        Jojo::updateQuery("UPDATE {article} SET ar_category = ? WHERE ar_language = ? AND ar_category= '0' ", array($catid, $pagelanguage));
                    }
                }
            }
        
        }
    }
    //delete option to enable categories
    Jojo::deleteQuery("DELETE FROM {option} WHERE op_name = 'article_enable_categories' ");
    echo 'Article categories enforced';
}

