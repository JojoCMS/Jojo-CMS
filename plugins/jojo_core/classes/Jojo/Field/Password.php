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

class Jojo_Field_password extends Jojo_Field
{
    var $fd_size;
    var $error;
    var $readonly;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->fd_size = 30;
    }

    function setPostData($data = false)
    {
        $this->postdata = $data;
    }

    function checkvalue()
    {
        if ( ($this->fd_required == 'yes') && empty($this->postdata['orig']) && empty($this->postdata[1]) && empty($this->value) ) {
            $this->error = 'Required field';
        } elseif ($this->postdata[1] != $this->postdata[2]) {
            /* password and confirmation must match */
            $this->error = 'Password and confirmation must match';
        } elseif (false) {
            /* TODO: enforce password length policy of some sort */
            $this->error = 'Password must be at least 8 characters and contain at least 1 number';
        }

        return ($this->error == '');
    }

    function displayedit()
    {
        global $smarty;
        $this->fd_size = intval($this->fd_size / 2);

        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('required', $this->fd_required);
        $smarty->assign('fd_size', $this->fd_size);
        $smarty->assign('fd_help', htmlentities($this->fd_help));
        $smarty->assign('error', $this->error);
        $smarty->assign('value', $this->value);

        return  $smarty->fetch('admin/fields/password.tpl');
    }

    function setvalue($newvalue)
    {
        $this->value = $newvalue['orig'];
        /* Check the passwords match */
        if ($newvalue[1] == $newvalue[2]) {
            /* Check the password is not empty */
            if (!empty($newvalue[1])) {
                /* Hash the value, it's auto-salted */
                $this->value = Jojo_Auth_Local::hashPassword($newvalue[2]);
            }
            return true;
        } else {
            $this->error = 'Passwords did not match';
            return false;
        }
    }
}