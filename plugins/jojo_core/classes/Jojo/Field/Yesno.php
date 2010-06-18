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

class Jojo_Field_yesno extends Jojo_Field
{
    var $fd_size;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->options = array();
    }

    function displayedit()
    {
        global $smarty;

        $vals        = array(1,0);
        $displayvals = array('Yes','No');


        $smarty->assign('vals',        $vals);
        $smarty->assign('displayvals', $displayvals);
        $smarty->assign('fd_help',     htmlentities($this->fd_help));
        $smarty->assign('readonly',    $this->readonly);
        $smarty->assign('value',       $this->value);
        $smarty->assign('fd_field',    $this->fd_field);

        return  $smarty->fetch('admin/fields/radio.tpl');
    }

    function getHiddenFields()
    {
        return $hiddenfields;
    }

    function displayJs()
    {
        global $smarty;
        return $smarty->fetch('admin/fields/radio_js.tpl');
    }
}