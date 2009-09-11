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

$table = 'articlecomment';
$o = 1;

$default_td[$table]['td_displayfield'] = 'articlecommentid';
$default_td[$table]['td_parentfield'] = '';
$default_td[$table]['td_rolloverfield'] = '';
$default_td[$table]['td_orderbyfields'] = 'ac_timestamp DESC';
$default_td[$table]['td_topsubmit'] = 'yes';
$default_td[$table]['td_deleteoption'] = 'yes';
$default_td[$table]['td_menutype'] = 'tree';
$default_td[$table]['td_categoryfield'] = '';
$default_td[$table]['td_categorytable'] = '';
$default_td[$table]['td_group1'] = 'ac_articleid';
$default_td[$table]['td_help'] = '';

//Article Comment ID
$default_fd[$table]['articlecommentid']['fd_order'] = $o++;
$default_fd[$table]['articlecommentid']['fd_type'] = 'readonly';
$default_fd[$table]['articlecommentid']['fd_help'] = 'A unique ID, automatically assigned by the system';

//Timestamp
$default_fd[$table]['ac_timestamp']['fd_order'] = $o++;
$default_fd[$table]['ac_timestamp']['fd_required'] = 'yes';
$default_fd[$table]['ac_timestamp']['fd_default'] = 'NOW()';
$default_fd[$table]['ac_timestamp']['fd_help'] = '';

//Name (of poster)
$default_fd[$table]['ac_name']['fd_order'] = $o++;
$default_fd[$table]['ac_name']['fd_type'] = 'text';
$default_fd[$table]['ac_name']['fd_size'] = '40';
$default_fd[$table]['ac_name']['fd_help'] = '';

//Body
$default_fd[$table]['ac_body']['fd_order'] = $o++;
$default_fd[$table]['ac_body']['fd_type'] = 'wysiwygeditor';
$default_fd[$table]['ac_body']['fd_rows'] = '10';
$default_fd[$table]['ac_body']['fd_cols'] = '50';
$default_fd[$table]['ac_body']['fd_help'] = '';

//BB Body
$default_fd[$table]['ac_bbbody']['fd_order'] = $o++;
$default_fd[$table]['ac_bbbody']['fd_type'] = 'bbeditor';
$default_fd[$table]['ac_bbbody']['fd_options'] = 'ar_body';
$default_fd[$table]['ac_bbbody']['fd_rows'] = '10';
$default_fd[$table]['ac_bbbody']['fd_cols'] = '50';
$default_fd[$table]['ac_bbbody']['fd_help'] = '';

//Email
$default_fd[$table]['ac_email']['fd_order'] = $o++;
$default_fd[$table]['ac_email']['fd_type'] = 'email';
$default_fd[$table]['ac_email']['fd_size'] = '40';
$default_fd[$table]['ac_email']['fd_help'] = '';

//Website
$default_fd[$table]['ac_website']['fd_order'] = $o++;
$default_fd[$table]['ac_website']['fd_type'] = 'url';
$default_fd[$table]['ac_website']['fd_size'] = '40';
$default_fd[$table]['ac_website']['fd_help'] = '';

//Article ID
$default_fd[$table]['ac_articleid']['fd_order'] = $o++;
$default_fd[$table]['ac_articleid']['fd_type'] = 'dblist';
$default_fd[$table]['ac_articleid']['fd_options'] = 'article';
$default_fd[$table]['ac_articleid']['fd_help'] = '';

//User ID
$default_fd[$table]['ac_userid']['fd_order'] = $o++;
$default_fd[$table]['ac_userid']['fd_type'] = 'dblist';
$default_fd[$table]['ac_userid']['fd_options'] = 'user';
$default_fd[$table]['ac_userid']['fd_help'] = '';

//Status
$default_fd[$table]['ac_status']['fd_order'] = $o++;
$default_fd[$table]['ac_status']['fd_type'] = 'radio';
$default_fd[$table]['ac_status']['fd_default'] = 'active';
$default_fd[$table]['ac_status']['fd_options'] = "active\ninactive";
$default_fd[$table]['ac_status']['fd_help'] = '';