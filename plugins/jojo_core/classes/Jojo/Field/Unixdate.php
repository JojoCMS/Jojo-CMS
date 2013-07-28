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

class Jojo_Field_unixdate extends Jojo_Field
{
    var $fd_size;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->fd_size = 20;
    }

    function checkvalue()
    {
        if ( ($this->fd_required == 'yes') and ($this->isblank()) ) {
            $this->error = 'Required field';
        }
        if (empty($this->error)) return true;
        return false;
    }

    function displayedit()
    {
        global $smarty;

        //template uses Any+Time: documentation at http://www.ama3.com/anytime/
        $formatteddate = ($this->value != 0) ? strftime("%Y-%m-%d %H:%M", $this->value) : '';
        $smarty->assign('formatteddate', $formatteddate);

        $printabledate = ($this->value != 0) ? strftime("%c", $this->value) : '';
        $smarty->assign('printabledate', $printabledate);

        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('error',    $this->error);
        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('fd_help',  htmlentities($this->fd_help));

        return $smarty->fetch('admin/fields/unixdate.tpl');
    }

    function displayView()
    {
        return ($this->value != 0) ? strftime("%c", $this->value) : '';
    }

    function displayJs()
    {
        global $smarty;
        return $smarty->fetch('admin/fields/unixdate_js.tpl');
    }

    function setValue($newvalue)
    {
        if (!empty($newvalue) && ($newvalue != 'n/a' || $newvalue != 'na') && strtotime($newvalue)) {
            $this->value = strtotime($newvalue);
        } elseif ($this->fd_default=='now') {
            $this->value = time();
        } else {
            $this->value = 0;
        }
        return true;
    }
}