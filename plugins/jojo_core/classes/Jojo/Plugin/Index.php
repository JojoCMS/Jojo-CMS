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

class Jojo_Plugin_Index extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty;
        $smarty->assign('content', $this->page['pg_body']);
        $pluginfiles = Jojo::listPlugins('templates/index-inner.tpl');
        $content = array();
        if (count($pluginfiles)) {
            $content['content'] = $smarty->fetch($pluginfiles[0]);
        } else {
            $content['content'] = $this->page['pg_body'];
        }
        return $content;
    }

    function getCorrectUrl()
    {
        $url = parent::getCorrectUrl();
        if (substr($url, -1, 1) != '/') {
              $url .= '/';
        }
        /* allow URLs that have the Google Adwords / Yahoo tracking code */
        $allowed_vars = array('__utma', 'gclid=', 'gad=', 'OVKEY=', 'OVRAW=', 'OVMTC=');
        $allowed_vars = Jojo::applyFilter('index_allowed_vars', $allowed_vars); //Allow plugins to add additional safe strings here

        foreach ($allowed_vars as $var) {
            if (strpos($_SERVER['REQUEST_URI'], $var) !== false) {
                return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            }
        }

        //if ((strpos($_SERVER['REQUEST_URI'],'gclid=') !== false) || (strpos($_SERVER['REQUEST_URI'],'gad=') !== false) || (strpos($_SERVER['REQUEST_URI'],'OVKEY=') !== false) || (strpos($_SERVER['REQUEST_URI'],'OVRAW=') !== false) || (strpos($_SERVER['REQUEST_URI'],'OVMTC=') !== false)) return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        return $url;
    }
}