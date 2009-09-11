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

class Jojo_Eventlog
{
    var $eventlogid;
    var $datetime;
    var $code;
    var $shortdesc;
    var $desc;
    var $importance;
    var $userid;
    var $username;
    var $ip;
    var $uri;
    var $referer;
    var $browser;

    public function __construct()
    {
        global $_USERID;
        $this->importance = 'normal';
        $this->userid = $_USERID ? $_USERID : 0;
        $this->username = '';
        $this->ip = Jojo::getIp();
        $this->uri = defined('_FULLSITEURI') ? _FULLSITEURI : ''; //Todo: default this to something better
        $this->referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $this->browser = Jojo::getBrowser();
    }

    public function savetodb()
    {
        if (Jojo::tableexists('eventlog')) {
            $query = 'INSERT INTO {eventlog} SET
                                el_datetime = NOW(), el_code = ?, el_shortdesc = ?,
                                el_desc = ?, el_importance = ?, el_userid = ?,
                                el_username = ?, el_ip = ?, el_uri = ?,
                                el_referer = ?, el_browser = ?';
            Jojo::insertQuery($query, array(
                                  $this->code,
                                  $this->shortdesc,
                                  $this->desc,
                                  $this->importance,
                                  $this->userid,
                                  $this->username,
                                  $this->ip,
                                  $this->uri,
                                  $this->referer,
                                  $this->browser));
        }

        /* send an immediate email to the webmaster for critical errors */
        if ($this->importance == 'critical') {
            $message = "This is an automated message from "._SITEURL."\n\n";
            $message .= "An error has occurred that is considered important, and the webmaster is alerted of these errors immediately.\n\n";
            $message .= "Date / Time: ".date('l dS \of F Y h:i:s A')."\n";
            if (Jojo::getIp()) $message .= "IP: ".Jojo::getIp()."\n";
            if ($_SERVER['REQUEST_URI']) $message .= "URI: ".$_SERVER['REQUEST_URI']."\n";
            if (!empty($_SERVER['HTTP_REFERER'])) $message .= "Referer: ".$_SERVER['HTTP_REFERER']."\n";
            if (Jojo::getBrowser()) $message .= "Browser: ".Jojo::getBrowser()."\n\n";
            $message .= "Error: \n\n";
            $message .= $this->desc;
            $message .= "\n\n________________________________________\n\n";
            $subject = 'Critical error: '. Jojo::either($this->shortdesc,$this->code);
            Jojo::simplemail(_WEBMASTERNAME, _WEBMASTERADDRESS, $subject, $message);
        }

        return true;
    }
}