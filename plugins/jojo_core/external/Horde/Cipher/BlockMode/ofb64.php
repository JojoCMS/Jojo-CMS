<?php
/**
 * The Horde_Cipher_BlockMode_ofb64:: This class implements the
 * Horde_Cipher_BlockMode using a 64 bit output feedback.
 *
 * This can used to encrypt any length string and the encrypted version
 * will be the same length.
 *
 * $Horde: framework/Cipher/Cipher/BlockMode/ofb64.php,v 1.7.12.6 2007/01/02 13:54:11 jan Exp $
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
class Horde_Cipher_BlockMode_ofb64 extends Horde_Cipher_BlockMode {

    /**
     * Encrypt a string.
     *
     * @param Horde_Cipher $cipher  Cipher algorithm to use for
     *                              encryption.
     * @param string $plaintext     The data to encrypt.
     *
     * @return  The encrypted data.
     */
    function encrypt(&$cipher, $plaintext)
    {
        $encrypted = '';

        $n = 0;
        $jMax = strlen($plaintext);
        for ($j = 0; $j < $jMax; $j++) {
            if ($n == 0) {
                $this->_iv = $cipher->encryptBlock($this->_iv);
            }

            $c = $plaintext[$j] ^ $this->_iv[$n];
            $encrypted .= $c;

            $n = (++$n) & 0x07;
        }

        return $encrypted;
    }

    /**
     * Decrypt a string.
     *
     * @param Horde_Cipher $cipher  Cipher algorithm to use for
     *                              decryption.
     * @param string $ciphertext    The data to decrypt.
     *
     * @return  The decrypted data.
     */
    function decrypt(&$cipher, $ciphertext)
    {
        $decrypted = '';

        $n = 0;
        $jMax = strlen($ciphertext);
        for ($j = 0; $j < $jMax; $j++) {
            if ($n == 0) {
                $this->_iv = $cipher->encryptBlock($this->_iv);
            }

            $c = $ciphertext[$j] ^ $this->_iv[$n];
            $decrypted .= $c;

            $n = (++$n) & 0x07;
        }

        return $decrypted;
    }

}
