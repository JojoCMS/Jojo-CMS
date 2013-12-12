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

class Jojo_Plugin_Jojo_article extends Jojo_Plugin
{

/*
* Core
*/

    /* Get articles  */
    static function getArticles($num=false, $start = 0, $categoryid='all', $sortby='ar_date desc', $exclude=false, $include=false, $minimal=false, $featuredfirst=false) {
        global $page;
        $now = time();
        if ($categoryid == 'all' && $include != 'alllanguages') {
            $categoryid = array();
            $sectionpages = self::getPluginPages('', $page->page['root']);
            foreach ($sectionpages as $s) {
                $categoryid[] = $s['articlecategoryid'];
            }
        }
        if (is_array($categoryid)) {
             $categoryquery = " AND ar_category IN ('" . implode("','", $categoryid) . "')";
        } else {
            $categoryquery = is_numeric($categoryid) ? " AND ar_category = '$categoryid'" : '';
        }
        /* if calling page is an article, Get current article, exclude from the list and up the limit by one */
        $exclude = ($exclude && Jojo::getOption('article_sidebar_exclude_current', 'no')=='yes' && $page->page['pg_link']=='jojo_plugin_jojo_article' && (Jojo::getFormData('id') || Jojo::getFormData('url'))) ? (Jojo::getFormData('url') ? Jojo::getFormData('url') : Jojo::getFormData('id')) : '';
        if ($num && $exclude) $num++;
        $shownumcomments = (boolean)(!$minimal && class_exists('Jojo_Plugin_Jojo_comment') && Jojo::getOption('comment_show_num', 'no') == 'yes');
        $query  = "SELECT " . ($minimal ? "ar.articleid, ar_date, ar_title, ar_author, ar_livedate, ar_expirydate, ar_url," : "ar.*,  ac.*,");
        $query  .= " p.pageid, pg_menutitle, pg_title, pg_url, pg_status, pg_livedate, pg_expirydate";
        $query .= $shownumcomments ? ", COUNT(com.itemid) AS numcomments" : '';
        $query .= " FROM {article} ar";
        $query .= " LEFT JOIN {articlecategory} ac ON (ar.ar_category=ac.articlecategoryid) LEFT JOIN {page} p ON (ac.pageid=p.pageid)";
        $query .= $shownumcomments ? " LEFT JOIN {comment} com ON (com.itemid = ar.articleid AND com.plugin = 'jojo_article')" : '';
        $query .= " WHERE 1" . $categoryquery;
        $query .= $featuredfirst ? " AND ar_featured=1" : '';
        $query .= $shownumcomments ? " GROUP BY articleid" : '';
        $query .= $num ? " ORDER BY $sortby LIMIT $start,$num" : '';
        $articles = Jojo::selectQuery($query);
        $articles = self::cleanItems($articles, $exclude, $include);
        $articles = $minimal ? $articles : self::formatItems($articles, $exclude, $include);
        if (!$num)  $articles = self::sortItems($articles, $sortby);
        if ($featuredfirst) {
         	$numfeatured = count($articles);
        	$query = str_replace('ar_featured=1', 'ar_featured=0', $query);
       		if ($start) {
        		$query = $num ? str_replace('LIMIT ' . $start . ',', 'LIMIT ' . ($start - $numfeatured) . ',', $query) : $query;
			} else {
        		$query = $num ? str_replace('LIMIT 0,' . $num, 'LIMIT 0,' . ($num - $numfeatured), $query) : $query;
			}
        	$nonfeaturedarticles = Jojo::selectQuery($query);
			$nonfeaturedarticles = self::cleanItems($nonfeaturedarticles, $exclude, $include);
			$nonfeaturedarticles = $minimal ? $nonfeaturedarticles : self::formatItems($nonfeaturedarticles, $exclude, $include);
			if (!$num)  $nonfeaturedarticles = self::sortItems($nonfeaturedarticles, $sortby);
			$articles = $start ? $nonfeaturedarticles : array_merge($articles, $nonfeaturedarticles);
        }
       $articles = array_values($articles);
       return $articles;
    }

     /* get items by id - accepts either an array of ids returning a results array, or a single id returning a single result  */
    static function getItemsById($ids = false, $sortby=false, $include=false) {
        $shownumcomments = (boolean)(class_exists('Jojo_Plugin_Jojo_comment') && Jojo::getOption('comment_show_num', 'no') == 'yes');
        $query  = "SELECT ar.articleid as id, ar.*, ac.*, p.pageid, pg_menutitle, pg_title, pg_url, pg_status, pg_livedate, pg_expirydate";
        $query .= $shownumcomments ? ", COUNT(com.itemid) AS numcomments" : '';
        $query .= " FROM {article} ar";
        $query .= " LEFT JOIN {articlecategory} ac ON (ar.ar_category=ac.articlecategoryid) LEFT JOIN {page} p ON (ac.pageid=p.pageid)";
        $query .= $shownumcomments ? " LEFT JOIN {comment} com ON (com.itemid = ar.articleid AND com.plugin = 'jojo_article')" : '';
        $query .=  is_array($ids) ? " WHERE articleid IN ('". implode("',' ", $ids) . "')" : " WHERE articleid=$ids";
        $query .= $shownumcomments ? " GROUP BY articleid" : '';
        $items = Jojo::selectAssoc($query);
        if ($items) {
            if (is_array($ids) && $sortby) { 
            	$items = self::sortItems($items, $sortby);
            } elseif (is_array($ids)) {
            	foreach ($ids as $i) {
            		if (isset($items[$i])) {
            			$sorteditems[] =  $items[$i];
            		}
            	}
            	$items = $sorteditems;
            } 
			$items = self::cleanItems($items, '', $include);
			$items = self::formatItems($items, '', $include);
        } 
        if ($items) {
        	return is_array($ids) ? $items : $items[0];
        }
        return false;
    }

    /* clean items for output */
    static function cleanItems($items, $exclude=false, $include=false) {
        $now    = time();
        foreach ($items as $k=>&$i){
            $pagedata = Jojo_Plugin_Core::cleanItems(array($i), $include);
            if (!$pagedata || $i['ar_livedate']>$now || (!empty($i['ar_expirydate']) && $i['ar_expirydate']<$now) || (!empty($i['articleid']) && $i['articleid']==$exclude)  || (!empty($i['ar_url']) && $i['ar_url']==$exclude)) {
                unset($items[$k]);
                continue;
            } else {
                $i['pagetitle'] = $pagedata[0]['title'];
                $i['pageurl']   = $pagedata[0]['url'];
            }
        }
        $items = array_values($items);
        return $items;
    }

    /* clean items for output */
    static function formatItems($items, $exclude=false, $include=false) {
        foreach ($items as $k=>&$i){
            $i['id']           = $i['articleid'];
            $i['title']        = Jojo::htmlspecialchars($i['ar_title']);
            $i['seotitle']        = Jojo::htmlspecialchars($i['ar_seotitle']);
            $i['author']        = Jojo::htmlspecialchars($i['ar_author']);
            // Snip for the index description
            $splitcontent = Jojo::iExplode('[[snip]]', $i['ar_body']);
            $i['bodysnip'] = array_shift($splitcontent);
            /* Strip all tags and template include code ie [[ ]] */
            $i['bodysnip'] = strpos($i['bodysnip'], '[[')!==false ? preg_replace('/\[\[.*?\]\]/', '',  $i['bodysnip']) : $i['bodysnip'];
            $i['bodyplain'] = trim(strip_tags($i['bodysnip']));
            $i['description'] = $i['ar_desc'] ? Jojo::htmlspecialchars($i['ar_desc']) : (strlen($i['bodyplain']) >400 ?  substr($mbody=wordwrap($i['bodyplain'], 400, '$$'), 0, strpos($mbody,'$$')) : $i['bodyplain']);
            $i['snippet']       = isset($i['snippet']) ? $i['snippet'] : '400';
            $i['thumbnail']       = isset($i['thumbnail']) ? $i['thumbnail'] : 's150';
            $i['mainimage']       = isset($i['mainimage']) ? $i['mainimage'] : 'v60000';
            $i['readmore'] = isset($i['readmore']) ? str_replace(' ', '&nbsp;', Jojo::htmlspecialchars($i['readmore'])) : '&gt;&nbsp;read&nbsp;more';
            $i['date']       = $i['ar_date'];
            $i['datefriendly'] = isset($i['dateformat']) && !empty($i['dateformat']) ? strftime($i['dateformat'], $i['ar_date']) :  Jojo::formatTimestamp($i['ar_date'], "medium");
            $i['image'] = !empty($i['ar_image']) ? 'articles/' . urlencode($i['ar_image']) : '';
            $i['url']          = self::getArticleUrl($i['articleid'], $i['ar_url'], $i['ar_title'], $i['pageid'], $i['ar_category']);
            $i['plugin']     = 'jojo_article';
             if (class_exists('Jojo_Plugin_Jojo_Tags') && Jojo::getOption('article_tags', 'no') == 'yes' ) {
                /* Split up tags for display */
                $i['tags'] = Jojo_Plugin_Jojo_Tags::getTags('jojo_article', $i['articleid']);
            }
           unset($items[$k]['ar_bbbody']);
        }
        $items = array_values($items);
        return $items;
    }

    /* sort items for output */
    static function sortItems($items, $sortby=false) {
        if ($sortby) {
            $order = "date";
            $reverse = false;
            switch ($sortby) {
              case "ar_date desc":
                $order="date";
                $reverse = true;
                break;
              case "ar_title asc":
                $order="name";
                break;
              case "ar_author":
                $order="author";
                break;
              case "ar_livedate desc":
                $order="live";
                break;
            }
            usort($items, array('Jojo_Plugin_Jojo_article', $order . 'sort'));
            $items = $reverse ? array_reverse($items) : $items;
        }
        return $items;
    }

    private static function namesort($a, $b)
    {
         if ($a['ar_title']) {
            return strcmp($a['ar_title'],$b['ar_title']);
        }
    }

    private static function authorsort($a, $b)
    {
         if ($a['ar_author']) {
            return strcmp($a['ar_author'],$b['ar_author']);
        }
    }

    private static function datesort($a, $b)
    {
         if ($a['ar_date']) {
            return strnatcasecmp($a['ar_date'],$b['ar_date']);
         }
    }

    private static function livesort($a, $b)
    {
         if ($a['ar_livedate']) {
            return strcmp($b['ar_livedate'],$a['ar_livedate']);
         }
    }

    /*
     * calculates the URL for the article - requires the article ID, but works without a query if given the URL or title from a previous query
     *
     */
    static function getArticleUrl($id=false, $url=false, $title=false, $pageid=false, $category=false )
    {
        $pageprefix = Jojo::getPageUrlPrefix($pageid);

        /* URL specified */
        if (!empty($url)) {
            return $pageprefix . self::_getPrefix('article', $category) . '/' . $url . '/';
         }
        /* ID + title specified */
        if ($id && !empty($title)) {
            return $pageprefix . self::_getPrefix('article', $category) . '/' . $id . '/' .  Jojo::cleanURL($title) . '/';
        }
        /* use the ID to find either the URL or title */
        if ($id) {
            $article = Jojo::selectRow("SELECT ar_url, ar_title, ar_category, p.pageid FROM {article} ar LEFT JOIN {articlecategory} ac ON (ar.ar_category=ac.articlecategoryid) LEFT JOIN {page} p ON (ac.pageid=p.pageid) WHERE articleid = ?", array($id));
             if ($article) {
                return self::getArticleUrl($id, $article['ar_url'], $article['ar_title'], $article['pageid'], $article['ar_category']);
            }
         }
        /* No article matching the ID supplied or no ID supplied */
        return false;
    }


    function _getContent()
    {
        global $smarty;
        $content = array();
        $pageid = $this->page['pageid'];
        $pageprefix = Jojo::getPageUrlPrefix($pageid);
        $smarty->assign('multilangstring', $pageprefix);

        if (class_exists('Jojo_Plugin_Jojo_comment') && Jojo::getOption('comment_subscriptions', 'no') == 'yes') {
            Jojo_Plugin_Jojo_comment::processSubscriptionEmails();
        }

        /* Are we looking at an article or the index? */
        $articleid = Jojo::getFormData('id',        0);
        $url       = Jojo::getFormData('url',      '');
        $action    = Jojo::getFormData('action',   '');
        $categorydata =  Jojo::selectRow("SELECT * FROM {articlecategory} WHERE pageid = ?", $pageid);
        $categorydata['type'] = isset($categorydata['type']) ? $categorydata['type'] : 'normal';
        if ($categorydata['type']=='index') {
            $categoryid = 'all';
        } elseif ($categorydata['type']=='parent') {
            $childcategories = Jojo::selectQuery("SELECT articlecategoryid FROM {page} p  LEFT JOIN {articlecategory} c ON (c.pageid=p.pageid) WHERE pg_parent = ? AND pg_link = 'jojo_plugin_jojo_article'", $pageid);
            foreach ($childcategories as $c) {
                $categoryid[] = $c['articlecategoryid'];
            }
            $categoryid[] = $categorydata['articlecategoryid'];
        } else {
            $categoryid = $categorydata['articlecategoryid'];
        }
        $sortby = $categorydata ? $categorydata['sortby'] : '';

        /* handle unsubscribes */
        if ($action == 'unsubscribe') {
            $code      = Jojo::getFormData('code',      '');
            $articleid = Jojo::getFormData('articleid', '');
            if (Jojo_Plugin_Jojo_comment::removeSubscriptionByCode($code, $articleid, 'jojo_article')) {
                $content['content'] = 'Subscription removed.<br />';
            } else {
                $content['content'] = 'This unsubscribe link is inactive, or you have already been unsubscribed.<br />';
            }
            $content['content'] .= 'Return to <a href="' . self::getArticleUrl($articleid) . '">article</a>.';
            return $content;
        }

        if ($action == 'rss') {
            $articles = self::getArticles(100, 0, $categoryid, $sortby, $exclude=false, $include='showhidden');
            $rssfields = array(
                'pagetitle' => $this->page['pg_title'],
                'pageurl' => _SITEURL . '/' . $pageprefix . $this->page['pg_url'] . '/',
                'title' => 'ar_title',
                'body' => 'ar_body',
                'url' => 'url',
                'date' => 'date',
                'datetype' => 'unix',
                'options' => array('snip' => (isset($categorydata['snippet']) ? $categorydata['snippet'] : '400' ), 'imagesize' => '')
            );
            $articles = array_slice($articles, 0, Jojo::getOption('rss_num_items', 15));
            Jojo::getFeed($articles, $rssfields);
        }

		$featuredfirst = (boolean)(Jojo::getOption('article_features', 'never')=='index' || Jojo::getOption('article_features', 'never')=='always');
        $articles = self::getArticles('', '', $categoryid, $sortby, $exclude=false, $include='showhidden', $minimal=true, $featuredfirst);

        if ($articleid || !empty($url)) {
            /* find the current, next and previous items */
            $article = array();
            $prevarticle = array();
            $nextarticle = array();
            $next = false;
            foreach ($articles as $a) {
                if (!empty($url) && $url==$a['ar_url']) {
                    $article = $a;
                    $next = true;
               } elseif ($articleid==$a['articleid']) {
                    $article = $a;
                    $next = true;
                } elseif ($next==true) {
                    $nextarticle = $a;
                     break;
                } else {
                    $prevarticle = $a;
                }
            }

            /* If the item can't be found, return a 404 */
            if (!$article) {
                include(_BASEPLUGINDIR . '/jojo_core/404.php');
                exit;
            } else {
                $article = self::getItemsById($article['articleid'], '', $include='showhidden');
            }

            if ($modarticle = Jojo::runHook('modify_article', array($article))) {
                $article = $modarticle;
            }
            /* Get the specific article */
            $articleid = $article['articleid'];
            $article['ar_datefriendly'] = Jojo::mysql2date($article['ar_date'], "long");

            /* calculate the next and previous articles */
            if (Jojo::getOption('article_next_prev') == 'yes') {
                if (!empty($nextarticle)) {
                    $smarty->assign('nextarticle', self::getItemsById($nextarticle['articleid']));
                }
                if (!empty($prevarticle)) {
                    $smarty->assign('prevarticle', self::getItemsById($prevarticle['articleid']));
                }
            }

            /* Get tags if used */
            if (class_exists('Jojo_Plugin_Jojo_Tags')) {
                /* Split up tags for display */
                $tags = Jojo_Plugin_Jojo_Tags::getTags('jojo_article', $articleid);
                $smarty->assign('tags', $tags);

                /* generate tag cloud of tags belonging to this article */
                $article_tag_cloud_minimum = Jojo::getOption('article_tag_cloud_minimum');
                if (!empty($article_tag_cloud_minimum) && ($article_tag_cloud_minimum < count($tags))) {
                    $itemcloud = Jojo_Plugin_Jojo_Tags::getTagCloud('', $tags);
                    $smarty->assign('itemcloud', $itemcloud);
                }
               /* get related articles if tags plugin installed and option enabled */
                $numrelated = Jojo::getOption('article_num_related');
                if ($numrelated) {
                    $related = Jojo_Plugin_Jojo_Tags::getRelated('jojo_article', $articleid, $numrelated, 'jojo_article'); //set the last argument to 'jojo_article' to restrict results to only articles
                    $smarty->assign('related', $related);
                }
            }

            /* Get Comments if used */
            if (class_exists('Jojo_Plugin_Jojo_comment') && (!isset($article['comments']) || $article['comments']) ) {
                /* Was a comment submitted? */
                if (Jojo::getFormData('comment', false)) {
                    Jojo_Plugin_Jojo_comment::postComment($article);
                }
               $articlecommentsenabled = (boolean)(isset($article['ar_comments']) && $article['ar_comments']=='yes');
               $commenthtml = Jojo_Plugin_Jojo_comment::getComments($article['id'], $article['plugin'], $article['pageid'], $articlecommentsenabled);
               $smarty->assign('commenthtml', $commenthtml);
            }

            /* Add breadcrumb */
            $breadcrumbs                      = $this->_getBreadCrumbs();
            $breadcrumb                       = array();
            $breadcrumb['name']               = $article['title'];
            $breadcrumb['rollover']           = $article['description'];
            $breadcrumb['url']                = $article['url'];
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;

            /* Assign article content to Smarty */
            $smarty->assign('jojo_article', $article);
            $smarty->assign('jojo_articles', $articles);

            /* Prepare fields for display */
            if (isset($article['ar_htmllang'])) {
                // Override the language setting on this page if necessary.
                $content['pg_htmllang'] = $article['ar_htmllang'];
                $smarty->assign('pg_htmllang', $article['ar_htmllang']);
            }
            $content['title']            = $article['title'];
            $content['seotitle']         = Jojo::either($article['seotitle'], $article['title']);
            $content['breadcrumbs']      = $breadcrumbs;

            if (!empty($article['ar_metadesc'])) {
                $content['meta_description'] = $article['ar_metadesc'];
            } else {
                $meta_description_template = Jojo::getOption('article_meta_description', '[article] - [body]... ');
                $metafilters = array(
                        '[article]',
                        '[title]',
                        '[site]',
                        '[body]',
                        '[author]'
                        );
                $metafilterreplace = array(
                        $article['title'],
                        $article['title'],
                        _SITETITLE,
                        $article['description'],
                        $article['ar_author']
                        );
                        $content['meta_description'] = str_replace($metafilters, $metafilterreplace, $meta_description_template);
            }
            $content['metadescription']  = $content['meta_description'];
            if ((boolean)(Jojo::getOption('ogdata', 'no')=='yes')) {
                $content['ogtags']['description'] = $article['description'];
                $content['ogtags']['image'] = $article['image'] ? _SITEURL .  '/images/' . ($article['thumbnail'] ? $article['thumbnail'] : 's150') . '/' . $article['image'] : '';
                $content['ogtags']['title'] = $article['title'];
            }
            $content['content'] = $smarty->fetch('jojo_article.tpl');

        } else {

            /* Article index section */
            $pagenum = Jojo::getFormData('pagenum', 1);
            if ($pagenum[0] == 'p') {
                $pagenum = substr($pagenum, 1);
            }
            $smarty->assign('pagenum', $pagenum);

            /* get number of articles for pagination */
            $articlesperpage = Jojo::getOption('articlesperpage', 40);
            $start = ($articlesperpage * ($pagenum-1));
            $numarticles = count($articles);
            $numpages = ceil($numarticles / $articlesperpage);
            /* calculate pagination */
            if ($numpages == 1) {
                $pagination = '';
            } else {
                $smarty->assign('numpages', $numpages);
                $smarty->assign('pageurl', $pageprefix . self::_getPrefix('article', $categorydata['articlecategoryid']));
                $pagination = $smarty->fetch('pagination.tpl');
            }

            $smarty->assign('pagination', $pagination);

            /* clear the meta description to avoid duplicate content issues */
            $content['metadescription'] = '';

            /* get article content for just the ones on the index page and assign to Smarty */
            $articles = array_slice($articles, $start, $articlesperpage);
            $articleids = array();
            foreach ($articles as $k=>$a){
                $articleids[$k] = $a['articleid'];
            }
			$sortby = $featuredfirst ? false : $sortby;
            $articles = self::getItemsById($articleids, $sortby, $include='showhidden');
            $smarty->assign('jojo_articles', $articles);

            $content['content'] = $smarty->fetch('jojo_article_index.tpl');
       }
        return $content;
    }

    static function getPluginPages($for='', $section=0)
    {
        global $sectiondata;
        $cacheKey = 'articles';
        /* Have we got a cached result? */
        static $_pluginpages;
        if (isset($_pluginpages[$cacheKey])) {
            return $_pluginpages[$cacheKey];
        }
        /* Cache some stuff */
        $items =  Jojo::selectAssoc("SELECT p.pageid AS id, c.*, p.*  FROM {articlecategory} c LEFT JOIN {page} p ON (c.pageid=p.pageid) ORDER BY pg_parent, pg_order");
        // use core function to clean out any pages based on permission, status, expiry etc
        $items =  Jojo_Plugin_Core::cleanItems($items, $for);
        foreach ($items as $k=>$i){
            if ($section && $section != $i['root']) {
                unset($items[$k]);
                continue;
            }
        }
        if ($items) {
            $_pluginpages[$cacheKey] = $items;
        } else {
            $_pluginpages[$cacheKey] = array();
        }
        return $_pluginpages[$cacheKey];
    }

    public static function sitemap($sitemap)
    {
        global $page;
        /* See if we have any article sections to display and find all of them */
        $indexes =  self::getPluginPages('sitemap');
        if (!count($indexes)) {
            return $sitemap;
        }

        if (Jojo::getOption('article_inplacesitemap', 'separate') == 'separate') {
            /* Remove any existing links to the articles section from the page listing on the sitemap */
            foreach($sitemap as $j => $section) {
                $sitemap[$j]['tree'] = self::_sitemapRemoveSelf($section['tree']);
            }
            $_INPLACE = false;
        } else {
            $_INPLACE = true;
        }

        $now = strtotime('now');
        $limit = 15;
        $articlesperpage = Jojo::getOption('articlesperpage', 40);
         /* Make sitemap trees for each articles instance found */
        foreach($indexes as $k => $i){
            $categoryid = $i['articlecategoryid'];
            $sortby = $i['sortby'];

            /* Create tree and add index and feed links at the top */
            $articletree = new hktree();
            $indexurl = $i['url'];
            if ($_INPLACE) {
                $parent = 0;
            } else {
               $articletree->addNode('index', 0, $i['title'], $indexurl);
               $parent = 'index';
            }

            $articles = self::getArticles('', '', $categoryid, $sortby);
            $n = count($articles);

            /* Trim items down to first page and add to tree*/
            $articles = array_slice($articles, 0, $articlesperpage);
            foreach ($articles as $a) {
                $articletree->addNode($a['id'], $parent, $a['title'], $a['url']);
            }

            /* Get number of pages for pagination */
            $numpages = ceil($n / $articlesperpage);
            /* calculate pagination */
            if ($numpages > 1) {
                for ($p=2; $p <= $numpages; $p++) {
                    $url = $indexurl .'p' . $p .'/';
                    $nodetitle = $i['title'] . ' (p.' . $p . ')';
                    $articletree->addNode('p' . $p, $parent, $nodetitle, $url);
                }
            }
            if ($i['rsslink']) {
                /* Add RSS link for the plugin page */
                $articletree->addNode('rss', $parent, $i['title'] . ' RSS Feed', $indexurl . 'rss/');
            }

            /* Add to the sitemap array */
            if ($_INPLACE) {
                /* Add inplace */
                $url = $i['url'];
                $sitemap['pages']['tree'] = self::_sitemapAddInplace($sitemap['pages']['tree'], $articletree->asArray(), $url);
            } else {
                $mldata = Jojo::getMultiLanguageData();
                /* Add to the end */
                $sitemap["articles$k"] = array(
                    'title' => $i['title'] . ( _MULTILANGUAGE ? ' (' . ucfirst($mldata['sectiondata'][$i['root']]['name']) . ')' : ''),
                    'tree' => $articletree->asArray(),
                    'order' => 3 + $k,
                    'header' => '',
                    'footer' => '',
                    );
            }
        }
        return $sitemap;
    }

    static function _sitemapAddInplace($sitemap, $toadd, $url)
    {
        foreach ($sitemap as $k => $t) {
            if ($t['url'] == $url) {
                $sitemap[$k]['children'] = isset($sitemap[$k]['children']) ? array_merge($toadd, $sitemap[$k]['children']): $toadd;
            } elseif (isset($sitemap[$k]['children'])) {
                $sitemap[$k]['children'] = self::_sitemapAddInplace($t['children'], $toadd, $url);
            }
        }
        return $sitemap;
    }

    static function _sitemapRemoveSelf($tree)
    {
        static $urls;

        if (!is_array($urls)) {
            $urls = array();
            $indexes =  self::getPluginPages('sitemap');
            if (count($indexes)==0) {
               return $tree;
            }
            foreach($indexes as $key => $i){
                $urls[] = $i['url'];
            }
        }

        foreach ($tree as $k =>$t) {
            if (in_array($t['url'], $urls)) {
                unset($tree[$k]);
            } else {
                $tree[$k]['children'] = self::_sitemapRemoveSelf($t['children']);
            }
        }
        return $tree;
    }

    /**
    /**
     * XML Sitemap filter
     *
     * Receives existing sitemap and adds article pages
     */
    static function xmlsitemap($sitemap)
    {
        /* Get articles from database */
        $articles = self::getArticles('', '', 'all', '', '', 'alllanguages');
        $now = time();
        $indexes =  self::getPluginPages('xmlsitemap');
        $ids=array();
        foreach ($indexes as $i) {
            $ids[$i['articlecategoryid']] = true;
        }
        /* Add articles to sitemap */
        foreach($articles as $k => $a) {
            // strip out articles from expired pages
            if (!isset($ids[$a['ar_category']])) {
                unset($articles[$k]);
                continue;
            }
            $url = _SITEURL . '/'. $a['url'];
            $lastmod = $a['date'];
            $priority = 0.6;
            $changefreq = '';
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }
        /* Return sitemap */
        return $sitemap;
    }

    /**
     * Removes any [[snip]] tags leftover in the content before outputting
     */
    static function removesnip($data)
    {
        $data = str_ireplace('[[snip]]','',$data);
        return $data;
    }

    /**
     * Get the url prefix for a particular part of this plugin
     */
    static function _getPrefix($for='article', $categoryid=false) {
        $cacheKey = $for;
        $cacheKey .= ($categoryid) ? $categoryid : 'false';

        /* Have we got a cached result? */
        static $_cache;
        if (isset($_cache[$cacheKey])) {
            return $_cache[$cacheKey];
        }

        /* Cache some stuff */
        $res = Jojo::selectRow("SELECT p.pageid, pg_title, pg_url FROM {page} p LEFT JOIN {articlecategory} c ON (c.pageid=p.pageid) WHERE `articlecategoryid` = '$categoryid'");
        if ($res) {
            $_cache[$cacheKey] = !empty($res['pg_url']) ? $res['pg_url'] : $res['pageid'] . '/' . $res['pg_title'];
        } else {
            $_cache[$cacheKey] = '';
        }
        return $_cache[$cacheKey];
    }

    static function getPrefixById($id=false) {
        if ($id) {
            $data = Jojo::selectRow("SELECT articlecategoryid, pageid FROM {article} LEFT JOIN {articlecategory} ON (ar_category=articlecategoryid) WHERE articleid = ?", array($id));
            if ($data) {
                $fullprefix = Jojo::getPageUrlPrefix($data['pageid']) . self::_getPrefix('', $data['articlecategoryid']);
                return $fullprefix;
            }
        }
        return false;
    }

    function getCorrectUrl()
    {
        global $page;
        $pageid  = $page->page['pageid'];
        $id = Jojo::getFormData('id',     0);
        $url       = Jojo::getFormData('url',    '');
        $action    = Jojo::getFormData('action', '');
        $pagenum   = Jojo::getFormData('pagenum', 1);

        $data = Jojo::selectRow("SELECT articlecategoryid FROM {articlecategory} WHERE pageid=?", $pageid);
        $categoryid = !empty($data['articlecategoryid']) ? $data['articlecategoryid'] : '';

        if ($pagenum[0] == 'p') {
            $pagenum = substr($pagenum, 1);
        }

        /* unsubscribing */
        if ($action == 'unsubscribe') {
            return _PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        /* the special URL for the latest article */
        if ($action == 'latest') {
            $article = Jojo::selectRow("SELECT a.*, p.pageid FROM {article} a  LEFT JOIN {articlecategory} ac ON (a.ar_category=ac.articlecategoryid) LEFT JOIN {page} p ON (ac.pageid=p.pageid) ORDER BY ar_date DESC, ar_title");
            return _SITEURL . '/' . self::getArticleUrl($article['articleid'], $article['ar_url'], $article['ar_title'], $article['pageid'], $article['ar_category']);
        }
        $correcturl = self::getArticleUrl($id, $url, null, $pageid, $categoryid);

        if ($correcturl) {
            return _SITEURL . '/' . $correcturl;
        }

        /* index with pagination */
        if ($pagenum > 1) return parent::getCorrectUrl() . 'p' . $pagenum . '/';

        if ($action == 'rss') return parent::getCorrectUrl() . 'rss/';

        /* index - default */
        return parent::getCorrectUrl();
    }

    static public function isArticleUrl($uri)
    {
        $prefix = false;
        $getvars = array();
        /* Check the suffix matches and extract the prefix */
        if (preg_match('#^(.+)/latest$#', $uri, $matches)) {
            /* "$prefix/[action:latest]" eg "articles/latest/" */
            $prefix = $matches[1];
            $getvars = array('action' => 'latest');
        } elseif (preg_match('#^(.+)/unsubscribe/([0-9]+)/([a-zA-Z0-9]{16})$#', $uri, $matches)) {
            /* "$prefix/[action:unsubscribe]/[articleid:integer]/[code:[a-zA-Z0-9]{16}]" eg "articles/unsubscribe/34/7MztlFyWDEKiSoB1/" */
            $prefix = $matches[1];
            $getvars = array(
                        'action' => 'unsubscribe',
                        'articleid' => $matches[2],
                        'code' => $matches[3]
                        );
        /* Check for standard plugin url format matches */
        } elseif ($uribits = parent::isPluginUrl($uri)) {
            $prefix = $uribits['prefix'];
            $getvars = $uribits['getvars'];
        } else {
            return false;
        }
        /* Check the prefix matches */
        if ($res = self::checkPrefix($prefix)) {
            /* If full uri matches a prefix it's an index page so ignore it and let the page plugin handle it */
            if (self::checkPrefix(trim($uri, '/'))) {
                return false;
            }

            /* The prefix is good, pass through uri parts */
            foreach($getvars as $k => $v) {
                $_GET[$k] = $v;
            }
            return true;
        }
        return false;
    }

    /**
     * Check if a prefix is an article prefix
     */
    static public function checkPrefix($prefix)
    {
        static $_prefixes, $categories;
        if (!isset($categories)) {
            /* Initialise cache */
            $categories = array(false);
            $categories = array_merge($categories, Jojo::selectAssoc("SELECT articlecategoryid, articlecategoryid as articlecategoryid2 FROM {articlecategory}"));
            $_prefixes = array();
        }
        /* Check if it's in the cache */
        if (isset($_prefixes[$prefix])) {
            return $_prefixes[$prefix];
        }
        /* Check everything */
        foreach($categories as $category) {
            $testPrefix = self::_getPrefix('article', $category);
            $_prefixes[$testPrefix] = true;
            if ($testPrefix == $prefix) {
                /* The prefix is good */
                return true;
            }
        }
        /* Didn't match */
        $_prefixes[$testPrefix] = false;
        return false;
    }

    static function getNavItems($pageid, $selected=false)
    {
        $nav = array();
        $section = Jojo::getSectionRoot($pageid);
        $articlepages = self::getPluginPages('', $section);
        if (!$articlepages || !isset($articlepages[$pageid])) return $nav;
        $categoryid = $articlepages[$pageid]['articlecategoryid'];
        $sortby = $articlepages[$pageid]['sortby'];
        $items = isset($articlepages[$pageid]['addtonav']) && $articlepages[$pageid]['addtonav'] ? self::getArticles('', '', $categoryid, $sortby) : '';
        if (!$items) return $nav;
        //if the page is currently selected, check to see if an item has been called
        if ($selected) {
            $id = Jojo::getFormData('id', 0);
            $url = Jojo::getFormData('url', '');
        }
        foreach ($items as $i) {
            $nav[$i['id']]['url'] = $i['url'];
            $nav[$i['id']]['title'] = ($i['seotitle'] ? $i['seotitle'] : ($i['ar_desc'] ? Jojo::htmlspecialchars($i['ar_desc']) : $i['title']));
            $nav[$i['id']]['label'] = $i['title'];
            $nav[$i['id']]['selected'] = (boolean)($selected && (($id && $id== $i['id']) ||(!empty($url) && $i['url'] == $url)));
        }
        return $nav;
    }
    static function admin_action_after_save_article($id)
    {
        $article = self::getItemsById($id);
        if (empty($article['ar_htmllang'])) {
            $mldata = Jojo::getMultiLanguageData();
            $htmllanguage =  $mldata['sectiondata'][Jojo::getSectionRoot($article['pageid'])]['lc_defaultlang'];
            Jojo::updateQuery("UPDATE {article} SET `ar_htmllang`=? WHERE `articleid`=?", array($htmllanguage, $id));
        }

        Jojo::updateQuery("UPDATE {option} SET `op_value`=? WHERE `op_name`='article_last_updated'", time());
        return true;
    }

    // Sync the articategory data over to the page table
    static function admin_action_after_save_articlecategory($id) {
        if (!Jojo::getFormData('fm_pageid', 0)) {
            // no pageid set for this category (either it's a new category or maybe the original page was deleted)
            self::sync_category_to_page($id);
       }
    }

    // Sync the category data over from the page table
    static function admin_action_after_save_page($id) {
        if (strtolower(Jojo::getFormData('fm_pg_link',    ''))=='jojo_plugin_jojo_article') {
           self::sync_page_to_category($id);
       }
    }

    static function sync_category_to_page($catid) {
        // add a new hidden page for this category and make up a title
            $newpageid = Jojo::insertQuery(
            "INSERT INTO {page} SET pg_title = ?, pg_link = ?, pg_url = ?, pg_parent = ?, pg_status = ?",
            array(
                'Orphaned Articles',  // Title
                'jojo_plugin_jojo_article',  // Link
                'orphaned-articles',  // URL
                0,  // Parent - don't do anything smart, just put it at the top level for now
                'hidden' // hide new page so it doesn't show up on the live site until it's been given a proper title and url
            )
        );
        // If we successfully added the page, update the category with the new pageid
        if ($newpageid) {
            jojo::updateQuery(
                "UPDATE {articlecategory} SET pageid = ? WHERE articlecategoryid = ?",
                array(
                    $newpageid,
                    $catid
                )
            );
       }
       return true;
    }

    static function sync_page_to_category($pageid) {
        // Get the list of categories by page id
        $categories = jojo::selectAssoc("SELECT pageid AS id, pageid FROM {articlecategory}");
        // no category for this page id
        if (!count($categories) || !isset($categories[$pageid])) {
            jojo::insertQuery("INSERT INTO {articlecategory} (pageid) VALUES ('$pageid')");
        }
        return true;
    }

   /**
     * RSS Icon filter
     * Places the RSS feed icon in the head of the document, sitewide
     */
    static function rssicon($data)
    {
        global $page;

        /* add RSS feeds for each page */
        $categories =  self::getPluginPages('', $page->page['root']);
        foreach ($categories as $c) {
            $prefix =  self::_getPrefix('article', $c['articlecategoryid']) . '/rss/';
            if ($prefix && (isset($c['externalrsslink']) && $c['externalrsslink']) && (!isset($c['rsslink']) || $c['rsslink'] == 1)) {
              $data[$c['pg_title']] = $c['externalrsslink'];
            } elseif($prefix && (!isset($c['rsslink']) || $c['rsslink']==1)) {
                $data[$c['pg_title']] = _SITEURL . '/' .  Jojo::getPageUrlPrefix($c['pageid']) . $prefix;

            }
        }
        return $data;
    }

    /**
     * Site Search
     */
    static function search($results, $keywords, $language, $booleankeyword_str=false)
    {
        $searchfields = array(
            'plugin' => 'jojo_article',
            'table' => 'article',
            'idfield' => 'articleid',
            'languagefield' => 'ar_htmllang',
            'primaryfields' => 'ar_title',
            'secondaryfields' => 'ar_title, ar_desc, ar_body',
        );
        $rawresults =  Jojo_Plugin_Jojo_search::searchPlugin($searchfields, $keywords, $language, $booleankeyword_str);
        $data = $rawresults ? self::getItemsById(array_keys($rawresults)) : '';
        if ($data) {
            foreach ($data as $result) {
                $result['relevance'] = $rawresults[$result['id']]['relevance'];
                $result['type'] = $result['pagetitle'];
                $result['tags'] = isset($rawresults[$result['id']]['tags']) ? $rawresults[$result['id']]['tags'] : '';
                $results[] = $result;
            }
        }
        /* Return results */
        return $results;
    }

    /**
     * Newsletter content
     */
    static function newslettercontent($contentarray, $newletterid=false)
    {
        /* Get all the articles for this newsletter */
        if ($newletterid) {
            $articleids = Jojo::selectQuery('SELECT a.articleid FROM {article} a, {newsletter_article} n WHERE a.articleid = n.articleid AND n.newsletterid = ? ORDER BY n.order, a.ar_date DESC', $newletterid);
            if ($articleids) {
                foreach ($articleids as $i) {
                    $ids[] = $i['articleid'];
                }
                $articles = self::getItemsById($ids, '', 'showhidden');
                $css = Jojo::getOption('newslettercss', '');
                $newscss = array();
                if ($css) {
                    $styles = explode("\n", $css);
                    foreach ($styles as $k => $s) {
                        $style = explode('=', $s);
                        $newscss[$k]['tag'] = $style[0];
                        $newscss[$k]['style'] = $style[1];
                    }
                }
                $contentarray['articles'] = array();
                foreach($articles as &$a) {
                    $a['title'] = mb_convert_encoding($a['ar_title'], 'HTML-ENTITIES', 'UTF-8');
                    $a['bodyplain'] = mb_convert_encoding($a['bodyplain'], 'HTML-ENTITIES', 'UTF-8');
                    $a['body'] = Jojo::relative2absolute($a['ar_body'], _SITEURL);
                    $a['body'] = mb_convert_encoding(Jojo::inlineStyle($a['body'], $newscss, $list2table=true), 'HTML-ENTITIES', 'UTF-8');
                    $a['imageurl'] = rawurlencode($a['image']);
                    foreach ($ids as $k => $i) {
                        if ($i==$a['articleid']) {
                            $contentarray['articles'][$k] = $a;
                        }
                    }
                }
                ksort($contentarray['articles']);
            }
        }
        /* Return results */
        return $contentarray;
    }

/*
* Tags
*/
    static function getTagSnippets($ids)
    {
        $snippets = self::getItemsById($ids);
        return $snippets;
    }
}
