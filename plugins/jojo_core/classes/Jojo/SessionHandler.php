<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Harvey Kane <code@ragepank.com>
 * Copyright 2008 Michael Cochrane <mikec@jojocms.org>
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

class Jojo_SessionHandler {

    public static function close()
    {
        return true;
    }

    public static function open($save_path, $session_name)
    {
        return true;
    }

    public static function read($id)
    {
        /* Session timeout, don't rely on garbage collection. */
        $timeout = time() - ini_get('session.gc_maxlifetime');

        $query = 'SELECT session_data FROM {sessiondata} WHERE session_id = ? AND session_lastmodified > ?';
        $values = array($id, $timeout);

        $result = Jojo::selectQuery($query, $values);
        if (!$result) {
            //Jojo::log('Error retrieving session data (id = ' . $id . ')', __FILE__, __LINE__, PEAR_LOG_ERR);
            return '';
        }

        return $result[0]['session_data'];
    }

    public static function write($id, $session_data)
    {
        /* Don't save on read only sessions */
        if (defined('_READONLYSESSION')) {
            return true;
        }

        /* Build the SQL query. */
        $query = 'REPLACE INTO {sessiondata} (session_id, session_data, session_lastmodified) VALUES (?, ?, ?)';
        $values = array($id, $session_data, time());
        $result = Jojo::updateQuery($query, $values);
        return true;
    }

    public static function destroy($id)
    {
        /* Build the SQL query. */
        $query = 'DELETE FROM {sessiondata} WHERE session_id = ?';
        $values = array($id);

        /* Execute the query. */
        $result = Jojo::deleteQuery($query, $values);
        return true;
    }

    public static function gc($maxlifetime = 300)
    {
        /* Build the SQL query. */
        $query = 'DELETE FROM {sessiondata} WHERE session_lastmodified < ?';
        $values = array((int)(time() - $maxlifetime));

        /* Execute the query. */
        $result = Jojo::deleteQuery($query, $values);
        return $result;
    }
}