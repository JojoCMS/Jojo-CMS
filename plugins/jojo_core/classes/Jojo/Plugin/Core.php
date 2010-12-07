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

    static function admin_action_after_save_page($id)
    {
        $p = self::getItemsById($id, 'showhidden');
        if ($p && (!$p['pg_htmllang'] || !$p['pg_language'])) {
            $mldata = Jojo::getMultiLanguageData();
            $htmllanguage =  !$p['pg_htmllang'] ? $mldata['sectiondata'][Jojo::getSectionRoot($id)]['lc_defaultlang'] : $p['pg_htmllang'];
            $section =  !$p['pg_language'] ? $mldata['sectiondata'][Jojo::getSectionRoot($id)]['lc_code'] : $p['pg_language'];
            Jojo::updateQuery("UPDATE {page} SET `pg_htmllang`=?, `pg_language`=? WHERE `pageid`=?", array($htmllanguage, $section, $id));
        }
    }
    
    /**
     * Sitemap filter
     *
     * Receives existing sitemap and adds pages section
     */
    static function sitemap($sitemap)
    {
        $pagetree = new hktree();
        $pages = self::getItems('sitemap', $sortby='pg_order');
        /* Add pages to the sitemap */
        foreach ($pages as $k => $p) {
            $pagetree->addNode($p['id'], $p['pg_parent'], $p['title'], $p['url']);
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
        $pages = self::getItems('xmlsitemap');
        /* Add pages to the sitemap */
        foreach ($pages as $k =>$p) {
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
            $url = $p['absoluteurl'];
            /* Add pages to sitemap */
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }
        return $sitemap;
    }

    static function getItems($for=false, $sortby = false) {
        $query = 'SELECT * FROM {page} p';
        $query .= $sortby ? " ORDER BY " . $sortby : '';
        $items = Jojo::selectQuery($query);
        $items = self::cleanItems($items, $for);
        return $items;
    }

    static function getItemsById($ids=false, $for=false) {
        $items = array();
        $query  = "SELECT *";
        $query .= " FROM {page}";
        $query .=  is_array($ids) ? " WHERE pageid IN ('". implode("',' ", $ids) . "')" : " WHERE pageid=$ids";
        $items = Jojo::selectQuery($query);
        if (!$items) return false;
        $items = self::cleanItems($items, $for);
        if (!$items) return false;
        $items = is_array($ids) ? $items : $items[0];
        return $items;
    }

    static function getChildrenById($id=false, $for=false) {
        $query  = "SELECT *";
        $query .= " FROM {page}";
        $query .=  " WHERE pg_parent = '$id'";
        $items = Jojo::selectQuery($query);
        $items = self::cleanItems($items, $for);
        return $items;
    }

    /* clean items for output */
    static function cleanItems($items, $for=false) {
        global $_USERGROUPS;
        $now    = time();
        $pagePermissions = new JOJO_Permissions();
        foreach ($items as $k=>&$i){
            $pagePermissions->getPermissions('page', $i['pageid']);
            $mldata = Jojo::getMultiLanguageData();
            $i['root'] = Jojo::getSectionRoot($i['pageid']);
            if ((isset($mldata['sectiondata'][$i['root']]) && !$mldata['sectiondata'][$i['root']]['active']) || !$pagePermissions->hasPerm($_USERGROUPS, 'view') || $i['pg_livedate']>$now || (!empty($i['pg_expirydate']) && $i['pg_expirydate']<$now) || $i['pg_status']=='inactive' || ($for!='showhidden' && $i['pg_status']!='active') || ($for =='sitemap' && $i['pg_sitemapnav']=='no') || ($for =='xmlsitemap' && ($i['pg_xmlsitemapnav']=='no' || $i['pg_index']=='no' || strpos(strtolower($i['pg_link']), 'jojo_plugin_admin')!==false ) ) || ($for =='breadcrumbs' && $i['pg_breadcrumbnav']=='no')) {
                unset($items[$k]);
                continue;
            }
            $i['id'] = $i['pageid'];
            $i['title'] = htmlspecialchars( (isset($i['pg_menutitle']) && !empty($i['pg_menutitle']) ? $i['pg_menutitle'] : $i['pg_title']), ENT_COMPAT, 'UTF-8', false);
            $i['desc'] = isset($i['pg_desc']) ? htmlspecialchars($i['pg_desc'], ENT_COMPAT, 'UTF-8', false) : '';
            // Snip for the index description
            $i['bodyplain'] = isset($i['pg_body']) ? array_shift(Jojo::iExplode('[[snip]]', $i['pg_body'])) : '';
            /* Strip all tags and template include code ie [[ ]] */
            $i['bodyplain'] = preg_replace('/\[\[.*?\]\]/', '',  trim(strip_tags($i['bodyplain'])));
            $i['date'] = isset($i['pg_updated']) ? $i['pg_updated'] : '';
            $i['image'] = (isset($i['pg_image']) && !empty($i['pg_image'])) ? 'pages/' . $i['pg_image'] : '';
            $i = self::getUrl($i);
            $i['plugin'] = 'Core';
            unset($items[$k]['pg_body_code']);
        }
        return $items;
    }

    static function getUrl($item) {
        $mldata = Jojo::getMultiLanguageData();
        $homes = $mldata['homes'];
        $roots = $mldata['roots'];
        $pageprefix = Jojo::getPageUrlPrefix($item['pageid']);
        if (isset($item['pg_link']) && substr(strtolower($item['pg_link']), 0, 7) == 'http://') { 
        //external pages
            $item['absoluteurl'] = $item['url'] = $item['pg_link'];
        } elseif  (in_array($item['pageid'], $roots)){  
        //root page
            $item['absoluteurl'] = $item['url'] = false;
        } elseif (in_array($item['pageid'], $homes)){  
        // home pages
            $item['absoluteurl'] = $item['url'] = ((isset($item['pg_ssl']) && $item['pg_ssl'] == 'yes') ? _SECUREURL : _SITEURL) . '/' . $pageprefix;
        } else {
            $item['url'] = $pageprefix . (!empty($item['pg_url']) ? $item['pg_url'] : $item['pageid'] . '/' .  Jojo::cleanURL($item['pg_title'])) . '/';
            $item['absoluteurl'] = ((isset($item['pg_ssl']) && $item['pg_ssl'] == 'yes') ? _SECUREURL : _SITEURL) . '/' . $item['url'];
        }
        return $item;
    }

    /**
     * Site Search
     */
    static function search($results, $keywords, $language, $booleankeyword_str=false)
    {
        $searchfields = array(
            'plugin' => 'Core',
            'table' => 'page',
            'idfield' => 'pageid',
            'languagefield' => 'pg_htmllang',
            'primaryfields' => 'pg_title',
            'secondaryfields' => 'pg_title, pg_desc, pg_body',
        );
        $rawresults =  Jojo_Plugin_Jojo_search::searchPlugin($searchfields, $keywords, $language, $booleankeyword_str=false);
        $data = $rawresults ? self::getItemsById(array_keys($rawresults)) : '';
        $mldata = Jojo::getMultiLanguageData();
        $homes = $mldata['homes'];
        if ($data) {
            foreach ($data as $result) {
                $result['relevance'] = $rawresults[$result['id']]['relevance'];
                $result['type'] = 'General Content';
                $result['tags'] = isset($rawresults[$result['id']]['tags']) ? $rawresults[$result['id']]['tags'] : '';
               /* If its a root level page, just return the root and set the display url to 'home'*/
                if (in_array($result['pageid'], $homes)) $result['displayurl'] = $result['absoluteurl'];
                $results[] = $result;
            }
        }
        /* Return results */
        return $results;
    }

    /*
    * Tags
    */
    static function getTagSnippets($ids)
    {
        $snippets = self::getItemsById($ids);
        return $snippets;
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
