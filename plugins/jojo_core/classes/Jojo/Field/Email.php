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

class Jojo_Field_email extends Jojo_Field
{
    var $fd_size;
    var $brokenlinkmessage;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->fd_size = 40;
    }
    
    function setPostData($data = false)
    {
        $this->postdata = $data;
    }

    function checkvalue()
    {
       
        $confirm = (strpos($this->fd_options, 'confirm') !== false) ? true : false;
        if ($confirm && !empty($this->postdata[1]) && ($this->postdata[1] != $this->postdata[2])) {
            /* email and confirmation must match */
            $this->error = 'Email and confirmation must match';
        } elseif ( ($this->fd_required == "yes") and ($this->isblank()) ) {
            /* Check if required field is empty */
            $this->error = "Required field";
        }
        
        /* Check format of URL is valid, if it is, check the site exists and is live */
        if (!$this->isblank() && !Jojo::checkemailformat($this->value)) {
            $this->error = "The Email format is invalid";
        }
        
        /* TODO: Check the domain is active or registered */

        return ($this->error == '');
    }

    function displayedit()
    {
        global $smarty;

        $smarty->assign('fd_field', $this->fd_field);
        $confirm = (strpos($this->fd_options, 'confirm') !== false) ? true : false;
        $smarty->assign('confirm', $confirm);
        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('fd_size',  $this->fd_size);
        $smarty->assign('value',    $this->value);
        $smarty->assign('fd_help',  htmlentities($this->fd_help));

        return  $smarty->fetch('admin/fields/email.tpl');
    }
    
    function setValue($newvalue)
    {
        $this->value = $newvalue[1];
        $confirm = (strpos($this->fd_options, 'confirm') !== false) ? true : false;
        if ($confirm && ($newvalue[1] != $newvalue[2])) {
            $this->error = 'Email and confirmation must match';
            $this->value = '';
            return false;
        }
        return true;
    }
}