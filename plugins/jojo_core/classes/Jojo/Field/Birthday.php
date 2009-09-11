<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2009 Harvey Kane <code@ragepank.com>
 * Copyright 2009 Michael Holt <code@gardyneholt.co.nz>

 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Field_birthday extends Jojo_Field_date
{
    function displayview()
    {
        /* Only Display the day / month, not the year */
        return date('jS F', strtotime($this->value));
    }
}