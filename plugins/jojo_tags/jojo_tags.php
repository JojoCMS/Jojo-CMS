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
 * @package jojo_tags
 */

class Jojo_Plugin_Jojo_Tags extends Jojo_Plugin
{
    /**
     * Delete all the tags associated with a particular item
     * $plugin       string The name of the plugin that knows how to handle
     *                      this tag. Eg jojo_article
     * $itemid       string Any plugin sepecific id that the plugin needs to
     *                      retreive the tagged object.
     */
    static function deleteTags($plugin, $itemid, $tag=false)
    {
        $query = "DELETE FROM {tag_item} WHERE itemid = $itemid AND plugin = '$plugin'";

        if ($tag) {
            $tagid = Jojo_Plugin_Jojo_Tags::_getTagId($tag, true);
            $query .= " AND tagid = $tagid";
        }
        /* Delete the tags */
        Jojo::deleteQuery($query);
    }

    /**
     * saveTag is used by other plugins to save references to tagged content.
     *
     * $tag          string The tag string
     * $plugin       string The name of the plugin that knows how to handle
     *                      this tag. Eg jojo_article
     * $itemid       string Any plugin sepecific id that the plugin needs to
     *                      retreive the tagged object.
     */
    static function saveTag($tag, $plugin, $itemid)
    {
        $tagid = Jojo_Plugin_Jojo_Tags::_getTagId($tag, true);

        /* Check doesn't exist already */
        $res = Jojo::selectQuery("SELECT * FROM {tag_item} WHERE tagid = ? AND itemid = ? AND plugin = ?", array($tagid, $itemid, $plugin));
        if (!isset($res[0])) {
            /* Not found so insert */
            Jojo::insertQuery("INSERT INTO {tag_item} SET tagid = ?, itemid = ?, plugin = ?", array($tagid, $itemid, $plugin));
        }
    }

    /**
     * getTags is used by other plugins to retrieve tags saved against their content.
     *
     * $plugin       string The name of the plugin that knows how to handle
     *                      this tag. Eg jojo_article
     * $itemid       string Any plugin sepecific id that the plugin needs to
     *                      retreive the tagged object.
     */
    static function getTags($plugin, $itemid)
    {

        /* get array of tags for the content in question */
        $tags = Jojo::selectQuery("SELECT t.tg_tag FROM {tag} t, {tag_item} ti WHERE t.tagid=ti.tagid AND ti.itemid = ? AND ti.plugin = ?", array($itemid, $plugin));

        if (!count($tags)) {
            /* Not found so insert */
            return false;
        } else {
            foreach ($tags as &$tag) {
               $tag['tag'] = $tag['tg_tag'];
               $tag['cleanword'] = htmlspecialchars($tag['tg_tag'], ENT_COMPAT, 'UTF-8', false);
               $tag['url'] = Jojo::getOption('tag_stricturl', 'yes') == 'yes' ? Jojo::cleanURL($tag['tg_tag']) : urlencode($tag['tg_tag']);
            }
        }
        return $tags;
    }

    /**
     * Retrieve the ID of a tag.
     *
     * $tag         string  The tag string
     * $create      boolean (optional) Create the tag if it doesn't exist?
     */
    static function _getTagId($tag, $create = false)
    {
        static $_cache = array();

        /* Return a cached id if we have it */
        if (isset($_cache[$tag])) {
            return $_cache[$tag];
        }

        /* Query the database for the tag */
        $res = Jojo::selectRow("SELECT tagid FROM {tag} WHERE tg_tag = ?", $tag);
        if (count($res)) {
            /* Found, cache and return */
            $_cache[$tag] = $res['tagid'];
            return $_cache[$tag];
        } elseif ($create) {
            /* Not found, create, cache and return */
            $_cache[$tag] = Jojo::insertQuery("INSERT INTO {tag} SET tg_tag = ?", $tag);
            return $_cache[$tag];
        }

        return false;
    }



    /**
     * Retrieve an array of related items. Best matches are returned first (best means the most tags in common).
     *
     * $plugin            string  The plugin name to search on eg jojo_article, jojo_page, product etc.
     * $itemid            integer  The ID of the object to search on
     * $numresults        integer  The number of related items to return
     * $plugintypes       array    Limits results to certain types of content only eg array('jojo_article','product')  - blank means return any type
     */
    static function getRelated($plugin, $itemid, $numresults=5, $plugintypes='')
    {
        $values = array();

        /* build SQL for limiting results */
        if (!is_array($plugintypes) && !empty($plugintypes)) $plugintypes = array($plugintypes); //convert string to array if required
        if (empty($plugintypes)) {
            //display results from any plugin
            $plugintypesql = '';
        } else {
            //display results from specific plugins only
            $plugintypesql = " AND (0 ";
            foreach ($plugintypes as $type) {
                $plugintypesql .= " OR `plugin` = ?";
                $values[] = $type;
            }
            $plugintypesql .= ")";
        }

        /* get array of tags for the content in question */
        $tags = Jojo_Plugin_Jojo_Tags::getTags($plugin, $itemid);

        /* Get tag results */
        $sqltags = array();
        if(!empty($tags)){
            foreach ($tags as $k => $v) {
                $sqltags[$k] = '"' . addslashes($v['tg_tag']) . '"';
            }
        }
        $results = array();
        if (count($sqltags)) {

            $query = sprintf("SELECT
                    plugin, itemid, COUNT(DISTINCT ti.tagid) AS nummatches
                  FROM
                    {tag_item} ti
                    INNER JOIN {tag} t ON ti.tagid = t.tagid
                  WHERE t.tg_tag IN (%s)
                    AND NOT(plugin = '%s' AND itemid = %s)
                    %s
                  GROUP BY CONCAT( plugin, itemid )
                  HAVING COUNT(DISTINCT ti.tagid) > 0
                  ORDER BY nummatches DESC
                  LIMIT %d", implode($sqltags, ','), $plugin, $itemid, $plugintypesql, $numresults); //%d , count($sqltags)

            $res = Jojo::selectQuery($query, $values);

            $ids = array();
            $matches = array();
            $i = 0;
            foreach ($res as $t) {
                $ids[$t['plugin']][$t['itemid']] = true;
                //$counter[$t['plugin']][$t['itemid']] = $t['nummatches']; //debug - uncomment to see the closeness of matches
                $sort[$t['plugin']][$t['itemid']] = $i++; //so that we can sort results best to worst
            }

            foreach ($ids as $plugin => $pluginids) {
                $classname = 'jojo_plugin_' . $plugin;
                if (!class_exists($classname)) {
                    $plugins = Jojo::listPlugins($plugin . '.php');
                    if (count($plugins) > 0) {
                      include($plugins[0]);
                    }
                }

                if (class_exists($classname)) {
                    $results = array_merge($results, call_user_func(array($classname, 'getTagSnippets'), array_keys($pluginids)));
                }
            }

        }

        /* sort result so best matches are first */
        $sortedresults = array();
        foreach ($results as $i=>$result) {
            //$results[$i]['title'] = $results[$i]['title'] . ' - '.$counter['jojo_article'][$results[$i]['id']];  //debug - uncomment to see the closeness of matches
            $sortedresults[$sort['jojo_article'][$result['id']]] = $result;
        }
        sort($sortedresults);
        return $sortedresults;
    }

    function _getContent()
    {
        global $smarty, $ajaxid;
        $content = array();

        /* Get tag results */
        $tags = explode('/', Jojo::getFormData('tags', false));

        $sqltags = array();
        if ($tags) {
            foreach ($tags as $k => $v) {
                    if (!$v) {
                        unset($tags[$k]);
                        continue;
                    }
                    $tags[$k] = Jojo::getOption('tag_stricturl', 'yes') == 'yes' ? str_replace('-', ' ', $v) : urldecode($v);
                    $sqltags[$k] = '"' . str_replace('"', '\"', $tags[$k]) . '"';
            }
            $smarty->assign('selectedtags', $tags);
        }

        if (count($sqltags)) {

            $query = sprintf("SELECT
                    plugin, itemid
                  FROM
                    {tag_item} ti
                    INNER JOIN {tag} t ON ti.tagid = t.tagid
                  WHERE t.tg_tag IN (%s)
                  GROUP BY CONCAT( plugin, itemid )
                  HAVING COUNT(DISTINCT ti.tagid) = %d", implode($sqltags, ','), count($sqltags));
            $res = Jojo::selectQuery($query);

            $ids = array();
            foreach ($res as $t) {
                $ids[$t['plugin']][$t['itemid']] = true;
            }
            
            /* If the item can't be found and it's not the index page, return a 404 */
            if (!empty($tags) && !$ids) {
                include(_BASEPLUGINDIR . '/jojo_core/404.php');
                exit;
            }

            $results = array();
            foreach ($ids as $plugin => $pluginids) {
                $classname = 'jojo_plugin_' . $plugin;
                if (!class_exists($classname)) {
                    $plugins = Jojo::listPlugins($plugin . '.php');
                    if (count($plugins) > 0) {
                      include($plugins[0]);
                    }
                }

                if (class_exists($classname)) {
                    $pluginresults = call_user_func(array($classname, 'getTagSnippets'), array_keys($pluginids));
                    $results = $pluginresults ? ( $results ? array_merge($results, $pluginresults) : $pluginresults ) : $results;
                }
            }
            if (!$results) {
                include(_BASEPLUGINDIR . '/jojo_core/404.php');
                exit;
            }
            foreach ($results as $k => $v) {
                $results[$k]['displayurl'] = urldecode($v['url']);
                $results[$k]['text'] = isset($v['bodyplain']) ? $v['bodyplain'] : $results[$k]['text'];
            }
            $numresults = count($results);
            $smarty->assign('results', $results);
            $smarty->assign('numresults', $numresults);
            $smarty->assign('tags', implode($sqltags, ' '));

            /* Add tag cloud breadcrumb */
            $breadcrumbs = $this->_getBreadCrumbs();

            if (count($tags) > 1) $smarty->assign('pg_index', 'no'); //do not index 2nd level tags pages
            $url = 'tags/';
            foreach ($tags as $tag) {
                /* Add tag results */
                $url = $url . $tag . '/';
                $breadcrumb = array();
                $breadcrumb['name'] = $tag;
                $breadcrumb['rollover'] = $tag;
                $breadcrumb['url'] = $url;
                $breadcrumbs[] = $breadcrumb;
            }


            $content['breadcrumbs'] = $breadcrumbs;


            if (count($tags) == 1 && $tags[0]) {
                $tag = $tags[0];
                $query = "SELECT * FROM {tag} t WHERE t.tg_tag = ?";
                $tagdata = Jojo::selectQuery($query, $tag);
                $tagdata = isset($tagdata[0]) ? $tagdata[0] : array('tg_tag' => $tag);
                $casedtag = htmlspecialchars($tagdata['tg_tag'], ENT_COMPAT, 'UTF-8', false);

                if (!defined('_LINKBODY')) define('_LINKBODY',''); //to avoid literal text appearing

                /* Don't want these pages to be supplemental */
                $meta = array();
                $meta[] = "Content tagged as $casedtag" . _LINKBODY;
                $meta[] = "Content tagged as $casedtag on  ". _SITETITLE;
                $meta[] = "$casedtag on " . _SITETITLE . " - browse $numresults content items tagged as $casedtag. " . _LINKBODY;
                $meta[] = "$casedtag on " . _SITETITLE . " - Browse our selection of content tagged $casedtag. " . _LINKBODY;
                $meta[] = "Recent content on " . _SITETITLE . " tagged as $casedtag. ";
                $meta[] = "The contents of this page relate to $casedtag. " . _LINKBODY;
                $meta[] = "$casedtag - found $numresults pages relating to $casedtag on " . str_replace('http://','',_SITEURL) . " " . _LINKBODY;

                $content['meta_description'] =  empty($tagdata['tg_metadesc']) ? $meta[Jojo::semiRand(0, (count($meta)-1))] : $tagdata['tg_metadesc'] ; //pick a semi-random meta description

                /* Don't want these pages to be supplemental */
                $body = array();
                $body[] = "<strong>$casedtag</strong> Content tagged as <em>$casedtag</em>. ";
                $body[] = "Recent content on " . _SITETITLE . " tagged as <strong>$casedtag</strong>. ";
                $body[] = "Content tagged as <strong>$casedtag</strong>.";
                $body[] = "<strong>$casedtag</strong> on " . _SITETITLE . " - browse $numresults items of content tagged as '<strong>$casedtag</strong>'. ";
                $body[] = "<strong>$casedtag</strong> on " . _SITETITLE . " - Browse our selection of content tagged '$casedtag'. ";
                $body[] = "The contents of this page relate to $casedtag.";

                //pick a semi-random body content if none provided
                $smarty->assign('openingparagraph', empty($tagdata['tg_body']) ? $body[Jojo::semiRand(0, (count($body)-1))] : $tagdata['tg_body']);
                $smarty->assign('tag', $casedtag);

                $content['seotitle'] =  empty($tagdata['tg_seotitle']) ? $casedtag : $tagdata['tg_seotitle'];

                $content['title'] = $casedtag;
                $content['metadescription'] = $content['meta_description'];
            } elseif (count($tags) > 1) {
                $content['seotitle'] =  implode($tags, ' / ');
                $content['title'] = implode($tags, '/');
            } else {
                $smarty->assign('tags', false);
            }

        }
        $smarty->assign('article_tag_cloud_related', Jojo::getOption('article_tag_cloud_related'));
        $content['content'] = $smarty->fetch('tags.tpl');

        return $content;
    }

    static function getTagArray($tags='')
    {
        //global $smarty;
        if (!empty($tags)) {
            $tags = Jojo_Plugin_Jojo_Tags::tagstrToArray($tags);

            $sqltags = array();
            foreach ($tags as $k => $v) {
                $sqltags[$k] = '"' . str_replace('"', '\"', $tags[$k]) . '"';
            }

            /* Create a related tags tag cloud */
            $innerQuery = sprintf("SELECT
                    CONCAT(plugin, itemid)
                  FROM
                    {tag_item} ti
                    INNER JOIN {tag} t ON ti.tagid = t.tagid
                  WHERE t.tg_tag IN (%s)
                  GROUP BY CONCAT( plugin, itemid )
                  HAVING COUNT(DISTINCT ti.tagid) = %d
                  ", implode($sqltags, ','), count($sqltags));
            $query = sprintf("SELECT tg_tag, COUNT(*) as frequency
                       FROM {tag_item} ti
                       INNER JOIN {tag} t ON ti.tagid = t.tagid
                       WHERE CONCAT(ti.plugin, ti.itemid) IN (%s)
                       AND t.tg_tag NOT IN (%s)
                       GROUP BY tg_tag;", $innerQuery, implode($sqltags, ','));
            $res = Jojo::selectQuery($query);
            //$smarty->assign('related', implode($tags, '/'));
            //$smarty->assign('tags', $tags);
        } else {
            /* Create overall tag cloud */
            $query = "SELECT tg_tag, COUNT(*) as frequency
                       FROM {tag_item} ti
                       INNER JOIN {tag} t ON ti.tagid = t.tagid
                       GROUP BY tg_tag;";
            $res = Jojo::selectQuery($query);
        }

        return $res;
    }

    static function getTagCloud($tags='', $restrict=false)
    {
        global $smarty;

        if (!$restrict) $restrict = array();
        if (!empty($tags)) {
            $tags = Jojo_Plugin_Jojo_Tags::tagstrToArray($tags);

            $sqltags = array();
            foreach ($tags as $k => $v) {
                $sqltags[$k] = '"' . str_replace('"', '\"', $tags[$k]) . '"';
            }

            /* Create a related tags tag cloud */
            if ( Jojo::getOption('article_tag_cloud_related') != 'no' ){
                $innerQuery = sprintf("SELECT
                        CONCAT(plugin, itemid)
                      FROM
                        {tag_item} ti
                        INNER JOIN {tag} t ON ti.tagid = t.tagid
                      WHERE t.tg_tag IN (%s)
                      GROUP BY CONCAT( plugin, itemid )
                      HAVING COUNT(DISTINCT ti.tagid) = %d
                      ", implode($sqltags, ','), count($sqltags));
                $query = sprintf("SELECT tg_tag, COUNT(*) as frequency
                           FROM {tag_item} ti
                           INNER JOIN {tag} t ON ti.tagid = t.tagid
                           WHERE CONCAT(ti.plugin, ti.itemid) IN (%s)
                           AND t.tg_tag NOT IN (%s)
                           GROUP BY tg_tag;", $innerQuery, implode($sqltags, ','));
                $res = Jojo::selectQuery($query);
                $smarty->assign('related', implode($tags, '/'));
                $smarty->assign('tags', $tags);
            } else {
                $res = "";
            }
        } else {
            /* Create overall tag cloud */
            $query = "SELECT tg_tag, COUNT(*) as frequency,
                CASE WHEN SUBSTRING_INDEX(tg_tag, ' ', 1)
                        IN ('a', 'an', 'the')
                    THEN CONCAT(
                        SUBSTRING(tg_tag, INSTR(tg_tag, ' ') + 1),
                        ', ',
                        SUBSTRING_INDEX(tg_tag, ' ', 1)
                    )
                    ELSE tg_tag
                END AS tg_tagsort
                       FROM {tag_item} ti
                       INNER JOIN {tag} t ON ti.tagid = t.tagid
                       GROUP BY tg_tag
                       ORDER BY tg_tagsort;";
            $res = Jojo::selectQuery($query);
        }

        $cloudwords = array();
        foreach ($res as $r) {
            $cloudwords[$r['tg_tag']] = $r['frequency'];
        }

        if (count($cloudwords) > 1) {
            $max = max($cloudwords);
        } elseif (count($cloudwords) == 1) {
            $max = $r['frequency'];
        } else {
            $max = 0;
        }
        foreach ($cloudwords as $word => $count) {
          $fontsize = round($count / $max * 2,1)+ 0.8;
          $cloudwords[$word] = array(
                                'fontsize' => $fontsize,
                               'cleanword' => htmlspecialchars($word, ENT_COMPAT, 'UTF-8', false),
                                'url' => Jojo::getOption('tag_stricturl', 'yes') == 'yes' ? Jojo::cleanURL($word) : urlencode($word)
                               );
        }

        /* remove unwanted tags if we are after a restricted set */
        if (count($restrict)) {
            foreach ($cloudwords as $word => $val) {
                if (!Jojo_Plugin_Jojo_Tags::in_arrayi($word, $restrict)) unset($cloudwords[$word]);
            }
        }

        $smarty->assign('cloudwords', $cloudwords);

        /* Get the tagcloud html */
        $prefixdata = Jojo::selectRow("SELECT `pg_url` FROM {page} WHERE `pg_link` = 'Jojo_Plugin_Jojo_Tags';");
        $prefix = $prefixdata['pg_url'];
        if (_MULTILANGUAGE) {
            global $page;
            $mldata = Jojo::getMultiLanguageData();
            $prefix = Jojo::getMultiLanguageString ( $page->page['pg_language']) . $prefix;
        }
        $smarty->assign('prefix', $prefix);
        $html = $smarty->fetch('tagcloud.tpl');
        return $html;
    }

    /* case insensitive in_array function */
    static function in_arrayi($search, &$array) {
      $search = strtolower($search);
      foreach ($array as $item)
        if (strtolower($item['tg_tag']) == $search)
          return TRUE;
      return FALSE;
    }

    static function tagcloudfilter($content)
    {
        global $smarty;

        /* Find all [[tagcloud]] tags */
        preg_match_all('/\[\[tagcloud(:([^\]]*))?\]\]/', $content, $matches);
        foreach($matches[0] as $id => $match) {
            $tags = !empty($matches[2][$id]) ? $matches[2][$id] : '';
            $html = Jojo_Plugin_Jojo_Tags::getTagCloud($tags);
            $content = str_replace($matches[0][$id], $html, $content);
        }
        return $content;
    }

    /* a tag string looks like this: internet "web design" blogging photography "new york" */
    static function tagArrayToStr($tagarray)
    {
        /* trim whitespace */
        $n = count($tagarray);
        for ($i=0;$i<$n;$i++) {
            $tagarray[$i] = trim($tagarray[$i]);
            if (strpos($tagarray[$i], ' ') !== false) $tagarray[$i] = '"'.$tagarray[$i].'"'; //add quotes if required
        }
        return implode(' ', $tagarray);
    }

    static function tagStrToArray($tagstr)
    {
        /* Create an array of the tags */
        $tags = array();
        $tagstring = str_replace(array("\n", "\r"), ' ', strtolower($tagstr));

        /* Get quoted tags first */
        preg_match_all('#\"(.*)\"#U', $tagstring, $ms);
        foreach($ms[0] as $k => $v) {
            if (trim($ms[1][$k])) {
                $tags[trim($ms[1][$k])] = true;
            }
        }

        /* Remove located quoted tags from string */
        $tagstring = str_replace(array_values($ms[0]), '', $tagstring);

        /* Get remaining single word tags */
        $parts = explode(' ', $tagstring);
        foreach($parts as $p) {
            if (trim($p)) {
                $tags[trim($p)] = true;
            }
        }
        return array_keys($tags);
    }

    /**
     * XML Sitemap filter
     *
     * Receives existing sitemap and adds tags pages
     */
    static function xmlsitemap($sitemap)
    {
        /* Get tags from database */
        $tags = Jojo::selectQuery("SELECT * FROM {tag}");

        /* Add tags to sitemap */
        foreach($tags as $t) {
            $url = _SITEURL . '/tags/' . (Jojo::getOption('tag_stricturl', 'yes') == 'yes' ? Jojo::cleanURL($t['tg_tag']) : urlencode($t['tg_tag']) ) . '/';
            $lastmod = '';
            $priority = 0.5;
            $changefreq = '';
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }

        /* Return sitemap */
        return $sitemap;
    }

    function getCorrectUrl()
    {
        $uri = trim(Jojo::getFormData('tags'), '/');
        if (strpos($uri, '/external/') ||
            strpos($uri, 'css/styles.css') ||
            strpos($uri, 'js/common.js')) {
            include(_BASEPLUGINDIR . '/jojo_core/404.php');
            exit;
        }

        $tag = explode('/', trim(Jojo::getFormData('tags'), '/'));
        if (count($tag) > 1 || $tag[0]) {
            $expectedurl  = _SITEURL . '/';
            if (_MULTILANGUAGE) {
                $mldata = Jojo::getMultiLanguageData();
                $languagePrefix = Jojo::getMultiLanguageString ( $this->page['pg_language']);
                $expectedurl .= $languagePrefix != '' ? $languagePrefix : '';
            }
            $expectedurl .= $this->page['pg_url'] . '/' . (Jojo::getOption('tag_stricturl', 'yes') == 'yes' ? Jojo::cleanURL(implode($tag, '/')) : implode($tag, '/') )  . '/';
            return $expectedurl;
        }

        return parent::getCorrectUrl();
    }

}