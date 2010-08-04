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

class Jojo_Field_url extends Jojo_Field
{
    var $fd_size;
    var $brokenlinkmessage;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->fd_size = 50;
    }

    function checkvalue()
    {
        //Check if required field is empty
        if ( ($this->fd_required == 'yes') && ($this->isblank()) ) {
            $this->error = 'Required field';
        }
        //Check format of URL is valid, if it is, check the site exists and is live
        if (!$this->isblank() && !Jojo::checkUrlFormat($this->value)) {
            $this->error = 'The URL format is invalid';
        }

        return ($this->error == '');
    }

    function displayedit()
    {
        global $smarty;

        /*
        $urlcheck = '';
        if (!$this->isblank()) {
            if (!checkurl($this->value)) {
                $urlcheck = false;
            } else {
                $urlcheck = true;
            }
        }
        */

        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('readonly', $this->fd_readonly);
        //$smarty->assign('urlcheck', $urlcheck);
        $smarty->assign('fd_size',  $this->fd_size);
        $smarty->assign('value',    $this->value);
        $smarty->assign('fd_help',  htmlentities($this->fd_help));
        return  $smarty->fetch('admin/fields/url.tpl');
    }

    function displayview()
    {
        return isset($this->value) ? '<a href="'.$this->value.'" target="_BLANK" rel="nofollow">'.preg_replace('%^http://(.*)%i', '$1', $this->value).'</a>' : '';
    }

    function setvalue($newvalue) //overrides original definition
    {
        /* Ensures that http:// is added to the url if not already */
        if (!empty($newvalue)) {
            $this->value = Jojo::addHttp($newvalue);
        }
    }
}