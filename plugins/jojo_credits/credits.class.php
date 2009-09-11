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
 * @package jojo_credits
 *
 * Displays a static link to the web developer at the bottom of every page on a site. The link is static,
 * but is randomly determined from the URL. This is an alternative to a sitewide link to your
 * homepage at the bottom of a site. Much better to have different links to sub pages of your site
 * which will give a more natural spread of links.
 *
 * Because Google likes static links, this technique is much better than a randomised link. See the
 * article for details... http://www.ragepank.com/articles/31/optimizing-webmaster-credits/
 *
 * This file may be used and distributed subject to the LGPL available from http://www.fsf.org/copyleft/lgpl.html
 * If you find this class useful, I would appreciate a link back to http://www.ragepank.com
 * or simply an email to let me know you like it :)
 *
 * This class is distributed as is, there are no implied guarantees that the code works, or won't
 * crash your server / burn your house down / cause you to put on 10kg.
 *
 * You are free to improve or modify this script - if you do so, I would appreciate a copy too :)
 * Email: info at (the domain mentioned above) dot com
 *

 Class Usage
 ===========

 //Define Credits to use (No more than 16)
 $credits_array = array();
 $credits_array[] = '<a href="http://www.domain.com">Web Design</a> by YourName'; //This is the default if you define less than 16
 $credits_array[] = '<a href="http://www.domain.com">Website</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com">Web Development</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/web-design.htm">Website Design</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/web-design.htm">Website</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/web-development.htm">Web Development</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/web-development.htm">PHP Web Development</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/web-hosting.htm">Web Hosting</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/web-hosting.htm">Hosting</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/web-hosting.htm">Website Hosting</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/seo.htm">Search Engine Optimization</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/seo.htm">SEO</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/seo.htm">Website Optimization</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/graphic-design.htm">Graphic Design</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/graphic-design.htm">Graphics</a> by YourName';
 $credits_array[] = '<a href="http://www.domain.com/graphic-design.htm">Design</a> by YourName';

 //Change this line to reflect the location of this class
 require_once('credits.class.php');

 //Create a new instance of the class - use the array as the argument
 $credits = new credits($credits_array);

 //Display the link (include in the footer of your page)
 echo $credits->getCredits();

 * @version 1
 * @copyright 2006 Harvey Kane
 * @author Harvey Kane - info at (the domain mentioned above) dot com
 *
 **/

class credits
{
    /* Private variables - do not modify */
    var $_credits = array();
    var $_nofollow;
    var $_default;

    function credits($input = array(), $nofollow=false)
    {
        $this->_nofollow = $nofollow;
        if (isset($input[0])) {
            $this->_default = $input[0];
        } else {
            return false;
        }
        $n = count($input);
        for ($i = 0; $i < $n; $i++) {
            $this->_credits[$i] = trim($input[$i]);
        }
        return true;
    }

    /* adds a nofollow to a link */
    function addNofollow($text)
    {
        $text = preg_replace('/<\\s*a([^>]*)\\s*rel\\s*=\\s*["\']?nofollow["\']?\\s*([^>]*)>/i', '<a$1$2>', $text);
        $text = preg_replace('/<\\s*a([^>]*)\\s*>/i', '<a$1 rel="nofollow">', $text);
        return $text;
    }

    function getCredits($s='', $nofollow=false)
    {
        if ($s == '') {
            $s = $_SERVER['REQUEST_URI'];
        }

        $hash = md5($s);
        $key = hexdec($hash[0]);

        $link = isset($this->_credits[$key]) ? $this->_credits[$key] : $this->_default;
        return $nofollow ? $this->addNofollow($link) : $link;
    }
}