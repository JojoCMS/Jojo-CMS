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
 * @package jojo_robots
 */

class Jojo_Plugin_Jojo_robots extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty;

        /* An array of pages to exclude - in addition to any pages marked as noindex in the admin area */
        $disallow = array('_docs','_htaccess','_stats', 'actions',
                          'classes','includes','install','js','css',
                          'config','external','login','templates',
                          'forgot-password','change-password','register');

        if (Jojo::getOption('robots.txt')) {
            echo Jojo::getOption('robots.txt');
            exit();
        }

        if (Jojo::fieldExists('page', 'pg_index')) {
            /* only include public pages that are set to index = no - we don't want to advertise private pages */
            $pages = Jojo::selectQuery("SELECT pageid, pg_url FROM {page} WHERE pg_index='no' AND pg_url!=''");
            $perms = new Jojo_Permissions();
            foreach ($pages as $p) {
                /* check for public permissions on each page */
                $perms->getPermissions('page', $p['pageid']);
                if ($perms->hasPerm(array('everyone'), 'show')) {
                    /* page is available to the public, so add to the disallow array */
                    $disallow[] = $p['pg_url'];
                }
            }
        }

        /* Apply filter to allow other plugins to alter the file */
        $disallow = Jojo::applyFilter('jojo_robots_disallow', $disallow);

        /* remove duplicates and sort alphabetically */
        $disallow = array_unique($disallow);
        sort($disallow);

        /* Assign the disallow array to smarty */
        $smarty->assign('disallow', $disallow);

        /* allow additional rules to be added by plugins */
        $rules = '';
        $rules = Jojo::applyFilter('jojo_robots_rules', $rules);
        $smarty->assign('rules', $rules);

        /* Fetch the content and output it */
        header('Content-type: text/plain');
        $smarty->display('jojo_robots.tpl');
        exit();
    }

    function getCorrectUrl()
    {
        /* Act like a file, not a folder */
        $url = rtrim(parent::getCorrectUrl(), '/');
        return $url;
    }
}