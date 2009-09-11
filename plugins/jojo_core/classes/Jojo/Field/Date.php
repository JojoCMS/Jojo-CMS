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

class Jojo_Field_date extends Jojo_Field
{
    var $fd_size;
    var $error = '';

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->fd_size = 20;
    }

    function checkvalue()
    {
        /* a boolean false value indicates a bad format. Empty string means it hasn't been set. */
        if ($this->value === false) {
            $this->error = 'Invalid date format';
        } elseif (($this->fd_required == 'yes') && ($this->isblank())) {
            $this->error = 'Required field';
        }
        return empty($this->error) ? true : false;
    }

    function displayedit()
    {
        global $smarty;

        if (empty($this->value) || $this->value == 'NOW()' || ($this->value == '0000-00-00')) {
            $formatted = ($this->fd_default=='NOW()') ? date('j M Y'):'';
        } else {
            /* If this seems a bit complex, I'm trying to format the date without using a uniz timestamp, which truncates to the unix epoch on Windows */
            $date = date_parse($this->value);
            $formatted = ltrim($date['day'], '0').' '.date('M', strtotime($date['month'].'/13/2009')).' '.$date['year'];
        }

        //TODO: Add handlers so that initial date is the same format as the Javascript, and Today, tomorrow, yesterday are handled
        $smarty->assign('error',         $this->error);
        $smarty->assign('value',         $this->value);
        $smarty->assign('fd_field',      $this->fd_field);
        $smarty->assign('fd_units',      $this->fd_units);
        $smarty->assign('fd_size',       $this->fd_size);
        $smarty->assign('fd_help',       htmlentities($this->fd_help));
        $smarty->assign('value',         $this->value);
        $smarty->assign('readonly',      $this->readonly);
        $smarty->assign('formatteddate', $formatted);
        $smarty->assign('mysql2date',    Jojo::mysql2date($this->value, 'medium'));

        return  $smarty->fetch('admin/fields/date.tpl');
    }

    function displayView()
    {
        global $smarty;

        if (empty($this->value) || $this->value == 'NOW()' || ($this->value == '0000-00-00')) {
            return ($this->fd_default=='NOW()') ? date('j M Y'):'';
        } else {
            $date = date_parse($this->value);
            return ltrim($date['day'], '0').' '.date('M', strtotime($date['month'].'/13/2009')).' '.$date['year'];
        }
    }

    function displayJs()
    {
        global $smarty;
        return  $smarty->fetch('admin/fields/date_js.tpl');
    }

    function setvalue($newvalue)
    {
        if (empty($newvalue) && ($this->fd_default == 'NOW()')) {
            $this->value = date('Y-m-d');
            return true;
        } elseif (empty($newvalue)) {
            $this->value = '';
            return true;
        }
        $this->value = self::date2mysql($newvalue);
        return true;
    }

    /////////////////////////DATE2MYSQL////////////////////////////////////////////
    //Converts any textual date to MySQL format
    //TODO: Check that date is valid and return errors if not
    static function date2mysql($date)
    {
        $timestamp = Jojo::strToTimeUK($date);
        if (!$timestamp) return false;
        return ($timestamp) ? date('Y-m-d', $timestamp) : '';
    }
}