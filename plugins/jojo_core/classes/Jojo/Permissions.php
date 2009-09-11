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

class Jojo_Permissions {
    var $_table;
    var $_perms;

    var $_recordPerms = array();
    var $_cachedPerms = array();

    function getPermissions($table, $record = 0)
    {
        if (!isset($this->_cachedPerms[$table]) || !isset($this->_cachedPerms[$table][$record])) {
            /* Create table object */
            $this->_table = &Jojo_Table::singleton($table);

            /* Check if this table has record permissions */
            $perms = array();
            $permsField = $this->_table->getPermsField();

            if ($permsField) {
                /* Get Permissions from parents */
                while($record > 0) {
                    $perms[$record] = $this->_getRecordPermissions($record);
                    $record = $this->_getParentID($record);
                }
            }

            /* Merge all permissions */
            $mergedPerms = $this->_table->getDefaultPermissions();

            /* Merge parent permissions in order from root to record */
            $perms = array_reverse($perms);
            foreach ($perms as $perm) {
                $mergedPerms = $this->_mergePerms($mergedPerms, $perm);
            }

            /* Return merged permissions */
            $this->_cachedPerms[$table][$record] = $mergedPerms;
        }

        $this->_perms = $this->_cachedPerms[$table][$record];

        return $this->_perms;
    }

    /* Has as particular user got a particular permission */
    function hasPerm($group, $action)
    {
        /* If multiple groups are specified check all of them */
        if (is_array($group)) {
            foreach($group as $u) {
                if ($this->hasPerm($u, $action)) {
                    return true;
                }
            }
            return false;
        }

        /* Check group exists */
        if (!isset($this->_perms[$group])) {
            return false;
        }

        /* Check permission exists */
        if (!isset($this->_perms[$group][$action])) {
            return false;
        }

        /* Return permission value */
        return $this->_perms[$group][$action];
    }

    /* Merge Permissions, with $second taking higher precidence */
    function _mergePerms($first, $second)
    {
        $merged = $first;
        if (is_array($second)) {
            foreach ($second as $group => $groupPerms) {
                foreach($groupPerms as $permName => $permValue) {
                    $merged[$group][$permName] = $permValue;
                }
            }
        }

        return $merged;
    }

    /* Get the permissions of a particular record */
    function _getRecordPermissions($record)
    {
        static $cache = array();

        /* See if we have the table cached yet */
        $tablename = $this->_table->getTableName();
        if (!isset($cache[$tablename])) {
            /* Cache all the permissions from the table */
            $query = sprintf("SELECT  %s AS id, %s AS perms FROM {%s}",
                            $this->_table->getOption('primarykey'),
                            $this->_table->getPermsField(),
                            $tablename);
            $res = Jojo::selectQuery($query);
            foreach ($res as $row) {
                $cache[$tablename][$row['id']] = $row['perms'];
            }
        }

        /* See if we have the permissions cached yet */
        if (isset($cache[$tablename][$record]) && !is_array($cache[$tablename][$record])) {
            /* Not cached, have we got the table cached yet? */

            /* Split the result into different parts */
            preg_match_all("/([a-zA-Z0-9]+)\.([a-zA-Z]+)[\s=]+([01])+/", $cache[$tablename][$record], $parts);

            /* Assemble the results */
            $perms = array();
            foreach($parts[0] as $k => $v) {
                $perms[$parts[1][$k]][$parts[2][$k]] = ($parts[3][$k] == 1);
            }

            /* Save the permissions in the cache */
            $cache[$tablename][$record] = $perms;
        }

        /* Return the perms */
        return isset($cache[$tablename][$record]) ? $cache[$tablename][$record] : array();
    }

    /* Get the parent of a particular record */
    function _getParentID($record)
    {
        /* Check if this table has parents */
        if ($this->_table->getOption('parentfield') == '') {
            return 0;
        }

        static $cache;

        if (!isset($cache[$this->_table->getTableName()])) {
            /* Create and execute query */
            $query = sprintf("SELECT  %s AS id, %s AS parent FROM {%s}",
                            $this->_table->getOption('primarykey'),
                            $this->_table->getOption('parentfield'),
                            $this->_table->getTableName());
            $res = Jojo::selectQuery($query);
            foreach ($res as $row) {
                $cache[$this->_table->getTableName()][$row['id']] = $row['parent'];
            }
        }

        return isset($cache[$this->_table->getTableName()][$record]) ? $cache[$this->_table->getTableName()][$record] : 0;

    }
}