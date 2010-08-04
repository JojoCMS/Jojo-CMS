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

class Jojo_Field_encryptedtext extends Jojo_Field
{
    var $fd_size;
    var $error;
    var $readonly;
    var $counter = 0;
    var $texttype;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        if (empty($this->fd_size)) $this->fd_size = 20;
    }

    /*
     * Check the value of this field
     */
    function checkvalue()
    {
        /* Check the value is not blank if required */
        if (($this->fd_required == "yes") && ($this->isblank()) ) {
            $this->error = "Required field";
        }

        return ($this->error == "");
    }

    /*
     * Return the html for editing this field
     */
    function displayedit()
    {
        global $smarty;

        $key = $this->fd_options;

        /* Make sure a key has been set */
        if (empty($key)) {
            return 'This field cannot be used until an encryption key is set in fielddata options.';
        }

        /* decode the data into a string */
        require_once(_BASEPLUGINDIR . '/jojo_core/external/Horde/Cipher.php');
        require_once(_BASEPLUGINDIR . '/jojo_core/external/Horde/Cipher/blowfish.php');
        $cipher = &Horde_Cipher::factory('blowfish');
        $cipher->setBlockMode('ofb64');
        $cipher->setIV('jojojojo');
        $cipher->setKey($key);

        $displayvalue = $cipher->decrypt($this->value);

        $smarty->assign('fd_field', $this->fd_field);
        $smarty->assign('fd_size', $this->fd_size);
        $smarty->assign('value', htmlentities($displayvalue, ENT_COMPAT, 'UTF-8'));
        $smarty->assign('class', $class);
        $smarty->assign('maxsize', $this->fd_maxsize);
        $smarty->assign('readonly', $this->fd_readonly);
        $smarty->assign('fd_help', htmlentities($this->fd_help));

        return  $smarty->fetch('admin/fields/encryptedtext.tpl');
    }

    function setValue($newvalue)
    {
        /* encode the string data before saving */
        $key = $this->fd_options;
        require_once(_BASEPLUGINDIR . '/jojo_core/external/Horde/Cipher.php');
        require_once(_BASEPLUGINDIR . '/jojo_core/external/Horde/Cipher/blowfish.php');
        $cipher = &Horde_Cipher::factory('blowfish');
        $cipher->setBlockMode('ofb64');
        $cipher->setIV('jojojojo');
        $cipher->setKey($key);

        $this->value = $cipher->encrypt($newvalue);
        return true;
    }
}