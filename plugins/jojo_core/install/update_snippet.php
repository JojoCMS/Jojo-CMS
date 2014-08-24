<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$default_td['snippet'] = array(
        'td_name' => "snippet",
        'td_primarykey' => "snippetid",
        'td_displayfield' => "name",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
        'td_languagefield' => "section",
        'td_defaultpermissions' => "everyone.show=1\neveryone.view=1\neveryone.edit=1\neveryone.add=1\neveryone.delete=1\nadmin.show=1\nadmin.view=1\nadmin.edit=1\nadmin.add=1\nadmin.delete=1\nnewsletter.show=1\nnewsletter.view=1\nnewsletter.edit=1\nnewsletter.add=1\nnewsletter.delete=1\nnotloggedin.show=1\nnotloggedin.view=1\nnotloggedin.edit=1\nnotloggedin.add=1\nnotloggedin.delete=1\nregistered.show=1\nregistered.view=1\nregistered.edit=1\nregistered.add=1\nregistered.delete=1\nsysinstall.show=1\nsysinstall.view=1\nsysinstall.edit=1\nsysinstall.add=1\nsysinstall.delete=1\n",
    );

// Snippetid Field
$default_fd['snippet']['snippetid'] = array(
        'fd_name' => "Snippetid",
        'fd_type' => "integer",
        'fd_readonly' => "1",
        'fd_order' => "1",
    );

// Name Field
$default_fd['snippet']['name'] = array(
        'fd_name' => "Name",
        'fd_type' => "text",
        'fd_order' => "2",
    );

// Snippet Field
$default_fd['snippet']['snippet'] = array(
        'fd_name' => "Snippet",
        'fd_type' => "hidden",
        'fd_order' => "3",
    );

// Snippet Field
$default_fd['snippet']['snippet_code'] = array(
        'fd_name' => "Snippet",
        'fd_type' => "texteditor",
        'fd_options' => "snippet",
        'fd_order' => "4",
    );

// Section Field
$default_fd['snippet']['section'] = array(
        'fd_name' => "Section",
        'fd_type' => "hidden",
        'fd_options' => "",
        'fd_order' => "5",
    );
