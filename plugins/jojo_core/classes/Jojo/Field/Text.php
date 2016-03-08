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

class Jojo_Field_text extends Jojo_Field
{
    var $fd_size;
    var $counter = 0;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        if (empty($this->fd_size)) {
            $this->fd_size = 20;
        }
    }

    /*
     * Check the value of this field
     */
    public function checkvalue()
    {
        /* Check the value is not blank if required */
        if (($this->fd_required == 'yes') && ($this->isblank())) {
            $this->error = 'Required field';
        }

        return ($this->error == '');
    }

    /*
     * Return the html for editing this field
     */
    public function displayedit()
    {
        global $smarty;

        /* Create a counter if this field has a length specified */
        switch ($this->fd_options) {
            case 'seotitle':
                // Google allows 70 characters - this includes the branding text we use at the end of every page title
                $this->counter = 70;
                $this->counter = $this->counter - strlen(' | ' . _SITETITLE);
                break;

            case 'rollover':
                $this->counter = 70;
                break;

            default:
                $this->counter = (is_numeric($this->fd_options)) ? intval($this->fd_options) : 0;
                break;
        }

        $smarty->assign('counter',       $this->counter);
        $smarty->assign('fd_field',      $this->fd_field);
        $smarty->assign('fd_size',       $this->fd_size);
        $smarty->assign('value',         htmlentities($this->value, ENT_COMPAT, 'UTF-8'));
        $smarty->assign('maxsize',       $this->fd_maxsize);
        $smarty->assign('readonly',      $this->fd_readonly);
        $smarty->assign('required',      $this->fd_required);
        $smarty->assign('fd_units',      $this->fd_units);
        $smarty->assign('fd_help',       htmlentities($this->fd_help));
        $smarty->assign('counterstrlen', $this->counter - strlen($this->value));
        return $smarty->fetch('admin/fields/text.tpl');
    }
}