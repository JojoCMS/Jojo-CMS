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

class Jojo_Plugin_Core extends Jojo_Plugin
{
    static function saveTags($record, $tags = array())
    {
        /* Ensure the tags class is available */
        if (!class_exists('Jojo_Plugin_Jojo_Tags')) {
            return false;
        }

        /* Delete existing tags for this item */
        Jojo_Plugin_Jojo_Tags::deleteTags('Core', $record['pageid']);

        /* Save all the new tages */
        foreach($tags as $tag) {
            Jojo_Plugin_Jojo_Tags::saveTag($tag, 'Core', $record['pageid']);
        }
    }

    static function getTagSnippets($ids)
    {
        /* Convert array of ids to a string */
        $ids = "'" . implode($ids, "', '") . "'";

        /* Get the pages */
        // build query to handle new language country functionality.
        $query = 'SELECT * FROM {page} as page';
        if ( _MULTILANGUAGE) {
            if ( Jojo::tableexists ( 'lang_country' )) {
                $query .= " LEFT JOIN {lang_country} as lang_country ON (page.pg_language = lc_code)
                                LEFT JOIN {language} as language ON (lang_country.lc_defaultlang = languageid) ";
            } else {
                $query .= " LEFT JOIN {language} as language ON (page.pg_language = languageid)";
            }
        }
        $query .= " WHERE pageid IN ($ids) AND pg_livedate < ? AND (pg_expirydate <= 0 OR pg_expirydate > ?)";
        $query .= _MULTILANGUAGE ? " AND language.active = 'yes'" : '';
        $query .= " ORDER BY pg_title DESC";
        $pages = Jojo::selectQuery($query,  array(time(), time()));

        /* Create the snippets */
        $snippets = array();
        foreach ($pages as $i => $p){
            if (_MULTILANGUAGE) {
                $mldata = Jojo::getMultiLanguageData();
                $language = !empty($p['pg_language']) ? $p['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $lclanguage = $mldata['longcodes'][$language];
            }
            $url = (_MULTILANGUAGE) ? Jojo::getMultiLanguageString ($language, false) : '';
            /* Calculate URL */
            if ($p['pg_url']) {
                // Discovery level
                if ((isset($mldata['homes']) && !in_array($p['pageid'], $mldata['homes'])) || !isset($mldata['homes'])) {
                    // This is not a language homepage so use the url.
                    // A language homepage should only have url/languagecode/ to be SEO friendly.
                   $url .= $p['pg_url'] . '/';
                }
            } else {
                // Rewritten
                if ((isset($mldata['homes']) && !in_array($p['pageid'], $mldata['homes'])) || !isset($mldata['homes'])) {
                    // This is not a language homepage so build the url.
                    // A language homepage should only have url/languagecode/ to be SEO friendly.
                    $url .=  (_MULTILANGUAGE && $language != 'en') ? '/' . $p['pageid'] . '/' . urlencode($p['pg_title']) : Jojo::rewrite('page', $p['pageid'], $p['pg_title']);
                }
            }

            $snippets[] = array(
                    'title' => htmlspecialchars($p['pg_title'], ENT_COMPAT, 'UTF-8', false),
                    'text'  => strip_tags($p['pg_body']),
                    'url'   => $url,
                    'absoluteurl' =>  ($p['pg_ssl'] == 'yes') ? _SECUREURL . '/' . $url : _SITEURL . '/' . $url,
                );
             if (substr(strtolower($p['pg_link']), 0, 7) == 'http://') {
                // External
                $snippets['url'] = $p['pg_link'];
                $snippets['absoluteurl'] = $p['pg_link'];
             }
         }

        /* Return the snippets */
        return $snippets;
    }

    static function applyContentVars($data)
    {
        global $smarty;
        /* replace [[myvar]] with the appropriate value from options */
        $vars = Jojo::selectQuery("SELECT * FROM {option} WHERE op_category = 'Variable' OR  op_category = 'variable'");
        foreach ($vars as $var) {
            $data = str_replace('[[' . $var['op_name'] . ']]', $var['op_value'], $data);
        }

        /* replace [[my-template.tpl]] with the output of the template */
        preg_match_all('/\\[\\[([0-9a-z-_\\/]+\\.tpl)\\]\\]/i', $data, $matches);
        foreach($matches[1] as $id => $v) {
            $html = $smarty->fetch($matches[1][$id]);
            $data = str_replace($matches[0][$id], $html, $data);
        }

        return $data;
    }

    /* Any Square brackets that have been escaped as \[\[ or \]\] will be converted back to [[ or ]] just before outputting */
    static function unescapeSquareBrackets($data)
    {
        $data = str_replace(array('\\[', '\\]'), array('[', ']'), $data);
        return $data;
    }

    static function fixAnchorLinks($data)
    {
        $data = preg_replace('/<a([^>]*?)href=["\'](#[a-z0-9-_]*)?["\']([^>]*?)>/i', '<a$1href="' . $_SERVER['REQUEST_URI'] . '$2"$3>', $data);
        return $data;
    }

    /*
     * Applies rel=nofollow to any links on the page pointing to a domain in the nofollow_list option
     * Use this feature to specifically nofollow all links to certain sites
     */
    static function nofollowList($data)
    {
        $blacklist = Jojo::getOption('nofollow_list');
        if (empty($blacklist)) return $data;

        $blacklist = explode("\n", $blacklist);
        foreach ($blacklist as $dirtydomain) {
            $domain = str_replace('.', '\\.', trim($dirtydomain));
            $data = preg_replace('%<a([^>]*?)href=["\\\'](' . $domain . ').*?["\\\']([^>]*?)>%', '<a$1href="$2"$3 rel="nofollow">', $data);
        }
        return $data;
    }

    /**
     * Sitemap filter
     *
     * Receives existing sitemap and adds pages section
     */
    static function sitemap($sitemap)
    {
        global $_USERGROUPS;


        $perms = new Jojo_Permissions();
        $pagetree = new hktree();

        // build query to handle new language/country functionality.
        $query = 'SELECT * FROM {page} as page';
        if ( _MULTILANGUAGE) {
            if ( Jojo::tableexists ( 'lang_country' )) {
                $query .= " LEFT JOIN {lang_country} as lang_country ON (page.pg_language = lc_code)
                                LEFT JOIN {language} as language ON (lang_country.lc_defaultlang = languageid) ";
            } else {
                $query .= " LEFT JOIN {language} as language ON (page.pg_language = languageid)";
            }
        }
        $query .= " WHERE  pg_sitemapnav = 'yes' AND pg_livedate < ? AND  (pg_expirydate = 0 OR pg_expirydate > ?)";
        $query .= _MULTILANGUAGE ? " AND language.active = 'yes'" : '';
        $query .= " ORDER BY pg_order";
        $sitemappages = Jojo::selectQuery($query, array(strtotime('now'), strtotime('now')));
        foreach ($sitemappages as $sp) {
            if (_MULTILANGUAGE) {
                $language = !empty($sp['pg_language']) ? $sp['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $mldata = Jojo::getMultiLanguageData();
                $lclanguage = $mldata['longcodes'][$language];
                $home = $mldata['homes'][$language];
                $root = $mldata['roots'][$language];
            }
            $link  =  Jojo::urlPrefix( Jojo::yes2true($sp['pg_ssl']));
            if (_MULTILANGUAGE && ($sp['pageid'] == $home || $sp['pageid'] == $root)) { //language homepage or root
                $link = _SITEURL . '/' . Jojo::getMultiLanguageString($language,false);
            } elseif ($sp['pageid'] == 1 ) { //homepage
                $link = _SITEURL;
            } elseif (substr(strtolower($sp['pg_link']), 0, 7) == 'http://') { //external
                $link .= $sp['pg_link'];
            } elseif ($sp['pg_url']) { //discovery level
                $link .= (_MULTILANGUAGE) ? Jojo::getMultiLanguageString($language,false) : '';
                $link .= $sp['pg_url'] . '/';
            } else { //rewritten
                $link .= (_MULTILANGUAGE) ? Jojo::getMultiLanguageString($language,false) : '';
                $link .=  Jojo::rewrite('page', $sp['pageid'], $sp['pg_title']);
            }
            $perms->getPermissions('page', $sp['pageid']);
            if ($perms->hasPerm($_USERGROUPS, 'show')) {
                $sp['title'] = htmlspecialchars($sp['pg_title'],ENT_COMPAT,'UTF-8',false);
                $pagetree->addNode($sp['pageid'], $sp['pg_parent'], $sp['title'], $link);
            }

        }

        /* Add to the sitemap array */
        $sitemap['pages'] = array(
                    'title' => 'Pages',
                    'tree'  => $pagetree->asArray(),
                    'order' => 0,
                    'header' => '',
                    'footer' => ''
                    );

        return $sitemap;
    }

    /**
     * XML Sitemap filter
     *
     * Receives existing sitemap and adds pages section
     */
    static function xmlsitemap($sitemap)
    {
        /* Get pages from database */
        $perms = new Jojo_Permissions();

        // build query to handle new language country functionality.
        $query = 'SELECT * FROM {page} as page';
        if ( _MULTILANGUAGE) {
            if ( Jojo::tableexists ( 'lang_country' )) {
                $query .= " LEFT JOIN {lang_country} as lang_country ON (page.pg_language = lc_code)
                                LEFT JOIN {language} as language ON (lang_country.lc_defaultlang = languageid) ";
            } else {
                $query .= " LEFT JOIN {language} as language ON (page.pg_language = languageid)";
            }
        }
        $query .= " WHERE pg_index = 'yes' AND pg_xmlsitemapnav = 'yes' AND pg_livedate < ? AND (pg_expirydate = 0 OR pg_expirydate > ?)";
        $query .= _MULTILANGUAGE ? " AND language.active = 'yes'" : '';
        $query .= " ORDER BY pg_order";
        $sitemappages = Jojo::selectQuery($query, array(strtotime('now'), strtotime('now')));
        /* Add pages to the sitemap */
        foreach ($sitemappages as $p) {
            // Get multilanguage data for this page
            if (_MULTILANGUAGE) {
                $language = !empty($p['pg_language']) ? $p['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $mldata = Jojo::getMultiLanguageData();
                $lclanguage = $mldata['longcodes'][$language];
                $home = $mldata['homes'][$language];
                $root = $mldata['roots'][$language];
            }
            /* Check permissions to ensure page is public */
            $perms->getPermissions('page', $p['pageid']);
            if (!$perms->hasPerm(array('everyone'), 'show')) {
                continue;
            }

            /* Calculate URL */
            $url  =  _SITEURL . '/' . Jojo::urlPrefix( Jojo::yes2true($p['pg_ssl']));
            if (_MULTILANGUAGE && ($p['pageid'] == $home || $p['pageid'] == $root)) { //language homepage or root
                $url = _SITEURL . '/' . Jojo::getMultiLanguageString($p['pg_language']);
            } elseif ($p['pageid'] == 1 ) { //homepage
                $url = _SITEURL;
            } elseif (substr(strtolower($p['pg_link']), 0, 7) == 'http://') { //external
                $url .= $p['pg_link'];
            } elseif (_MULTILANGUAGE && ($p['pageid'] == $home || $p['pageid'] == $root)) { //Is this a language homepage or root page?
                $url .= Jojo::getMultiLanguageString ( $p['pg_language'], true );
            } elseif ($p['pg_url']) {
                // Discovery level
                if (_MULTILANGUAGE) {
                    $url .= Jojo::getMultiLanguageString ( $p['pg_language'], true );
                    if ( !in_array ($p['pageid'], $mldata['homes'])) {
                        // not a homepage so show the full url for the page.
                        $url .= $p['pg_url'] . '/';
                    }
                } else {
                    $url .= $p['pg_url'] . '/';
                }
            } else {
                // Rewritten
                $url .= (_MULTILANGUAGE ) ? Jojo::getMultiLanguageString ( $p['pg_language'], true ) : '';
                $url .=  Jojo::rewrite('page',$p['pageid'],$p['pg_title']);
            }

            /* Calculate last modified date */
            $p['pg_xmlsitemap_lastmod']=="yes" ? $lastmod = strtotime($p['pg_updated']):$lastmod='';

            if($p['pg_url']=="lx" or $p['pg_url']=="search" or $p['pg_url']=="sitemap" or $p['pg_url']=="robots.txt" or $p['pg_url']=="tags" ) $lastmod='';

            /* Set priority */
            if($p['pg_xmlsitemap_priority']) {
                $priority = $p['pg_xmlsitemap_priority'];
            } else {
                if ($p['pageid'] == 1) {
                    // Homepage gets top priority
                    $priority = 1.0;
                } else if ($p['pg_parent'] == 0) {
                    // Top level pages have greater priority
                    $priority = 0.9;
                } else {
                     //Other pages get lesser priority
                    $priority = 0.7;
                }
            }

            /* Set changefreq */
            $changefreq = $p['pg_xmlsitemap_changefreq'];

            /* Add pages to sitemap */
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }

        return $sitemap;
    }

    /**
     * Site Search
     *
     */
    static function search($results, $keywords, $language, $booleankeyword_str=false)
    {
        global $_USERGROUPS;

        $data = Jojo::selectQuery("SELECT pageid FROM {page} WHERE pg_title = 'Not on Menu'");
        $_NOT_ON_MENU_ID = isset($data[0]['pageid']) ? $data[0]['pageid'] : 99999; //if for some reason this page doesn't exist, don't let it default to 0 (which would block all top-level pages from search results)

        $pagePermissions = new JOJO_Permissions();
        $boolean = ($booleankeyword_str) ? true : false;
        $keywords_str = ($boolean) ? $booleankeyword_str :  implode(' ', $keywords);
        if ($boolean && stripos($booleankeyword_str, '+') === 0  ) {
            $like = '1';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" AND (pg_body LIKE '%%%s%%' OR pg_title LIKE '%%%s%%')", Jojo::clean($keyword), Jojo::clean($keyword));
            }
        } elseif ($boolean && stripos($booleankeyword_str, '"') === 0) {
            $like = "(pg_body LIKE '%%%". implode(' ', $keywords). "%%' OR pg_title LIKE '%%%". implode(' ', $keywords) . "%%')";
        } else {
            $like = '(0';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" OR pg_body LIKE '%%%s%%' OR pg_title LIKE '%%%s%%'", Jojo::clean($keyword), Jojo::clean($keyword));
            }
            $like .= ')';
        }
        $query = "SELECT pageid, pg_title, pg_body, pg_link, pg_url, pg_language, pg_expirydate, pg_livedate, pg_ssl, ( (MATCH(pg_title) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ") * 0.2) + MATCH(pg_title, pg_desc, pg_body) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ") ) AS relevance ";
        $query .= "FROM {page} AS page ";
        $query .= "LEFT JOIN {language} AS language ON (page.pg_language = languageid) ";
        $query .= "WHERE $like";
        $query .= ($language) ? "AND pg_language = '$language' " : '';
        $query .= "AND language.active = 'yes' ";
        $query .= "AND pg_livedate<" . time() . " AND (pg_expirydate<=0 OR pg_expirydate>" . time() . ") ";
        $query .= "AND pg_link!='Jojo_Plugin_Jojo_search' ";
        $query .= "AND pg_parent != $_NOT_ON_MENU_ID ";
        $query .= " ORDER BY relevance DESC LIMIT 100";

        $data = Jojo::selectQuery($query, array($keywords_str, $keywords_str));

        if (_MULTILANGUAGE) {
            global $page;
            $mldata = Jojo::getMultiLanguageData();
            $homes = $mldata['homes'];
            $roots = $mldata['roots'];
        } else {
            $homes = array(1);
        }

        foreach ($data as $d) {
            $pagePermissions->getPermissions('page', $d['pageid']);
            /* If its not permitted to view then omit the result*/
            if (!$pagePermissions->hasPerm($_USERGROUPS, 'view')) continue;
            /* If its a root page for the language then omit the result*/
            if  (_MULTILANGUAGE && in_array($d['pageid'], $roots)) continue;
            /* If its a multilanguage site then check for the longcode language name for the url*/
            if (_MULTILANGUAGE) {
                $language = !empty($d['pg_language']) ? $d['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $lclanguage = $mldata['longcodes'][$language];
            }
            $result = array();
            $result['relevance'] = $d['relevance'];
            $result['title'] = $d['pg_title'];
            $result['body'] = $d['pg_body'];
            $result['url'] = (_MULTILANGUAGE) ? Jojo::getMultiLanguageString ( $language, false ) : '';
            /* If its a root level page, just return the root and set the display url to 'home'*/
            if (in_array($d['pageid'], $homes)) {
                $result['displayurl'] = (_MULTILANGUAGE) ? Jojo::getMultiLanguageString ( $language, false ) . ' (home page)' : ' (home page)';
           /* Use page url if we have it*/
            } elseif (!empty($d['pg_url'])) {
                $result['url'] .= $d['pg_url'].'/';
            /* else generate a URL from the data */
            } else {
                $result['url'] .= Jojo::rewrite('page', $d['pageid'], $d['pg_title']);
            }
            $result['absoluteurl'] =  ($d['pg_ssl'] == 'yes') ? _SECUREURL . '/' . $result['url'] : _SITEURL . '/' . $result['url'];
            $result['id'] = $d['pageid'];
            $result['plugin'] = 'Core';
            $result['type'] = 'General Content';
            $results[] = $result;
        }


        /* Return results */
        return $results;
    }

    /* Add a message a the bottom of the site to alert to debug mode being enabled on this site */
    static function debugmodestatus() {
        if (_DEBUG) {
            return "Debug Mode currently enabled.<br/><span style='font-size:80%'>This has an impact on performance and should be turned off before the site is made live.</span>";
        }
    }

    protected static function sendCacheHeaders($timestamp) {
        // A PHP implementation of conditional get, see
        //   http://fishbowl.pastiche.org/archives/001132.html
        $last_modified = substr(date('r', $timestamp), 0, -5) . 'GMT';
        $etag = '"'.md5($last_modified) . '"';
        // Send the headers
        header("Last-Modified: $last_modified");
        header("ETag: $etag");
        header('Cache-Control: private, max-age=28800');
        header('Expires: ' . date('D, d M Y H:i:s \G\M\T', time() + 28800));
        header('Pragma: ');
        // See if the client has provided the required headers
        $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ?
            stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) :
            false;
        $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
            stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) :
            false;
        if (!$if_modified_since && !$if_none_match) {
            return;
        }
        // At least one of the headers is there - check them
        if ($if_none_match && $if_none_match != $etag) {
            return; // etag is there but doesn't match
        }
        if ($if_modified_since && $if_modified_since != $last_modified) {
            return; // if-modified-since is there but doesn't match
        }
        // Nothing has changed since their last request - serve a 304 and exit
        header('HTTP/1.0 304 Not Modified');
        exit;
    }
}
