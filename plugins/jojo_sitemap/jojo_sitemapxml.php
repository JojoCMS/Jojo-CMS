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
 * @package jojo_sitemap
 */


class Jojo_Plugin_Jojo_SitemapXML extends Jojo_Plugin
{

    function ping($engine='google')
    {
        /* ping all engines (currently only Google) */
        if ($engine == 'ping') {
            $engine = 'google';
        }
        if (strtolower($engine) == 'google') {
            $url = 'http://www.google.com/webmasters/sitemaps/ping?sitemap='.urlencode(_SITEURL.'/sitemap.xml'); //hardcoded to sitemap.xml
            foreach (Jojo::listPlugins('external/snoopy/Snoopy.class.php') as $pluginfile) {
                require_once($pluginfile);
                break;
            }
            $snoopy = new Snoopy;
            $snoopy->fetch($url);
            /* a 200 response means the ping was successful. Any other response is a failure. */
            if (strpos($snoopy->response_code, '200') !== false) return true;
        }
        return false;
    }

    function _getContent()
    {
        global $smarty;

        /* handle pings */
        $ping = Jojo::getFormData('ping', false);
        if ($ping) {
            if (Jojo_Plugin_Jojo_SitemapXML::ping($ping)) {
                echo 'Ping successful';
            } else {
                echo 'Ping failed';
            }
            exit();
        }

        /*
         * Sitemap array
         *
         * Array of pages.
         *   Key is the page url.
         *   Value is an array with url, lastmod, changefreq, priority
         */
        $sitemap = array();

        /* Apply filter to allow other plugins to alter the sitemap */
        $sitemap = Jojo::applyFilter('jojo_xml_sitemap', $sitemap);

        /* Assign the sitemap array to smarty */
        $smarty->assign('sitemap', $sitemap);

        /* Fetch the xml and output it */
        header('Content-type: application/xml');
        $smarty->display('xml_sitemap.tpl');
        exit();
    }

    function getCorrectUrl()
    {

        /* handle pings */
        $ping = Jojo::getFormData('ping', false);
        if ($ping) {
            return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }

        /* Act like a file, not a folder */
        $url = rtrim(parent::getCorrectUrl(), '/');
        return $url;
    }
}