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

//$templateoptions['dateparse']      = false;

$menu = Jojo::getOption('menu');

/* Don't create default menus */
$templateoptions['menu'] = false;

/* Create current section/ current sub pages array to show cascading selected menu levels beyond child*/
$selectedPages = _getSelected($page->id);
$smarty->assign('selectedpages', $selectedPages);

/* Create navigation array */
$root = 0;
if (_MULTILANGUAGE && isset($page)) {
    /* If on a multilanuage site, get the root for the current language */
    $mldata = Jojo::getMultiLanguageData();
    $root = $mldata['roots'][$page->getValue('pg_language')];
}

/* Get one level of main navigation for the top navigation */
$smarty->assign('mainnav', _getNav($root, 0));

/* Get one level of navigation for the footer */
$smarty->assign('footernav', _getNav($root, 0, 'footernav'));

/* Get 2 levels of sub naviation */
if ($page->getValue('pg_parent') != $root) {
    /* Get sister pages to this page */
    $smarty->assign('subnav', _getNav($selectedPages[1], 2));
} else {
    /* Get children pages of this page */
    $smarty->assign('subnav', _getNav($page->id, 2));
}

/* Get tags for the page, if it has them (articles do this already) */
   global $tags;
   if (!isset($tags)) {
    foreach (JOJO::listPlugins('jojo_tags.php') as $pluginfile) {
        require_once($pluginfile);
             $tags = JOJO_Plugin_Jojo_Tags::getTags('Core', $page->id);
            if (count($tags) > 0) {
                $smarty->assign('tags', $tags);
            }
        break;
    }
}

/* Functions */
function _getNav($root, $subnavLevels, $field = 'mainnav')
{
    global $_USERGROUPS;

    /* Create permissions object */
    static $perms;
    if (!$perms) {
        $perms = new Jojo_Permissions();
    }

    /* Get multilanguage data */
    if (_MULTILANGUAGE) {
        global $page;
        $mldata = Jojo::getMultiLanguageData();
        $home = $mldata['homes'][$page->getValue('pg_language')];
    } else {
        $home = 1;
    }

    /* Get pages from database */
    static $_cached;
    if (!isset($_cached[$field])) {
        $now    = time();
        // If pg_mainnavalways exists - and requested menu is mainnav - adjust query to include
        // those pages that are configured to appear in all main nav menus.
        if ((_MULTILANGUAGE) && (Jojo::fieldExists ( 'page', 'pg_mainnavalways' )) && ($field == 'mainnav')) {
            $query = sprintf("SELECT
                           pageid, pg_parent, pg_url, pg_link, pg_title, pg_desc, pg_menutitle, pg_language, pg_status, pg_livedate, pg_expirydate, pg_followto, pg_mainnavalways, pg_secondarynav
                         FROM
                           {page}
                         WHERE
                           (pg_%s = 'yes' or pg_mainnavalways = 'yes')
                         ORDER BY
                           pg_order", $field);
        } else {
        $query = sprintf("SELECT
                           pageid, pg_parent, pg_url, pg_link, pg_title, pg_desc, pg_menutitle, pg_language, pg_followto, pg_status, pg_livedate, pg_expirydate
                         FROM
                           {page}
                         WHERE
                           pg_%s = 'yes'
                         ORDER BY
                           pg_order", $field);
        }
        $_cached[$field] = array();
        $result = Jojo::selectquery($query);
        foreach ($result as $k => $row) {
            // strip out expired pages
            if ($row['pg_livedate']>$now || (!empty($row['pg_expirydate']) && $row['pg_expirydate']<$now) || ($row['pg_status']=='inactive' || ($row['pg_status']=='hidden' && !isset($_SESSION['showhidden'])))) {
                unset($result[$k]);
                continue;
            }
            $r = $row['pg_parent'];
            if (!isset($_cached[$field][$r])) {
                $_cached[$field][$r] = array();
            }
            $_cached[$field][$r][] = $row;
            if ((_MULTILANGUAGE) && (isset($row['pg_mainnavalways'])) && ($row['pg_mainnavalways'] == 'yes') && ($r != $root)) {
                if ((($field == 'mainnav') && ((in_array ($r, $mldata['roots'])) || ($r == 1)))) {
                    $_cached[$field][$root][] = $row;
                }
            }
        }
    }
    $nav = isset($_cached[$field][$root]) ? $_cached[$field][$root] : array();

    foreach ($nav as $id => &$n) {
        /* Remove pages the user isn't allowed to be shown */
        $perms->getPermissions('page', $n['pageid']);
        if (!$perms->hasPerm($_USERGROUPS, 'show')) {
           unset($nav[$id]);
           continue;
        }

        /* Create the url for this page */
        $n['url'] = _SITEURL . '/' . (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($n['pg_language'], false) : '');
        if ($n['pageid'] != $home) {
            /* Use page url is we have it, else generate something */
            $n['url'] .= ($n['pg_url'] ? $n['pg_url'] : $n['pageid'] . '/' . Jojo::cleanURL($n['pg_title'])) . '/';
        }
        /* Create title and label for display */
        $n['title'] = htmlspecialchars(($n['pg_desc'] ? $n['pg_desc'] : $n['pg_title']), ENT_COMPAT, 'UTF-8', false);
        $n['label'] = htmlspecialchars(($n['pg_menutitle'] ? $n['pg_menutitle'] : $n['pg_title']), ENT_COMPAT, 'UTF-8', false);

        if ($subnavLevels) {
           /* Add sub pages to this page */
           $n['subnav'] = _getNav($n['pageid'], $subnavLevels - 1, $field);
        }
    }
    return $nav;
}

/* Get currently selected page and step back up through parents to build a current section/sub pages array */
function _getSelected($pageid) {
    if (!$pageid) {
        return array();
    }

    /* Cache the page parents */
    static $_pageParent;
    if (!is_array($_pageParent)) {
       $query = "SELECT
                       pageid, pg_parent
                     FROM
                      {page}";
       $_pageParent = Jojo::selectAssoc($query);
    }

    global $root;

    /* Start with the current page */
    $selectedPages = array($pageid);
    $depth = 0;

    while (($selectedPages[0] != $root) && ($selectedPages[0] != 0) && ($depth < 10)) {
       /* Find the parent of this iteration's top page */
       if (!isset($_pageParent[$selectedPages[0]])) {
           return $selectedPages;
       }
       $pg_parent = $_pageParent[$selectedPages[0]];

       /* Add new parent to top of array and move others down */
       array_unshift($selectedPages, $pg_parent);
       $depth ++;
    }
    return $selectedPages;
}
