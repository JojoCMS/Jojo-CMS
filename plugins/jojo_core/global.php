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

$templateoptions['dateparse']  = false;
$templateoptions['frajax'] = false;
$templateoptions['menu'] = false;
$smarty->assign('templateoptions', $templateoptions);
$smarty->assign('htmldoctype', (boolean)(Jojo::getOption('doctype', 'xhtml')=='html5'));
$smarty->assign('xmldoctype', '<?xml version="1.0" encoding="utf-8" ?>');
$smarty->assign('boilerplatehtmltag', (boolean)(Jojo::getOption('boilerplate_htmltag', 'no')=='yes'));
$smarty->assign('modernizr', Jojo::getOption('modernizr', 'no'));
$smarty->assign('jqueryhead', (boolean)(Jojo::getOption('jquery_head', 'yes')=='yes'));
$smarty->assign('commonhead', (boolean)(Jojo::getOption('commonjs_head', 'yes')=='yes'));

/* Create current section/ current sub pages array to show cascading selected menu levels beyond child*/
$root = Jojo::getSectionRoot($page->id);
$selectedPages = Jojo::getSelectedPages($page->id, $root);
$smarty->assign('selectedpages', $selectedPages);
$smarty->assign('pageurlprefix', Jojo::getPageUrlPrefix($page->id));

$mldata = Jojo::getMultiLanguageData();
$sectiondata =  isset($mldata['sectiondata'][$root]) ? $mldata['sectiondata'][$root] : '';
$smarty->assign('home', ($sectiondata ? $sectiondata['home'] : 1));
$smarty->assign('root', $root);
$smarty->assign('languagelist', $mldata['sectiondata']);

/* Get one level of main navigation for the top navigation */
$mainnav = Jojo::getNav($root, Jojo::getOption('nav_mainnav', 0));
$smarty->assign('mainnav', $mainnav);

/* Get one level of navigation for the footer */
$footernav = Jojo::getNav($root, Jojo::getOption('nav_footernav', 0), 'footernav');
$smarty->assign('footernav', $footernav);

/* Get one level of navigation for the secondarynav */
$secondarynav =  Jojo::getOption('use_secondary_nav', 'no')=='yes' ? Jojo::getNav($root, 1, 'secondarynav') : '';
$smarty->assign('secondarynav', $secondarynav);

/* Get 2 levels of sub navigation as a separate variable if mainnav is only one level*/
if ($page->getValue('pg_parent') != $root && isset($selectedPages[1])) {
    /* Get sister pages to this page */
    $subnav = Jojo::getNav($selectedPages[1], Jojo::getOption('nav_subnav', 2));
} else {
    /* Get children pages of this page */
    $subnav = Jojo::getNav($page->id, Jojo::getOption('nav_subnav', 2));
}
$smarty->assign('subnav', $subnav);

/* Current year (e.g. for copyright statement) */
$smarty->assign('currentyear', date('Y'));

/* Breadcrumb separator */
$smarty->assign('sep', htmlspecialchars(Jojo::getOption('breadcrumbs_sep', '>'), ENT_COMPAT, 'UTF-8', false));
