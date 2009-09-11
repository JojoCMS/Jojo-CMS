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
 * @package jojo_article
 */

class Jojo_Plugin_Jojo_Article_admin extends Jojo_Plugin
{
    static public function isArticleUrl($uri)
    {
        $prefix = false;
        $getvars = array();

        /* Check the suffix matches and extra the prefix */
        if (preg_match('#^(.+)/admin/([a-zA-Z0-9]{16})$#', $uri, $matches)) {
            $prefix = $matches[1];
            $getvars = array('code' => $matches[2]);
        } else {
            /* Didn't match */
            return false;
        }

        /* Check the prefix matches */
        if (Jojo_Plugin_Jojo_article::checkPrefix($prefix)) {
            /* The prefix is good, pass through uri parts */
            foreach($getvars as $k => $v) {
                $_GET[$k] = $v;
            }
            return true;
        }
        return false;
    }

    function _getContent()
    {
        global $smarty, $_USERGROUPS;
        $content = array();
        $messages = array();

        /* What's the code? */
        $urlParts = explode('/', _SITEURI);
        $code = $urlParts[count($urlParts) - 1];
        if (strlen($code) != 16 || !preg_match('%^([0-9]+)$%', $code)) {
            /* Should never get here as getCorrectUrl checks the code format */
            $content['content'] = 'Invalid code';
            return $content;
        }

        /* Approve, dofollow and use anchortextcomments that match the code */
        $useanchortext = Jojo::selectQuery("SELECT * FROM {articlecomment} WHERE ac_anchortextcode = ?", $code);
        Jojo::updateQuery("UPDATE {articlecomment} SET ac_nofollow = 'no', ac_useanchortext='yes' WHERE ac_anchortextcode = ?", $code);
        foreach ($useanchortext as $a) {
            $messages[] = "Updating comment by " . $a['ac_name'] . " to DOFOLLOW, and USE ANCHOR TEXT";
        }

        /* Approve and dofollow comments that match the code */
        $active = Jojo::selectQuery("SELECT * FROM {articlecomment} WHERE ac_approvecode = ?", $code);
        Jojo::updateQuery("UPDATE {articlecomment} SET ac_nofollow = 'no' WHERE ac_approvecode = ?", $code);
        foreach ($active as $a) {
            $messages[] = "Updating comment by " . $a['ac_name'] . " to DOFOLLOW";
        }

        /* Delete comments that match the code */
        $delete = Jojo::selectQuery("SELECT * FROM {articlecomment} WHERE ac_deletecode = ?", $code);
        Jojo::deleteQuery("DELETE FROM {articlecomment} WHERE ac_deletecode = ?", $code);
        foreach ($delete as $d) {
           $messages[] = "Deleting  comment by " . $d['ac_name'];
        }

        /* Didn't find anything */
        if (!count($useanchortext) && !count($active) && !count($delete)) {
           $messages[] = "No matching comments were found. This comment may have already been deleted.";
        }

        /* Redirect to article index page which will show the messages */
        $content['content'] = implode('<br/>', $messages);
        return $content;
    }

    function getCorrectUrl()
    {
        /**
         * Url will be in the format:
         *   /articles/admin/1234567890123456/
         */

        /* What's in our url */
        $urlParts = explode('/', _SITEURI);
        $lastPart = $urlParts[count($urlParts) - 1];
        if (preg_match('%^([0-9]+)$%', $lastPart) && strlen($lastPart) == 16) {
            return _SITEURL . '/' . $this->getValue('pg_url') . '/' . $lastPart . '/';
        }
        return _SITEURL;
    }
}