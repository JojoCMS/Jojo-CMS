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
 * @package jojo_tags
 */

$table = 'tag';
$o = 1;

$default_td[$table]['td_primarykey'] = 'tagid';
$default_td[$table]['td_displayfield'] = 'tg_tag';
$default_td[$table]['td_orderbyfields'] = 'tg_tag';
$default_td[$table]['td_topsubmit'] = 'yes';
$default_td[$table]['td_deleteoption'] = 'yes';
$default_td[$table]['td_menutype'] = 'list';
$default_td[$table]['td_help'] = '';

//Tag ID
$field = 'tagid';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'readonly';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_help'] = 'An automatically assigned unique id for this tag.';
$default_fd[$table][$field]['fd_mode'] = 'basic';

//Tag
$field = 'tg_tag';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_required'] = 'yes';
$default_fd[$table][$field]['fd_help'] = 'A tag is a word or phrase to describe content on a page. Tags are useful for searching within your site, and are used most often on blog websites.';
$default_fd[$table][$field]['fd_mode'] = 'basic';

//SEO Title
$field = 'tg_seotitle';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'text';
$default_fd[$table][$field]['fd_Name'] = 'SEO Title';
$default_fd[$table][$field]['fd_size'] = '50';
$default_fd[$table][$field]['fd_help'] = 'Search Engine Optimised text for the title bar';
$default_fd[$table][$field]['fd_options'] = 'seotitle';
$default_fd[$table][$field]['fd_tabname'] = '';
$default_fd[$table][$field]['fd_mode'] = 'standard';

//BBBody
$field = 'tg_bbbody';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'texteditor';
$default_fd[$table][$field]['fd_name'] = 'Body';
$default_fd[$table][$field]['fd_options'] = 'tg_body'; //this is where the HTML will be cached
$default_fd[$table][$field]['fd_rows'] = '10';
$default_fd[$table][$field]['fd_cols'] = '50';
$default_fd[$table][$field]['fd_help'] = '';
$default_fd[$table][$field]['fd_tabname'] = '';
$default_fd[$table][$field]['fd_mode'] = 'basic';

//Body
$field = 'tg_body';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'hidden';
$default_fd[$table][$field]['fd_name'] = 'Body';
$default_fd[$table][$field]['fd_options'] = '';
$default_fd[$table][$field]['fd_help'] = '';
$default_fd[$table][$field]['fd_tabname'] = '';
$default_fd[$table][$field]['fd_mode'] = 'basic';

//META Description
$field = 'tg_metadesc';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'textarea';
$default_fd[$table][$field]['fd_name'] = 'Meta Description';
$default_fd[$table][$field]['fd_rows'] = '3';
$default_fd[$table][$field]['fd_cols'] = '40';
$default_fd[$table][$field]['fd_help'] = 'A good sales oriented description of the page for the Search Engine snippet';
$default_fd[$table][$field]['fd_options'] = 'metadescription';
$default_fd[$table][$field]['fd_tabname'] = '';
$default_fd[$table][$field]['fd_mode'] = 'standard';

//Tag ID
$field = 'tg_replace';
$default_fd[$table][$field]['fd_order'] = $o++;
$default_fd[$table][$field]['fd_type'] = 'replacelist';
$default_fd[$table][$field]['fd_options'] = 'tag';
$default_fd[$table][$field]['fd_name'] = 'Delete & replace with';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_help'] = 'Delete this tag and replace all instances of it with the selected tag';
$default_fd[$table][$field]['fd_mode'] = 'basic';


/* Tags Tab for Edit Pages */

// Tags Field
$default_fd['page']['pg_tags'] = array(
        'fd_name' => "Tags",
        'fd_type' => "tag",
        'fd_options' => "Core",
        'fd_help' => "A list of words describing the page",
        'fd_order' => "1",
        'fd_tabname' => "Tags",
    );


/* Tags Tab for Edit Articles */
if (Jojo::tableExists('article')) {
// Tags Field
$default_fd['article']['ar_tags'] = array(
        'fd_name' => "Tags",
        'fd_type' => "tag",
        'fd_options' => "jojo_article",
        'fd_showlabel' => "no",
        'fd_help' => "A list of words describing the article",
        'fd_order' => "1",
        'fd_tabname' => "Tags",
        'fd_mode' => "standard",
    );
}