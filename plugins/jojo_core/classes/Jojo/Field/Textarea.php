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

class Jojo_Field_textarea extends Jojo_Field
{
    var $rows;
    var $cols;
    var $counter = 0;
    var $texttype;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->rows = 5;
        $this->cols = 50;
        if (!empty($fielddata['fd_rows'])) $this->rows = $fielddata['fd_rows'];
        if (!empty($fielddata['fd_cols'])) $this->cols = $fielddata['fd_cols'];
    }

    function displayedit()
    {
        global $smarty;

        if ($this->texttype == 'metadescription') {
          $this->counter = 155;
        } elseif ($this->fd_options == 'metadescription') {
          $this->counter = 155;
        } elseif (is_numeric($this->fd_options)) {
          $this->counter = $this->fd_options;
        } else {
          $this->counter = 0;
        }

        $smarty->assign('rows',          $this->rows);
        $smarty->assign('cols',          $this->cols);
        $smarty->assign('counterstrlen', $this->counter - strlen($this->value));
        $smarty->assign('value',         $this->value);
        $smarty->assign('counter',       $this->counter);
        $smarty->assign('fd_field',      $this->fd_field);
        $smarty->assign('readonly',      $this->readonly);
        $smarty->assign('fd_help',       htmlentities($this->fd_help));

        return  $smarty->fetch('admin/fields/textarea.tpl');
    }
}