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
    $catid = Jojo::insertQuery("INSERT INTO {articlecategory} SET pageid='$articlespage'");
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
    Jojo::insertQuery("INSERT INTO {article} ( `ar_title` , `ar_url`, `ar_body` , `ar_bbbody` , `ar_category` , `ar_date`)
    VALUES (
    'Welcome to JojoCMS', 'test', 'Welcome to the articles section of your JojoCMS site. This part of the site is under construction. Article plugin powered by <a href=\"http://www.jojocms.org\">Jojo CMS</a>.', '[editor:html]\n<p>Welcome to the articles section of your JojoCMS site. This part of the site is under construction. Article plugin powered by <a href=\"http://www.jojocms.org\">Jojo CMS</a>.', ?, ?
    );", array((isset($catid) ? $catid : 1), time()));
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

    $categories = jojo::selectAssoc("SELECT pageid AS id, articlecategoryid, pageid FROM {articlecategory}");
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
                // if no category for this page id
                if (!count($categories) || !isset($categories[$pageid])) { 
                    $catid[$k] = Jojo::insertQuery("INSERT INTO {articlecategory} (pageid) VALUES ('$pageid')");
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
               $pagelanguage = $page['pg_language'];
                // if no category for this page id
                if (!isset($categories[$pageid])) { 
                    $catid = Jojo::insertQuery("INSERT INTO {articlecategory} (pageid) VALUES ('$pageid')");
                } else {
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
            set a category for them and add any category-less articles to that category */
            foreach($articlepages as $k => $page) {
               $pageid = $page['pageid'];
                if (!isset($categories[$pageid])){
                     $catid = Jojo::insertQuery("INSERT INTO {articlecategory} (pageid) VALUES ('$pageid')");
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
            set a category for them and add any category-less articles to that category */
            foreach($articlepages as $k => $page) {
                $catid = '';
               $pageid = $page['pageid'];
               $pagelanguage = $page['pg_language'];
                if (!isset($categories[$pageid])) {
                    $catid = Jojo::insertQuery("INSERT INTO {articlecategory} (pageid) VALUES ('$pageid')");
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

