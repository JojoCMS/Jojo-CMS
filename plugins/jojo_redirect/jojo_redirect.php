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
 * @package jojo_redirect
 */
class Jojo_plugin_jojo_redirect extends Jojo_Plugin {

    /**
     * Custom requestURI function to handle redirects.
     */
    public static function redirect($uri) {

        /* Perform redirects based on redirect table */
        /* Put all redirects into the session */
        $_SESSION['redirects'] = (isset($_SESSION['redirects']) && !Jojo::ctrlf5()) ? $_SESSION['redirects'] : Jojo::selectAssoc("SELECT rd_from as first, redirect.* FROM {redirect} as redirect ORDER BY rd_order, rd_from");
        $redirects = $_SESSION['redirects'];
         /* Check for a direct match */
        if (isset($redirects[_SITEURI])) {
            $redirect = $redirects[_SITEURI];
            if ($redirect['rd_type'] == '301') {
                header("HTTP/1.1 301 Moved Permanently");
            }
            $to = $redirect['rd_to'];

            /* Make relative internal URLs absolute */
            if (Jojo::addhttp($to) != $to) {
                $to = _SITEURL . '/' . ltrim($to, '/');
            }
            header("Location: " . $to);
            exit;
        }
         /* If no match, check for a regex match */
        foreach ($redirects as $redirect) {
            if (isset($redirect['rd_regex']) && $redirect['rd_regex'] ==1) {
              $regex = '#' . $redirect['rd_from'] . '#';
              if (preg_match($regex, $uri, $matches)) {
                   if ($redirect['rd_type'] == '301') {
                        header("HTTP/1.1 301 Moved Permanently");
                    }
                    /* If the regex contains a () match and the 'to' url does too, replace the 'to' match with the 'from' match */
                    if (strpos($redirect['rd_to'], '()')!==false) {
                        $to = str_replace('()', $matches[1], $redirect['rd_to']);
                    } else {
                        $to = $redirect['rd_to'];
                    }

                    /* Make relative internal URLs absolute */
                    if (Jojo::addhttp($to) != $to) {
                        $to = _SITEURL . '/' . ltrim($to, '/');
                    }
                    header("Location: " . $to);
                    exit;
                }
            }
        }

       return false;
    }
}
