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

class Jojo_Plugin_Jojo_Article_rss extends Jojo_Plugin
{

    function _getContent()
    {
        $url = $this->page['pg_url'];
        $categoryurl = preg_replace('%^(.*?)/rss/?$%im', '$1', $url); //strip /rss/ off the end of the URL
        $_CATEGORIES = (Jojo::getOption('article_enable_categories', 'no') == 'yes') ? true : false ;
        $categorydata =  ($_CATEGORIES) ? Jojo::selectRow("SELECT articlecategoryid FROM {articlecategory} WHERE ac_url = '$categoryurl'") : '';
        $categoryid = ($_CATEGORIES && count($categorydata)) ? $categorydata['articlecategoryid'] : '';
        //echo $url.' - '.$categoryurl.' - '.$categoryid ;exit;
        $full = (Jojo::getOption('article_full_rss_description') == 'yes') ? true : false;
        $rss  = "<?xml version=\"1.0\" ?".">\n";
        $rss .= "<rss version=\"2.0\">\n";
        $rss .= "<channel>\n";
        $rss .= "<title>" . htmlentities(_SITETITLE) . "</title>\n";
        $rss .= "<description>" . htmlentities(Jojo::getOption('sitedesc', Jojo::getOption('sitetitle'))) . "</description>\n";
        $rss .= "<link>"._SITEURL . "</link>\n";
        $rss .= "<copyright>" . htmlentities(_SITETITLE) . " " . date('Y', strtotime('now')) . "</copyright>\n";

        $limit = Jojo::getOption('article_rss_num_articles');
        if (empty($limit)) $limit = 15;
        if ($_CATEGORIES && !empty($categoryid)) {
            $articles = Jojo::selectQuery("SELECT * FROM {article} WHERE `ar_livedate`<".time()." AND (`ar_expirydate`<=0 OR `ar_expirydate`>".time().") AND (ar_category = '$categoryid') ORDER BY ar_date DESC LIMIT $limit");
        } else {
            $articles = Jojo::selectQuery("SELECT * FROM {article} WHERE `ar_livedate`<".time()." AND (`ar_expirydate`<=0 OR `ar_expirydate`>".time().") ORDER BY ar_date DESC LIMIT $limit");
        }
        $n = count($articles);
        for ($i = 0; $i < $n; $i++) {
            $articles[$i]['ar_body'] = Jojo::relative2absolute($articles[$i]['ar_body'], _SITEURL);
            /* chop the article up to the first [[snip]] */
            if ($full) {
                $articles[$i]['ar_body'] = str_ireplace('[[snip]]','',$articles[$i]['ar_body']);
            } else {
                $arr = Jojo::iExplode('[[snip]]', $articles[$i]['ar_body']);
                if (count($arr) === 1) {
                    $articles[$i]['ar_body'] = substr($articles[$i]['ar_body'], 0, Jojo::getOption('article_rss_truncate', 800)) . ' ...';
                } else {
                    $articles[$i]['ar_body'] = $arr[0];
                }
            }
            $source = _SITEURL . "/" . Jojo_Plugin_Jojo_article::getArticleUrl($articles[$i]['articleid'], $articles[$i]['ar_url'], $articles[$i]['ar_title'], $articles[$i]['ar_language'], $articles[$i]['ar_category']);
            if (Jojo::getOption('article_feed_source_link') == 'yes') $articles[$i]['ar_body'] .= '<p>Source: <a href="'.$source.'">'.$articles[$i]['ar_title'].'</a></p>';
            $rss .= "<item>\n";
            $rss .= "<title>" . htmlentities($articles[$i]['ar_title'], ENT_QUOTES, 'UTF-8') . "</title>\n";
            $rss .= "<description>" . str_replace('&middot;', '', $this->rssEscape($articles[$i]['ar_body'])) . "</description>\n";
            $rss .= "<link>". $source . "</link>\n";
            $rss .= "<pubDate>" . Jojo::mysql2date($articles[$i]['ar_date'], 'rss') . "</pubDate>\n";
            $rss .= "</item>\n";
        }
        $rss .= "</channel>\n";
        $rss .= "</rss>\n";

        header('Content-type: application/xml');
        echo $rss;
        exit;
    }

    function getCorrectUrl()
    {
        /* Act like a file, not a folder */
        //$url = rtrim(parent::getCorrectUrl(), '/');
        $url = parent::getCorrectUrl();
        return $url;
    }

    function rssEscape($data) {
        return str_replace('<', '&lt;', str_replace('>', '&gt;', str_replace('"', '&quot;', str_replace('&', '&amp;', $data))));
    }
}