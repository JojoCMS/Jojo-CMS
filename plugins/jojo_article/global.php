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

$categories = Jojo::selectQuery("SELECT * FROM {articlecategory}");
$numarticles = Jojo::getOption('article_num_sidebar_articles', 3);

if ($numarticles) {
$exclude = (boolean)(Jojo::getOption('article_sidebar_exclude_current', 'no')=='yes');

    /* Create latest Articles array for sidebar: getArticles(x, start, categoryid) = list x# of articles */
    if (Jojo::getOption('article_sidebar_categories', 'no')=='yes') {
        $smarty->assign('allarticles', JOJO_Plugin_Jojo_article::getArticles($numarticles, 0, 'all', '', $exclude) );
        foreach ($categories as $c) {
            $smarty->assign('articles_' . str_replace('-', '_', $c['ac_url']), JOJO_Plugin_Jojo_article::getArticles($numarticles, 0, $c['articlecategoryid'],  $c['sortby']), $exclude );
        }
    } else {
        if (Jojo::getOption('article_sidebar_randomise', 0) > 0) {
            $recentarticles = JOJO_Plugin_Jojo_article::getArticles(Jojo::getOption('article_sidebar_randomise', 0), 0, 'all', 'ar_date DESC', $exclude);
            shuffle($recentarticles);
            $recentarticles = array_slice($recentarticles, 0, $numarticles);
        } else {
             $recentarticles = JOJO_Plugin_Jojo_article::getArticles($numarticles, 0, 'all', 'ar_date DESC', $exclude);       
        }        
        $smarty->assign('articles', $recentarticles );
    }
    
    /* Get the prefix for articles (can vary for multiple installs) for use in the theme template instead of hard coding it */
    $smarty->assign('articleshome', JOJO_Plugin_Jojo_article::_getPrefix('article', $page->getValue('pg_language')) );
    if (count($categories) && Jojo::getOption('article_sidebar_categories', 'no')=='yes') {
        foreach ($categories as $c) {
            $category = $c['ac_url'];
            $categoryid = $c['articlecategoryid'];
            $smarty->assign('articles_' . str_replace('-', '_', $category) . 'home', JOJO_Plugin_Jojo_article::_getPrefix('article', $page->getValue('pg_language'), $categoryid) );
        }
    }

}
/** Example usage in theme template:
            {if $articles}
            <div id='news' class="sidebarbox">
                <h2>News</h2>

                {foreach from=$articles key=key item=article}
                    {if $article.ar_image}<img src="images/v7000/articles/{$article.ar_image}" alt = "{$article.title}" class="right-image"/>{/if}
                    <h3>{$article.title}</h3>
                    <p class='news-content'>
                        {$article.bodyplain|truncate:150:"..."}
                        <a class='links' href='{$article.url}'>&gt; Read More</a>
                    </p>
                {/foreach}
                <p class="links">&gt; <a href='{$SITEURL}/{if _MULTILANGUAGE}{$lclanguage}/{/if}{$articleshome}/'>See all news articles</a></p>
            </div>
            {/if}
*/