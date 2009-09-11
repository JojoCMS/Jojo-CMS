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

class Jojo_Field_checkbox extends Jojo_Field
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

        /* Split the options up */
        $this->options = explode("\n", trim($this->fd_options));

        /* True option */
        $optionarray = explode(":", trim($this->options[0]));
        $smarty->assign('trueoption',  $optionarray[0]);
        if (isset($optionarray[2])) {
            $smarty->assign('show_on_true', explode(',', $optionarray[2]));
        }

        /* False option */
        $optionarray = explode(":", trim($this->options[1]));
        $smarty->assign('falseoption', $optionarray[0]);
        if (isset($optionarray[2])) {
            $smarty->assign('show_on_false', explode(',', $optionarray[2]));
        }

        $smarty->assign('readonly',    $this->readonly);
        $smarty->assign('fd_help',     htmlentities($this->fd_help));
        $smarty->assign('value',       $this->value);
        $smarty->assign('fd_field',    $this->fd_field);

        return  $smarty->fetch('admin/fields/checkbox.tpl');
    }

    function getHiddenFields()
    {
        $hiddenfields = array();
        $options = explode("\n", $this->fd_options);
        foreach ($options as $option) {
            $option2     = str_replace("\r", '', $option);
            $optionarray = explode(':', $option2);
            if (!empty($optionarray[2]) && ($this->value != $optionarray[0])) {
                $arr = explode('.', $optionarray[2]);
                foreach ($arr as $hiddenfield) {
                    $hiddenfields[] = $hiddenfield;
                }
            }
        }
        return $hiddenfields;
    }
}