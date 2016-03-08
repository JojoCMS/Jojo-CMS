<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2009 Jojo CMS <info@jojocms.org>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */
class Jojo_plugin_post_redirect extends Jojo_plugin {

    function _getContent() {
        $uri = Jojo::getPost('uri', false);
        if (!$uri) {
            exit;
        }
        Jojo::redirect($uri);
    }
}
