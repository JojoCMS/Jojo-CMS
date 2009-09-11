<?php
/**
 * The Cipher_rc4:: class implements the Cipher interface encryption data
 * using the RC4 encryption algorthim. This class uses the PEAR Crypt_RC4
 * class to do the encryption.
 *
 * $Horde: framework/Cipher/Cipher/rc4.php,v 1.4.12.8 2007/01/02 13:54:10 jan Exp $
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
class Horde_Cipher_rc4 extends Horde_Cipher {

    /**
     * Pointer to a PEAR Crypt_rc4 object
     *
     * @var array
     */
    var $_cipher = array();

    /**
     * Constructor
     */
    function Horde_Cipher_rc4($params = null)
    {
        require_once 'Crypt/Rc4.php';
        $this->_cipher = &new Crypt_Rc4();
    }

    /**
     * Set the key to be used for en/decryption.
     *
     * @param string $key  The key to use.
     */
    function setKey($key)
    {
        $this->_cipher->key($key);
    }

    /**
     * Return the size of the blocks that this cipher needs.
     *
     * @return integer  The number of characters per block.
     */
    function getBlockSize()
    {
        return 8;
    }

    /**
     * Encrypt a block of data.
     *
     * @param string $block  The data to encrypt.
     * @param string $key    The key to use.
     *
     * @return string  The encrypted output.
     */
    function encryptBlock($block, $key = null)
    {
        if (!is_null($key)) {
            $this->setKey($key);
        }

        // Make a copy of the cipher as it destroys itself during a crypt
        $cipher = $this->_cipher;
        $cipher->crypt($block);

        return $block;
    }

    /**
     * Decrypt a block of data.
     *
     * @param string $block  The data to decrypt.
     * @param string $key    The key to use.
     *
     * @return string  The decrypted output.
     */
    function decryptBlock($block, $key = null)
    {
        if (!is_null($key)) {
            $this->setKey($key);
        }

        // Make a copy of the cipher as it destroys itself during a
        // crypt.
        $cipher = $this->_cipher;
        $cipher->decrypt($block);

        return $block;
    }

}
