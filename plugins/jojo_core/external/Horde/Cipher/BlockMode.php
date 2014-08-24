<?php
/**
 * The Horde_Cipher_BlockMode:: class provides a common abstracted
 * interface to various block mode handlers for ciphers.
 *
 * $Horde: framework/Cipher/Cipher/BlockMode.php,v 1.14.12.10 2007/01/02 13:54:10 jan Exp $
 *
 * Copyright 2002-2007 Mike Cochrane <mike@graftonhall.co.nz>
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Mike Cochrane <mike@graftonhall.co.nz>
 * @since   Horde 2.2
 * @package Horde_Cipher
 */
class Horde_Cipher_BlockMode {

    /**
     * The initialization vector.
     *
     * @var string
     */
    var $_iv = "\0\0\0\0\0\0\0\0";

    /**
     * Attempts to return a concrete Horde_Cipher_BlockMode instance based on
     * $mode.
     *
     * @param string $mode   The type of concrete Horde_Cipher_BlockMode
     *                       subclass to return.
     * @param array $params  A hash containing any additional parameters a
     *                       subclass might need.
     *
     * @return Horde_Cipher_BlockMode  The newly created concrete
     *                                 CipherBlockMode instance, or PEAR_Error
     *                                 on error.
     */
    function &factory($mode, $params = null)
    {
        $mode = basename($mode);
        if (@file_exists(dirname(__FILE__) . '/BlockMode/' . $mode . '.php')) {
            require_once dirname(__FILE__) . '/BlockMode/' . $mode . '.php';
        }

        $class = 'Horde_Cipher_BlockMode_' . $mode;
        if (class_exists($class)) {
            $blockmode = &new $class($params);
        } else {
            $blockmode = PEAR::raiseError('Class definition of ' . $class . ' not found.');
        }

        return $blockmode;
    }

    /**
     * Set the IV
     *
     * @param string $iv  The new IV.
     */
    function setIV($iv)
    {
        $this->_iv = $iv;
    }

}
