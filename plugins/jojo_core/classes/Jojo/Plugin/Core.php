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
        //$data = preg_replace('/<a([^>]*?)href=["\']\\(#\\)([a-z0-9-_]*)?["\']([^>]*?)>/i', '<a$1href="#$2"$3>', $data);
          //$data = preg_replace('/<a([^>]*?)href=["\']\\(#\\)([a-z0-9-_]*)?["\']([^>]*?)>/i', '<a$1href="#$2"$3>', $data);
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
        $query .=  " WHERE pg_parent = '$id' ORDER BY pg_order";
        $items = Jojo::selectQuery($query);
        $items = self::cleanItems($items, $for);
        return $items;
    }

    /* clean items for output */
    static function cleanItems($items, $for=false) {
        global $isadmin, $_USERGROUPS;
        $now    = time();
        static $pagePermissions;
        if (!$pagePermissions) {
            $pagePermissions = new Jojo_Permissions();
        }
        $mldata = Jojo::getMultiLanguageData();
        foreach ($items as $k=>&$i){
            $pagePermissions->getPermissions('page', $i['pageid']);
            $i['root'] = Jojo::getSectionRoot($i['pageid']);
            if ( (!$pagePermissions->hasPerm($_USERGROUPS, 'view') && !$pagePermissions->hasPerm($_USERGROUPS, 'show')) || (!$pagePermissions->hasPerm($_USERGROUPS, 'view') && !($for=='nav' || $for=='sitemap')) || $i['pg_livedate']>$now || (!empty($i['pg_expirydate']) && $i['pg_expirydate']<$now) || $i['pg_status']=='inactive' || ($for!='showhidden' && $i['pg_status']!='active') || ($for =='sitemap' && (!isset($mldata['sectiondata'][$i['root']]) || $i['pg_sitemapnav']=='no')) || ($for =='xmlsitemap' && ($i['pg_xmlsitemapnav']=='no' || $i['pg_index']=='no' || !isset($mldata['sectiondata'][$i['root']]))) || ($for =='breadcrumbs' && $i['pg_breadcrumbnav']=='no')) {
                unset($items[$k]);
                continue;
            }
            $i['id'] = $i['pageid'];
            $i['title'] = htmlspecialchars( (isset($i['pg_menutitle']) && !empty($i['pg_menutitle']) ? $i['pg_menutitle'] : $i['pg_title']), ENT_COMPAT, 'UTF-8', false);
            $i['desc'] = isset($i['pg_desc']) ? htmlspecialchars($i['pg_desc'], ENT_COMPAT, 'UTF-8', false) : '';
            if ($for=='sitemap' || $for=='xmlsitemap' || $for=='breadcrumbs' || $for=='nav') {
                unset($items[$k]['pg_body']);
            } else {
                // Snip for the index description
                $splitcontent = isset($i['pg_body']) ? Jojo::iExplode('[[snip]]', $i['pg_body']) : array();
                $i['bodyplain'] = array_shift($splitcontent);
                /* Strip all tags and template include code ie [[ ]] */
                $i['bodyplain'] = trim(strip_tags($i['bodyplain']));
                $i['bodyplain'] = strpos($i['bodyplain'], '[[')!==false ? preg_replace('/\[\[.*?\]\]/', '',  $i['bodyplain']) : $i['bodyplain'];
            }
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

    static function getPrefixById($id=false) {
        if ($id) {
                $prefix = rtrim(Jojo::getPageUrlPrefix($id), '/');
                return $prefix;
        }
        return false;
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
        $rawresults =  Jojo_Plugin_Jojo_search::searchPlugin($searchfields, $keywords, $language, $booleankeyword_str);
        $data = $rawresults ? self::getItemsById(array_keys($rawresults)) : '';
        $mldata = Jojo::getMultiLanguageData();
        $homes = $mldata['homes'];
        if ($data) {
            foreach ($data as $result) {
                $result['relevance'] = $rawresults[$result['id']]['relevance'];
                $result['type'] = 'none';
                $result['tags'] = isset($rawresults[$result['id']]['tags']) ? $rawresults[$result['id']]['tags'] : '';
               /* If its a root level page, just return the root and set the display url to 'home'*/
                $result['displayurl'] = in_array($result['pageid'], $homes) ?  rtrim(str_replace('http://', '', $result['absoluteurl']), '/') : rtrim($result['url'], '/');
                $results[] = $result;
            }
        }
        /* Return results */
        return $results;
    }

    /**
     * OpenGraph tags
     */
    static function ogdata($content)
    {
        global $page;
        $ogdata['site_name'] =_SITENAME;
        $ogdata['type'] = 'article';
        $ogdata['url'] = $page->getCorrectUrl();
        $ogdata['title'] = $content['title'];
        $ogdata['image'] = Jojo::getOption('site_logo', '');
        if (!$content['metadescription']) {
            /* Strip all tags and template include code ie [[ ]] */
            $description = trim(strip_tags($content['content']));
            $description = strpos($description, '[[')!==false ? preg_replace('/\[\[.*?\]\]/', '',  $description) : $description;
            $description = strlen($description) >400 ? substr($mbody=wordwrap($description, 400, '$$'), 0, strpos($mbody,'$$')) . '...' : $description;
            $ogdata['description'] = $description;
        } else {
            $ogdata['description'] = $content['metadescription'];
        }
        if ($location = Jojo::getOption('site_geolocation', '')) {
            $location = explode(',', $location);
            $ogdata['latitude'] = isset($location[0]) ? $location[0] : '';
            $ogdata['longitude'] = isset($location[1]) ? $location[1] : '';
        }
        $ogdata['street_address'] = Jojo::getOption('site_street_address', '');
        $ogdata['locality'] = Jojo::getOption('site_locality', '');
        $ogdata['region'] = Jojo::getOption('site_region', '');
        $ogdata['postal_code'] = Jojo::getOption('site_postal_code', '');
        $ogdata['country_name'] = Jojo::getOption('site_country_name', '');
        $ogdata['email'] = Jojo::getOption('site_email', '');
        $ogdata['phone_number'] = Jojo::getOption('site_phone_number', '');
        $ogdata['fax_number'] = Jojo::getOption('site_fax_number', '');

        $ogdata['fb_admins'] = Jojo::getOption('facebook_admins', '');
        $ogdata['fb_app_id'] = Jojo::getOption('facebook_app_id', '');

       $ogdata = array_merge($ogdata, $content['ogtags']);
        /* Return data */
        return $ogdata;
    }

    /*
    * Tags
    */
    static function getTagSnippets($ids)
    {
        $snippets = self::getItemsById($ids);
        return $snippets;
    }


    /*
    * Content snippet filter to replace [[snippet:]] in templates or content with defined html chunks
    */
    public static function getSnippet($content)
    {
        global $page, $sectiondata;
        if (strpos($content, '[[snippet:') === false) {
            return $content;
        }
        preg_match_all('/\[\[snippet: ?([^\]]*)\]\]/', $content, $matches);
        foreach($matches[1] as $k => $search) {
            $snippet = Jojo::selectRow("SELECT snippet FROM {snippet} WHERE " . ( is_numeric($search) ? "snippetid = '$search'" : " name = '$search'"));
            if ($snippet) {
                $content = str_replace($matches[0][$k], $snippet['snippet'], $content);
            } else {
                $content = str_replace($matches[0][$k], '', $content);
            }
        }
        /* Allow for recursive snippeting */
        $content = self::getSnippet($content);

         return $content;
    }

    /*
    * Content pseudo-filter to replace [[columnbreak]] etc in content with fluid column divs
    */
    public static function pagebreak($content)
    {
        if (strpos($content, '[[columnbreak')!==false) {

            $columns = substr_count($content, '[[columns]]');
            $uneven = 0;
            $brcount = substr_count($content, '[[columnbreak]]');
            $brcount =  (!$columns || $columns==1) ?  $brcount : $brcount / $columns;
            // 1/3 | 2/3 split
            if (strpos($content, '[[columnbreak13]]')!==false) {
                $brcount = 13;
                $uneven = 8;
                $content =  str_replace('[[columnbreak13]]', '[[columnbreak]]', $content);
            // 2/3 | 1/3 split
            } elseif (strpos($content, '[[columnbreak23]]')!==false) {
                $brcount = 23;
                $uneven = 4;
                $content =  str_replace('[[columnbreak23]]', '[[columnbreak]]', $content);
            }

            switch ($brcount) {
              case '1':
                $colspan = 6;
                break;
              case '2':
                $colspan = 4;
                break;
              case '3':
                $colspan=3;
                break;
              case '5':
                $colspan=2;
                break;
              case '13':
                $colspan = 4;
                break;
              case '23':
                $colspan = 8;
                break;
              default:
                $colspan=12;
            }

            $colopen = '<div class="row-fluid"><div class="span' . $colspan . ' first"><div class="columncontent">';
            $colclose = '</div></div></div>';
            $colbreak = '</div></div><div class="span' . ($uneven ? $uneven : $colspan) . '"><div class="columncontent">';
            $colbreak = Jojo::applyFilter("columns_breakformat", $colbreak);

            $content = strpos($content, '[[columns]]')!==false ? str_replace(array('<p>[[columns]]</p>', '<p>[[columns]] </p>', '<p>[[columns]]&nbsp;</p>','[[columns]]'), $colopen, $content) : $colopen . "\n" . $content;
            $content = strpos($content, '[[endcolumns]]') ? str_replace(array('<p>[[endcolumns]]</p>', '<p>[[endcolumns]] </p>', '<p>[[endcolumns]]&nbsp;</p>','[[endcolumns]]'), $colclose, $content) : $content . "\n" . $colclose;
            $content = str_replace(array('<p>[[columnbreak]]</p>', '<p>[[columnbreak]] </p>', '<p>[[columnbreak]]&nbsp;</p>','[[columnbreak]]'), $colbreak, $content);
        }
        return $content;
    }

    /*
    * Content pseudo-filter to replace [[subpages]] in content with subpages list
    */
    public static function subpages($content)
    {
        if (strpos($content, '[[subpages]]')!==false) {
            global $page;
            $pageid = $page->id;
            $subpages = Jojo::getNav($pageid, 1);
            if ($subpages) {
                $html = '<ul>' . "\n";
                foreach ($subpages as $s) {
                    $html .= '<li><a href="' . $s['url'] . '" title="' . $s['title'] . '">' . $s['label'] . '</a></li>' . "\n";
                }
                $html .= '</ul>';
            } else {
                $html = '';
            }

            $content = str_replace(array('<p>[[subpages]]</p>', '<p>[[subpages]] </p>', '<p>[[subpages]]&nbsp;</p>','[[subpages]]'), $html, $content);
        }
        return $content;
    }

    /* Add a message a the bottom of the site to alert to debug mode being enabled on this site */
    static function debugmodestatus() {
        if (_DEBUG) {
            return "Debug Mode currently enabled.<br/><span style='font-size:80%'>This has an impact on performance and should be turned off before the site is made live.</span>";
        }
    }

    static function systeminstaller_menu() {

        /*

         * TODO

         *

         * Change format so that each section has a "$type" = "keyvalue/asis/template" and "$data" and is rendered accordingly

         * keyvalue will loop through $data and display key-value pairs

         * asis will simply display what is stored in $data

         * template will load the specified template from $template and supply $data to it

         *

         */

        global $_USERGROUPS;

        if (!in_array('sysinstall', $_USERGROUPS)) {

            return "";

        }

        global $smarty;

        $smarty->assign('_USERGROUPS', $_USERGROUPS);



        /****************  LINKS  ******************/

        $sysmenu['custom']['links'] = array(

            'Admin'     => "admin/",

            'Event Log' => "admin/eventlog/",

            'Options'   => "admin/options/",

            'Setup'     => "setup/",

            'Logout'    => "logout/"

        );

        $sysmenu['custom']['links'] = Jojo::applyFilter('sysmenu-links', $sysmenu['custom']['links']);



        /****************  JOJOCMS  ****************/

        $sysmenu['JojoCMS'] = array(

            "_BASEDIR"      => _BASEDIR,

            "_ALTPLUGINDIR" => (defined('_ALTPLUGINDIR') ? _ALTPLUGINDIR : 'not set'),

            "Version"       => @file_get_contents(_BASEDIR.'/version.txt')

        );

        // Git branch

        if (file_exists(_BASEDIR.'/.git/HEAD') && $gitbranch = @file_get_contents(_BASEDIR.'/.git/HEAD')) {

            $sysmenu['JojoCMS']['GIT Branch'] = substr($gitbranch, strrpos($gitbranch, '/')+1);

        }



        /***************  THIS USER  ****************/

        $sysmenu['custom']['user']['fields'] = array(

            'UserID' => 'userid',

            'Login' => 'us_login',

            'Email' => 'us_email',

            'Timezone' => 'us_timezone'

        );

        $sysmenu['custom']['user']['fields'] = Jojo::applyFilter('sysmenu-userfields', $sysmenu['custom']['user']['fields']);

        $sysmenu['custom']['user']['groups'] = implode(', ', $_USERGROUPS);



        /***************  THIS SITE  ****************/

        $sysmenu['This Website']["_WEBDIR"] = _WEBDIR;

        $sysmenu['This Website']["_MYSITEDIR"] = _MYSITEDIR;



        // Add the database name

        $sysmenu['This Website']["_DBNAME"] = _DBNAME;

        $sysmenu['This Website']["_DBUSER"] = _DBUSER;



        // Show the theme folder

        if (!isset($_SESSION['admintweaks']['theme'])) {

            $theme = Jojo::selectRow("SELECT `name` FROM {theme} WHERE `active` = 'yes'");

            if ($theme) {

                $theme = $theme['name'];

                $_SESSION['admintweaks']['theme'] = $theme;

                //$themetestpath = '/'.$theme.'/templates/template.tpl';

            }

        }

        if ($_SESSION['admintweaks']['theme']) {

            $sysmenu['This Website']['Theme'] = $_SESSION['admintweaks']['theme'];

        }



        // Show the "last maintenance" time

        $lastmaintenance = Jojo::getOption("last_maintenance");

        $lastmaintenance = $lastmaintenance .= ' ('.date("c", $lastmaintenance).')';

        $sysmenu['This Website']['Last Maintenance'] = $lastmaintenance;



        // Allow other plugins to add to the fields

        $sysmenu = Jojo::applyFilter('sysmenu', $sysmenu);



        /****************  PLUGINS  ****************/

        // Show the installed plugins (after the filter so plugins can't hide themselves

        $plugins_raw = Jojo::selectQuery("SELECT name FROM {plugin} WHERE active = 'yes' ORDER BY priority DESC, name");

        $sysmenu['custom']['plugins'] = array();

        foreach ($plugins_raw as $pl) {

            $sysmenu['custom']['plugins'][] = $pl['name'];

        }



        $smarty->assign('sysmenu', $sysmenu);

        return $smarty->fetch('admin/systeminstaller-menu.tpl');

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
