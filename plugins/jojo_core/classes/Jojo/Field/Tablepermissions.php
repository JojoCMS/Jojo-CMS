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

class Jojo_Field_tablepermissions extends Jojo_Field
{
    /*
    var $_permOptions = array(
                            'show' => "Show",
                            'view' => "View",
                            'edit' => "Edit",
                            'add' => "Add",
                            'delete' => "Delete",
                            'editown' => "Edit Own",
                            'deleteown' => "Delete Own",
                            'showown' => "Show Own",
                            'createchild' => "Create Child",
                            'editpermissions' => "Edit Perms"
                            );
    */
    var $_permOptions = array(
                            'show' => "Show",
                            'view' => "View",
                            'edit' => "Edit",
                            'add' => "Add",
                            'delete' => "Delete"
                            );


    function displayedit()
    {
        /* Get group names */
        $groups = array('everyone' => 'All visitors');
        $res = Jojo::selectQuery("SELECT * FROM {usergroups} ORDER BY gr_name");
        foreach($res as $g) {
            $groups[$g['groupid']] = $g['gr_name'];
        }

        /* Display values for this table */
        global $smarty;
        $smarty->assign('perms', $this->_getPerms());
        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('readonly', $this->readonly);
        $smarty->assign('groups', $groups);
        $smarty->assign('_permOptions', $this->_permOptions);

        return $smarty->fetch('admin/fields/tablepermissions.tpl');
    }

    function checkvalue()
    {
        /* Get group names */
        $groups = array('everyone' => 'All visitors');
        $res = Jojo::selectQuery("SELECT * FROM {usergroups} ORDER BY gr_name");
        foreach($res as $g) {
          $groups[$g['groupid']] = $g['gr_name'];
        }

        /* Change from an array to a text string of permissions */
        $value = "";
        foreach($groups as $group => $groupname) {
            foreach($this->_permOptions as $perm => $name) {
                $permName = $group . '.' . $perm;
                if (isset($this->value[$permName])) {
                    $value .= "$permName=1\n";
                } else {
                    $value .= "$permName=0\n";
                }
            }
        }

        /* Set the value to the string */
        $this->value = $value;

        return true;
    }

    function _getPerms()
    {
        $perms = array();
        preg_match_all("/([a-zA-Z]+)\.([a-zA-Z]+)[\s=]+([01])+/", $this->value, $parts);
        foreach($parts[0] as $k => $v) {
          $perms[$parts[1][$k]][$parts[2][$k]] = ($parts[3][$k] == 1);
        }
        return $perms;
    }
}