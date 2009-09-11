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

class Jojo_Field_permissions extends Jojo_Field
{
    var $_table;

    var $_permOptions = array(
                            'show' => "Show",
                            'view' => "View",
                            'edit' => "Edit",
                            'add' => "Add",
                            'delete' => "Delete"
                            );

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
    }

    function displayedit()
    {
        global $smarty;

        /* Get group names */
        $groups = array('everyone' => 'All visitors');
        $res = Jojo::selectQuery("SELECT * FROM {usergroups} ORDER BY gr_name");
        foreach($res as $g) {
          $groups[$g['groupid']] = $g['gr_name'];
        }

        /* joined permissions table */
        /* Display values for this record */
        $smarty->assign('perms',  $this->_getThisPerms());
        $smarty->assign('defaultperms', $this->_getInheritedPerms());

        /* Output permissions for each group */
        if ($this->readonly != 'yes') {
            foreach($groups as $group => $groupname) {
                foreach($this->_permOptions as $perm => $name) {
                    $permName = $group . '.' . $perm;
                    $smarty->assign('permName',$permName);

                    if (isset($defaultperms[$group]) && isset($defaultperms[$group][$perm])) {
                        $smarty->assign('defaultperms_group_perm',isset($defaultperms[$group][$perm]));
                    }

                    if (isset($perms[$group]) && isset($perms[$group][$perm])) {
                        $smarty->assign('perms_group_perm',isset($perms[$group][$perm]));
                    }
                }
            }
        }
        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('readonly', $this->readonly);
        $smarty->assign('groups', $groups);
        $smarty->assign('_permOptions', $this->_permOptions);
        $smarty->assign('defaultperms_group', isset($defaultperms[$group]));
        $smarty->assign('perms_group', isset($perms[$group]));

        return  $smarty->fetch('admin/fields/permissions.tpl');
    }

    function displayview()
    {
        global $smarty;

        /* Get group names */
        $groups = array('everyone' => 'All visitors');
        $res = Jojo::selectQuery("SELECT * FROM {usergroups} ORDER BY gr_name");
        foreach($res as $g) {
          $groups[$g['groupid']] = $g['gr_name'];
        }

        /* joined permissions table */
        /* Display values for this record */
        $smarty->assign('perms',  $this->_getThisPerms());
        $smarty->assign('defaultperms', $this->_getInheritedPerms());

        /* Output permissions for each group */
        if ($this->readonly != 'yes') {
            foreach($groups as $group => $groupname) {
                foreach($this->_permOptions as $perm => $name) {
                    $permName = $group . '.' . $perm;
                    $smarty->assign('permName',$permName);

                    if (isset($defaultperms[$group]) && isset($defaultperms[$group][$perm])) {
                        $smarty->assign('defaultperms_group_perm',isset($defaultperms[$group][$perm]));
                    }

                    if (isset($perms[$group]) && isset($perms[$group][$perm])) {
                        $smarty->assign('perms_group_perm',isset($perms[$group][$perm]));
                    }
                }
            }
        }
        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('readonly', $this->readonly);
        $smarty->assign('groups', $groups);
        $smarty->assign('_permOptions', $this->_permOptions);
        $smarty->assign('defaultperms_group', isset($defaultperms[$group]));
        $smarty->assign('perms_group', isset($perms[$group]));

        return  $smarty->fetch('admin/fields/permissions-view.tpl');
    }

    function setValue($newvalue)
    {
        $this->value = $newvalue;
        $this->checkvalue();
        return true;
    }

    function checkvalue()
    {
        /* Change from an array to a text string of permissions */
        $value = "";
        if (is_array($this->value)) {
        foreach($this->value as $permName => $permValue) {
            if ($permValue == 'Y') {
                $value .= "$permName = 1\n";
            } elseif ($permValue == 'N') {
                $value .= "$permName = 0\n";
            }
        }
        } else {
            $permValue = $this->value;
            if ($permValue == 'Y') {
                $value .= "$permName = 1\n";
            } elseif ($permValue == 'N') {
                $value .= "$permName = 0\n";
            } else {
                $value = $this->value;
            }
        }

        /* Set the value to the string */
        $this->value = $value;

        return true;
    }

    /* Get the Permissions of this record */
    function _getThisPerms()
    {
        $perms = array();

        /* Split the text into different parts */
        preg_match_all("/([a-zA-Z0-9]+)\.([a-zA-Z]+)[\s=]+([01])+/", $this->value, $parts);

        /* Assemble the results */
        foreach($parts[0] as $k => $v) {
          $perms[$parts[1][$k]][$parts[2][$k]] = ($parts[3][$k] == 1);
        }

        /* Return the perms */
        return $perms;
    }

    /* Get the inherited permissions for this record, does not
       include the permissions of thie record */
    function _getInheritedPerms()
    {
        $perms = array();

        /* Get Permissions from parents */
        $record = $this->table->getRecordID();
        //echo 'record='.$record;
        while($record > 0) {
            $record = $this->_getParentID($record);
            $perms[$record] = $this->_getRecordPermissions($record);
        }

        /* Merge all permissions */
        // Start with table permissions
        $mergedPerms = $this->table->getDefaultPermissions();

        // Merge parent permissions in order from root to record
        $perms = array_reverse($perms);
        foreach ($perms as $perm) {
            $mergedPerms = $this->_mergePerms($mergedPerms, $perm);
        }

        /* Return merged permissions */
        return $mergedPerms;
    }

    /* Merge Permissions, with $second taking higher precidence */
    function _mergePerms($first, $second)
    {
        $merged = $first;
        foreach ($second as $group => $groupPerms) {
            foreach($groupPerms as $permName => $permValue) {
                $merged[$group][$permName] = $permValue;
            }
        }

        return $merged;
    }

    /* Get the permissions of a particular record */
    function _getRecordPermissions($record)
    {
        /* Create and execute query */
        $query = sprintf("SELECT %s AS perms FROM {%s} WHERE %s = '%s';",
                        $this->fd_field,
                        $this->fd_table,
                        $this->table->getOption('primarykey'),
                        $record);
        $res = Jojo::selectQuery($query);

        /* Split the result into different parts */
        preg_match_all("/([a-zA-Z0-9]+)\.([a-zA-Z]+)[\s=]+([01])+/", isset($res[0]['perms']) ? $res[0]['perms'] : '', $parts);

        /* Assemble the results */
        $perms = array();
        foreach($parts[0] as $k => $v) {
          $perms[$parts[1][$k]][$parts[2][$k]] = ($parts[3][$k] == 1);
        }

        /* Return the perms */
        return $perms;
    }

    /* Get the parent of a particular record */
    function _getParentID($record)
    {
        /* Check if this table has parents */
        if ($this->table->getOption('parentfield') == "") {
            return 0;
        }

        $query = sprintf("SELECT %s AS parent FROM {%s} WHERE %s = '%s';",
                        $this->table->getOption('parentfield'),
                        $this->fd_table,
                        $this->table->getOption('primarykey'),
                        $record);
        $res = Jojo::selectQuery($query);
        return ($res[0]['parent']);
    }
}