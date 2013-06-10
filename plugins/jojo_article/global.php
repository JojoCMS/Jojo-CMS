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

$numarticles = Jojo::getOption('article_num_sidebar_articles', 3);

if ($numarticles) {
$featuredfirst = (boolean)(Jojo::getOption('article_features', 'never')=='sidebar' || Jojo::getOption('article_features', 'never')=='always');
$buffer = Jojo::getOption('article_buffer', 0);
$exclude = (boolean)(Jojo::getOption('article_sidebar_exclude_current', 'no')=='yes');
//some of the articles we're getting might have expired or not yet gone live, so put in a buffer
$num = $numarticles + $buffer;
    /* Create latest Articles array for sidebar: getArticles(x, start, categoryid) = list x# of articles */
    if (Jojo::getOption('article_sidebar_categories', 'no')=='yes') {
        $categories = Jojo::selectQuery("SELECT * FROM {articlecategory}");
        $allarticles = Jojo_Plugin_Jojo_article::getArticles($num, 0, 'all',  'ar_date desc', $exclude, $include='', $minimal=true, $featuredfirst);
        $allarticles = array_slice ($allarticles, 0, $numarticles);
        $articleids = array();
        foreach ($allarticles as $a){
            $articleids[] = $a['articleid'];
        }
        $allarticles = Jojo_Plugin_Jojo_article::getItemsById($articleids);
        $smarty->assign('allarticles',  $allarticles);
        foreach ($categories as $c) {
            $catarticles = Jojo_Plugin_Jojo_article::getArticles($num, 0, $c['articlecategoryid'],  $c['sortby'], $exclude, $include='', $minimal=true, $featuredfirst );
            if (isset($catarticles[0])) {
                $catarticles = array_slice ($catarticles, 0, $numarticles);
                $articleids = array();
                foreach ($catarticles as $a){
                    $articleids[] = $a['articleid'];
                }
                $catarticles = Jojo_Plugin_Jojo_article::getItemsById($articleids);
               $smarty->assign('articles_' . str_replace(array('-', '/'), array('_', ''), $catarticles[0]['pg_url']), $catarticles);
            }
        }
    } else {
        if (Jojo::getOption('article_sidebar_randomise', 0) > 0) {
            $num = Jojo::getOption('article_sidebar_randomise', 0) + $buffer;
            $recentarticles = Jojo_Plugin_Jojo_article::getArticles($num, 0, 'all',  'ar_date desc', $exclude, $include='', $minimal=true, $featuredfirst);
            $recentarticles = array_slice ($recentarticles, 0, Jojo::getOption('article_sidebar_randomise', 0));
            shuffle($recentarticles);
        } else {
            $recentarticles = Jojo_Plugin_Jojo_article::getArticles($num, 0, 'all', 'ar_date desc', $exclude, $include='', $minimal=true, $featuredfirst);
        }
        $recentarticles = array_slice($recentarticles, 0, $numarticles);
        $articleids = array();
        foreach ($recentarticles as $a){
            $articleids[] = $a['articleid'];
        }
        $recentarticles = Jojo_Plugin_Jojo_article::getItemsById($articleids);
        if (Jojo::getOption('article_sidebar_randomise', 0) > 0) shuffle($recentarticles);
        $smarty->assign('articles', $recentarticles);
    }

}
/** Example usage in theme template:
            {if $articles}
            <div id="news">
                <h2>News</h2>
                {foreach from=$articles key=key item=article}{if $article.ar_image}<img src="images/v7000/articles/{$article.ar_image}" alt="{$article.title}" class="float-right"/>{/if}
                    <h3>{$article.title}</h3>
                    {if $article.snippet=='full' || $article.snippet > strlen($article.ar_body)}{$article.ar_body}{else}
                    <p>{$article.bodyplain|truncate:$article.snippet:"..."} <a class='links' href='{$article.url}'>{$article.readmore}</a></p>
                    {/if}
                {/foreach}
                <p class="links">&gt; <a href='{$SITEURL}/{$articles[0].pageurl}'>See all {$articles[0].pagetitle}</a></p>
            </div>
            {/if}
*/
