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
 * @package jojo_google_verification
 */

class Jojo_Plugin_jojo_google_verification extends Jojo_Plugin
{
    function _getContent()
    {
        /* valid Google verification filenames are stored in Jojo::getOption('googleverification'), one valid filename per line. Valid files will return a 200 response, invalid return a 404. Alternatively, you can still upload the empy verification file onto the server. */
        $filename = $_GET['filename'];
        $valid = explode("\n", Jojo::getOption('googleverification'));
        foreach ($valid as $file) {
            if (trim($file) == $filename) {
                echo 'google-site-verification: '.$filename;
                exit();
            }
        }
        header("HTTP/1.0 404 Not Found");
        echo '<h1>Invalid Verification file</h1><strong>'.$filename.'</strong> is not a valid Google verification file.<br />You can add '.$filename.' to the list of valid verification files under edit options in the Jojo CMS admin section.';
        exit();
    }

    function getCorrectUrl()
    {
        //Assume the URL is correct
        return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}