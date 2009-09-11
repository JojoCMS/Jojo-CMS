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

class Jojo_Field_decimal extends Jojo_Field
{
    var $size;
    var $minvalue;
    var $maxvalue;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->size     = 6;
        $this->minvalue = ''; //'' means no minimum
        $this->maxvalue = ''; //'' means no maximum
        $this->units    = ''; //Define units where values are used
    }

    function displayedit()
    {
        global $smarty;

        $smarty->assign('value',       $this->value);
        $smarty->assign('fd_field',    $this->fd_field);
        $smarty->assign('fd_type',     $this->fd_type);
        $smarty->assign('fd_field',    $this->fd_field);
        $smarty->assign('size',        $this->size);
        $smarty->assign('fd_help',     htmlentities($this->fd_help));
        $smarty->assign('value',       $this->value);
        $smarty->assign('fd_help',     $this->fd_help);
        $smarty->assign('readonly',    $this->readonly);
        $smarty->assign('onlyIfUnits', Jojo::onlyIf($this->units, ' '.$this->units));

        return  $smarty->fetch('admin/fields/decimal.tpl');
    }
}