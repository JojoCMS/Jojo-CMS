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
 * @author  Michael Brandon <michael@searchmasters.co.nz>
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_bing_verification
 */

class Jojo_Plugin_jojo_bing_verification extends Jojo_Plugin
{
    function _getContent()
    {
        /* valid Bing verification xml file entries (different users) are stored in Jojo::getOption('bingverification'), one valid xml entry per line. Alternatively, you can still upload the xml file onto the server. */
        $lines = explode("\n", Jojo::getOption('bingverification'));
        echo
'<?xml version="1.0"?>
<users>
';
        foreach ($lines as $line) {
                echo '<user>'.$line.'</user>
';
            }
        echo '</users>';
        exit();
    }

    function getCorrectUrl()
    {
        //Assume the URL is correct
        return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}