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
 * @package jojo_article
 */

$table = 'comment';
$o = 1;

$default_td[$table]['td_displayfield'] = 'body';
$default_td[$table]['td_parentfield'] = '';
$default_td[$table]['td_rolloverfield'] = '';
$default_td[$table]['td_orderbyfields'] = 'timestamp DESC';
$default_td[$table]['td_topsubmit'] = 'yes';
$default_td[$table]['td_deleteoption'] = 'yes';
$default_td[$table]['td_menutype'] = 'tree';
$default_td[$table]['td_categoryfield'] = '';
$default_td[$table]['td_categorytable'] = '';
$default_td[$table]['td_group1'] = 'plugin';
$default_td[$table]['td_help'] = '';

// id Field
$default_fd[$table]['commentid'] = array(
        'fd_name' => "ID",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => $o++,
    );

//Timestamp
$default_fd[$table]['timestamp'] = array(
        'fd_name' => "Timestamp",
        'fd_type' => "unixdate",
        'fd_readonly' => 1,
        'fd_default' =>  'now',
        'fd_help' => "time comment added",
        'fd_order' => $o++,
    );

//Name (of poster)
$default_fd[$table]['name'] = array(
        'fd_name' => "Posted by",
        'fd_type' => "text",
        'fd_size' => 40,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//Body
$default_fd[$table]['body'] = array(
        'fd_name' => "HTML Content",
        'fd_type' => "hidden",
        'fd_readonly' => 1,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//BB Body
$default_fd[$table]['bbbody'] = array(
        'fd_name' => "BB Content",
        'fd_type' => "texteditor",
        'fd_options' => "body",
        'fd_rows' => 10,
        'fd_cols' => 50,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//Email
$default_fd[$table]['email']['fd_order'] = $o++;
$default_fd[$table]['email']['fd_type'] = 'email';
$default_fd[$table]['email']['fd_size'] = '40';
$default_fd[$table]['email']['fd_help'] = '';

//Website
$default_fd[$table]['website']['fd_order'] = $o++;
$default_fd[$table]['website']['fd_type'] = 'url';
$default_fd[$table]['website']['fd_size'] = '40';
$default_fd[$table]['website']['fd_help'] = '';

//Anchor Text
$default_fd[$table]['anchortext']['fd_order'] = $o++;
$default_fd[$table]['anchortext']['fd_type'] = 'text';
$default_fd[$table]['anchortext']['fd_size'] = '40';
$default_fd[$table]['anchortext']['fd_help'] = '';


//User ID
$default_fd[$table]['userid']['fd_order'] = $o++;
$default_fd[$table]['userid']['fd_type'] = 'dblist';
$default_fd[$table]['userid']['fd_options'] = 'user';
$default_fd[$table]['userid']['fd_help'] = '';

//User IP
$default_fd[$table]['ip'] = array(
        'fd_name' => "User IP",
        'fd_type' => "text",
        'fd_readonly' => 1,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//Active
$default_fd[$table]['active'] = array(
        'fd_name' => "Active",
        'fd_type' => "yesno",
        'fd_default' => 1,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//Author
$default_fd[$table]['authorcomment'] = array(
        'fd_name' => "Is Author",
        'fd_type' => "yesno",
        'fd_default' => 0,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//Anchor text
$default_fd[$table]['useanchortext'] = array(
        'fd_name' => "Use Anchor Text",
        'fd_type' => "yesno",
        'fd_default' => 0,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//No Follow
$default_fd[$table]['nofollow'] = array(
        'fd_name' => "No Follow link",
        'fd_type' => "yesno",
        'fd_default' => 1,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//Admin Codes
$default_fd[$table]['approvecode'] = array(
        'fd_name' => "Approve code",
        'fd_type' => "text",
        'fd_readonly' => 1,
        'fd_help' => "",
        'fd_order' => $o++,
    );

$default_fd[$table]['deletecode'] = array(
        'fd_name' => "Delete code",
        'fd_type' => "text",
        'fd_readonly' => 1,
        'fd_help' => "",
        'fd_order' => $o++,
    );

$default_fd[$table]['anchortextcode'] = array(
        'fd_name' => "Anchortext code",
        'fd_type' => "text",
        'fd_readonly' => 1,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//Item ID
$default_fd[$table]['itemid'] = array(
        'fd_name' => "Item ID",
        'fd_type' => "text",
        'fd_readonly' => 1,
        'fd_help' => "",
        'fd_order' => $o++,
    );

//Item ID
$default_fd[$table]['plugin'] = array(
        'fd_name' => "plugin",
        'fd_type' => "text",
        'fd_readonly' => 1,
        'fd_help' => "",
        'fd_order' => $o++,
    );
