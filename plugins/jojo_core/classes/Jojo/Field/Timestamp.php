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

class Jojo_Field_timestamp extends Jojo_Field
{
    function displayEdit()
    {
        global $smarty;
        if (!empty($this->value) && ($this->value != 'CURRENT_TIMESTAMP')) {
            $smarty->assign('formatteddate', date('d M Y h:ia',strtotime($this->value)));
        } else {
            $smarty->assign('formatteddate', '');
        }
        $smarty->assign('fd_field', $this->fd_field);
        return  $smarty->fetch('admin/fields/timestamp.tpl');
    }

    function displayView()
    {
        if (!empty($this->value) && ($this->value != 'CURRENT_TIMESTAMP')) {
            return date('d M Y h:ia',strtotime($this->value));
        } else {
            return $this->fd_field;
        }
    }

    function setValue($newvalue)
    {
        $this->value = date('Y-m-d H:i:s', time());
        return true;
    }
}