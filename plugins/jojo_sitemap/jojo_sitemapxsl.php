<?php

class Jojo_Plugin_Jojo_SitemapXSL extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty;
        /* Fetch the xml and output it */
        header('Content-type: application/xml');
        $smarty->display('google_sitemap_style.tpl');
        exit();
    }

    function getCorrectUrl()
    {

        /* Act like a file, not a folder */
        $url = rtrim(parent::getCorrectUrl(), '/');
        return $url;
    }
}