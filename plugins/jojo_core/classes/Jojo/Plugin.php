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

class Jojo_Plugin {

    /* All the values from the database about this page */
    var $page = array();

    /* The page id */
    var $id;

    /* The id of the active database record */
    var $qid;

    /* The table the page refers to */
    var $qt;

    /* A permissions object for this page */
    var $perms;

    /* Boolean whether the content is expired/not live/disabled */
    var $expired = false;

    var $revisions = false;


    function __construct($id)
    {
        $this->id = $id;

        $this->page = Jojo::selectRow(sprintf('SELECT * FROM {page} WHERE pageid = %d', $id));

        $this->perms = new Jojo_Permissions();
        $this->perms->getPermissions('page', $id);

        $this->qt = 'page';
        $this->qid = $id;

        /* Populate info on subpages belonging to this page */
        $subpages = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_parent = ? AND pg_mainnav = 'yes' ORDER BY pg_order", array($id));
        foreach ($subpages as $k => $sub) {
            $subpage = array();
            $subpage['pageid'] = $sub['pageid'];
            $subpage['name'] = $sub['pg_title'];
            $subpage['description'] = $sub['pg_desc'];
            if (!defined('_MULTILANGUAGE')) define('_MULTILANGUAGE', Jojo::yes2true(Jojo::getOption('multilanguage'))); //TODO - why is this not defined on some sites?
            if (_MULTILANGUAGE) {
                $languageheader = Jojo::getMultiLanguageString( $sub['pg_language'], true );
                $mldata = Jojo::getMultiLanguageData();
                if ( $sub['pageid'] == $mldata['homes'][$sub['pg_language']]) {
                    // this is a language/country homepage
                    $langCountryURL = $languageheader;
                } else {
                    $langCountryURL = $languageheader . $sub['pg_url'].'/';
                }
                $subpage['url'] =  Jojo::either( Jojo::onlyif($sub['pg_url'], $langCountryURL ), $sub['pg_link'], Jojo::rewrite('page',$subpage['pageid'],$subpage['name']));
            } else {
                $subpage['url'] =  Jojo::either( Jojo::onlyif($sub['pg_url'],$sub['pg_url'].'/'),$sub['pg_link'], Jojo::rewrite('page',$subpage['pageid'],$subpage['name']));
            }
            $subpage['rollover'] =  Jojo::either($sub['pg_desc'],$sub['pg_title']);
            $subpage['hyperlink'] = "<a href = '" . $subpage['url']."' title = '" . $subpage['rollover']."'>" . $subpage['name']."</a>";
            $this->page['subpages'][] = $subpage;
        }

        $now = strtotime('now');
        if (($this->page['pg_status'] == 'inactive') || ($now < $this->page['pg_livedate']) || (($now > $this->page['pg_expirydate']) && ($this->page['pg_expirydate'] > 0)) ) {
            $this->expired = true;
        }

    }

    function getContent()
    {
        global $smarty;
        $result = array();
        $result['title']            = $this->page["pg_title"];
        $result['menutitle']        =  Jojo::either($this->page["pg_menutitle"],$this->page["pg_title"]);
        $result['seotitle']         =  Jojo::either($this->page["pg_seotitle"],$this->page["pg_title"]);
        $result['desc']             = $this->page["pg_desc"];
        $result['metadescription']  =  Jojo::either($this->page["pg_metadesc"],$this->page["pg_desc"]);
        $result['metakeywords']     = '';
        $result['content']          = $this->page["pg_body"];
        $result['rssicon']          = array();
        $result['index']            = isset($this->page["pg_index"]) && ($this->page["pg_index"] == 'no') ? false : true;
        $result['followto']         = isset($this->page["pg_followto"]) && ($this->page["pg_followto"] == 'no') ? false : true;
        $result['followfrom']       = isset($this->page["pg_followfrom"]) && ($this->page["pg_followfrom"] == 'no') ? false : true;
        $result['javascript']       = '';
        $result['css']              = '';
        $result['head']             = isset($this->page["pg_head"]) ? $this->page["pg_head"] : '';
        $result = Jojo::applyFilter('jojo_plugin:result', $result);

        $root = 0;
        if (_MULTILANGUAGE) {
            $mldata = Jojo::getMultiLanguageData();
            $root = $mldata['roots'][$this->getValue('pg_language')];
            $home = $mldata['homes'][$this->getValue('pg_language')];
        }
        $result['breadcrumbs'] = $this->_getBreadcrumbs($root);

        if (_MULTILANGUAGE) {
            /* Load Languages */
            // new language/country functionality.  Get actual language for the page but identify the language
            $language = Jojo::selectRow("SELECT * FROM {language} WHERE languageid = ?", Jojo::getPageLanguageCode( $this->page ['pageid']));
            $charset = isset($language) ? $language['charset'] : 'utf-8';
            $result['longlanguage']  = $language['name'] != '' ?  Jojo::cleanURL($language['name']) : $this->page['pg_language'];
            $result['charset'] = $charset;
        }

        /* Get page tags if  the tags class is available */
        if (class_exists('Jojo_Plugin_Jojo_Tags')) {
            /* Split up tags for display */
            $tags = Jojo_Plugin_Jojo_Tags::getTags('Core', $this->page['pageid']);
            if ($tags) {
                $smarty->assign('tags', $tags);
            }
        }

        /* Class specific content */
        $extra = $this->_getContent();
        foreach ($extra as $k => $v) {
              $result[$k] = $v;
        }
        if ($this->expired) {
            $result['content'] = $smarty->fetch('expired.tpl');
        }

        return $result;
    }

    function _getContent()
    {
        return array();
    }

    function isSecure()
    {
        return Jojo::yes2true($this->page['pg_ssl']);
    }

    /**
     * Return a page object
     */
    static function getPage($id)
    {
        if (!$id) {
            return new Jojo_Plugin_404();
        }

        /* Lookup by ID */
        $page = Jojo::selectRow('SELECT pageid, pg_link FROM {page} WHERE pageid = ?', $id);
        if (!$page) {
            /* Not found */
            return false;
        }

        /* Found the page */
        $class = ($page['pg_link'] != '') ? $page['pg_link'] : 'Jojo_Plugin';
        if (!class_exists($class)) {
            $class = 'Jojo_Plugin_404';
        }

        return new $class($page['pageid']);
    }

    function _getBreadcrumbs($root = 0)
    {
        $breadcrumbs = array();
        $pageid = $this->page['pageid'];
        $maxDepth = 50;

        /* Get multilanguage data */
        if (_MULTILANGUAGE) {
            $mldata = Jojo::getMultiLanguageData();
            $root = $mldata['roots'][$this->getValue('pg_language')];
            $home = $mldata['homes'][$this->getValue('pg_language')];
        } else {
            $home = 1;
        }

        /* Skip if we are on the homepage */
        if ($pageid == $home) {
            return $breadcrumbs;
        }

        /* Loop until we work form this page back to the root */
        $added_home = false;
        while (true) {
            if (!$pageid) {
                break;
            }
            $bcpage = Jojo::selectQuery("SELECT pageid, pg_title, pg_menutitle, pg_title, pg_breadcrumbnav, pg_language, pg_url, pg_ssl, pg_desc, pg_parent FROM {page} WHERE pageid = ? LIMIT 1", $pageid);
            if (!isset($bcpage[0])) {
                break;
            }
            $page = $bcpage[0];
            $pageid = $page['pg_parent'];

            /* Set to not show on the bread crumb nav so skip */
            if ($page['pg_breadcrumbnav'] != 'yes') {
                continue;
            }

            /* Create the URL */
            $link = Jojo::urlPrefix(Jojo::yes2true($page['pg_ssl']));
            if ($page['pageid'] == $home) {
                $link = (_MULTILANGUAGE) ? (Jojo::getMultiLanguageString($page['pg_language'], false)) : _SITEURL . '/';
                $added_home = true;
            } elseif ($page['pg_url']) {
                $link .= (_MULTILANGUAGE) ? (Jojo::getMultiLanguageString ($page['pg_language'], false)) : '';
                $link .= $page['pg_url'] . '/';
            } else {
                $link .= (_MULTILANGUAGE) ? Jojo::getMultiLanguageString ($page['pg_language'], false) : '';
                $link .= Jojo::rewrite('page', $page['pageid'], $page['pg_title']);
            }

            /* Add the bread crumb */
            $breadcrumbs[] = array(
                               'name' => ($page['pg_menutitle']) ? $page['pg_menutitle'] : $page['pg_title'],
                               'rollover' => ($page['pg_desc']) ? $page['pg_desc'] : $page['pg_title'],
                               'url' => $link
                               );

            /* Are we done? */
            if ($added_home || $page['pg_parent'] == '' || !--$maxDepth) {
                /* No parent, too deep or added home page so stop */
                break;
            }

            /* Did we hit the root level? */
            if ($pageid == $root) {
                /* At the root level so add the home page then stop */
                $pageid = $home;
                $added_home = true;
            }
        }

        /* Reverse the bread crumbs */
        return array_reverse($breadcrumbs);
    }

    function getMenu($menutype='main',$depth)
    {
        return $this->_getMenu($menutype,$depth);
    }

    function _getMenu($menutype='main',$depth=5)
    {
        require_once(_BASEDIR."/classes/menu.class.php");
        $menu = new menu();
        $menu->maxdepth = $depth;
        $menu->pg_mainnav = 'pg_'.$menutype.'nav';
        $menu->domid = ($menutype == 'main') ? 'nav' : $menutype.'-nav';
        if (Jojo::yes2true(Jojo::getOption('menuuseindex'))) $menu->useindex = true;
        $menu->populate();
        $menu->activepageid = $this->id;

        if (_MULTILANGUAGE) {
            $mldata = Jojo::getMultiLanguageData();
            $root = $mldata['roots'][$this->getValue('pg_language')];
            return $menu->display($root);
        }

        return $menu->display();
    }

    function getCorrectUrl()
    {
        /* Allow URLs that have the Google Adwords / Yahoo tracking code */
        $allowed_vars = array('__utma', 'gclid=', 'gad=', 'OVKEY=', 'OVRAW=', 'OVMTC=', 'utm_source=','utm_medium=','utm_term=','utm_content=','utm_campaign=','OVADID=','OVKWID=');
        $allowed_vars = Jojo::applyFilter('index_allowed_vars', $allowed_vars); //Allow plugins to add additional safe strings here

        foreach ($allowed_vars as $var) {
            if (strpos($_SERVER['REQUEST_URI'], $var) !== false) {
                return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            }
        }

         /* Use the page url if we have it, else generate something */
        $link = '';
        if ($this->page['pg_url']) {
            $link .= $this->page['pg_url'] . '/';
        } else {
            $link .=  Jojo::rewrite('page', $this->page['pageid'], $this->page['pg_title']);
        }

        /* Add the language prefix if we need it */
        if (_MULTILANGUAGE) {
            $mldata = Jojo::getMultiLanguageData();
            $home = $mldata['homes'][$this->getValue('pg_language')];
            $link = Jojo::getMultiLanguageString ($this->page['pg_language'], false) . $link;
        } else {
            $home = 1;
        }

        /* Are we on the homepage? */
        $expecteduri = '/' . $link;
        if ($this->id == $home) {
            /* We are on a langauge homepage */
            $expecteduri = (_MULTILANGUAGE) ? '/' . Jojo::getMultiLanguageString($this->page['pg_language'], false) : '';
        }

        /* recalculate admin links */
        $expecteduri = Jojo::getAdminUri($expecteduri);

        /* Is this a secure page? */
        if ($this->isSecure()) {
            return _SECUREURL . $expecteduri;
        }
        return _SITEURL . $expecteduri;
    }

    function getValue($field)
    {
          if (isset($this->page[$field])) {
            return $this->page[$field];
        }
        return false;
    }
    /* regex to check standard plugin URL formats */
    static function isPluginUrl($uri)
    {
        $uribits = array();
        if (preg_match('#^(.+)/([0-9]+)/([^/]+)$#', $uri, $matches)) {
            /* "$prefix/[id:integer]/[string]" eg "articles/123/name-of-article/" */
            $uribits['prefix'] = $matches[1];
            $uribits['getvars'] = array(
                        'id' => $matches[2]
                        );
         } elseif (preg_match('#^(.+)/([0-9]+)$#', $uri, $matches)) {
            /* "$prefix/[id:integer]" eg "articles/123/" */
            $uribits['prefix'] = $matches[1];
            $uribits['getvars'] = array(
                        'id' => $matches[2]
                        );
        } elseif (preg_match('#^(.+)/p([0-9]+)$#', $uri, $matches)) {
            /* "$prefix/p[pagenum:([0-9]+)]" eg "articles/p2/" for pagination of articles */
            $uribits['prefix'] = $matches[1];
            $uribits['getvars'] = array(
                        'pagenum' => $matches[2]
                        );
        } elseif (preg_match('#^(.+)/rss$#', $uri, $matches)) {
            /* eg "articles/rss/" for rss feeds */
            $uribits['prefix'] = $matches[1];
            $uribits['getvars'] = array(
                        'action' => 'rss'
                        );
        } elseif (preg_match('#^(.+)/([a-z0-9-_]+)$#', $uri, $matches)) {
            /* "$prefix/[url:((?!rss)string)]" eg "articles/name-of-article/" ignoring "articles/rss" */
            $uribits['prefix'] = $matches[1];
            $uribits['getvars'] = array(
                        'url' => $matches[2]
                        );
        } else {
            /* Didn't match */
            return false;
        }
        return $uribits;
    }

}
