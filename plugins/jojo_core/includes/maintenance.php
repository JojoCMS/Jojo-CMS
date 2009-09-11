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

/* delete low priority log entries older than 1 month */
$lastmonth = date('Y-m-d H:i:s', strtotime('-1 month'));
Jojo::deleteQuery("DELETE FROM {eventlog} WHERE (el_importance='very low' OR el_importance='low') AND el_datetime<'$lastmonth'");

/* delete old ADODB SQL log entries */
if (Jojo::tableExists('adodb_logsql')) {
    $lastmonth = date('Y-m-d H:i:s', strtotime('-1 month'));
    Jojo::deleteQuery("DELETE FROM {adodb_logsql} WHERE created<'$lastmonth'");
}

/* email webmaster about any 'high' priority eventlog entries in last day */
$notification = Jojo::getOption('eventlog_email_notification');
if ($notification == 'yes') {
    $yesterday = date('Y-m-d H:i:s', strtotime('-1 day'));
    $message = '';
    /* list high/critical entries */
    $data = Jojo::selectQuery("SELECT * FROM {eventlog} WHERE el_datetime>'$yesterday' AND (el_importance='high' OR el_importance='critical') AND el_code != '404' ORDER BY el_datetime DESC LIMIT 100"); //limit to 100 to keep emails reasonable - any more errors than that means the site needs serious looking at, which isn't the job of this notification
    $message_errors = '';
    foreach ($data as $entry) {
        $message_errors .= "== ".$entry['el_code']." ==\n".date('jS M, Y H:ia', strtotime($entry['el_datetime']))."\n".$entry['el_shortdesc']."\n". rtrim($entry['el_desc']) . "\nUrl:  " . _SITEURL . '/' . $entry['el_uri'] . "\n\n";
    }
    $message .= empty($message_errors) ? '' : "The following high priority events have been logged on "._SITETITLE." in the past 24 hours. This notification can be disabled from the Edit Options screen ("._SITEURL."/admin/options/).\n\n".$message_errors."\n\n";

    /* list 404 pages */
    $data = Jojo::selectQuery("SELECT *, COUNT(*) AS numerrors FROM {eventlog} WHERE el_datetime>'$yesterday' AND el_code='404' GROUP BY el_uri ORDER BY numerrors, el_uri DESC LIMIT 1000");
    $message_404s = '';
    foreach ($data as $entry) {
        $message_404s .= _SITEURL."/".$entry['el_uri']." (".$entry['numerrors']." requests)\n";
    }
    $message .= empty($message_404s) ? '' : "The following pages have returned a 404 error on "._SITETITLE." in the past 24 hours. This notification can be disabled from the Edit Options screen ("._SITEURL."/admin/options/).\n\n".$message_404s."\n\n";

    if (!empty($message)) {
        $subject = 'Event log '.date('jS M, Y H:ia');
        Jojo::simpleMail(_WEBMASTERNAME, _WEBMASTERADDRESS, $subject, $message);
    }
}