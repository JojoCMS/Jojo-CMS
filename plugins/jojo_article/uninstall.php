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

/* remove the Article page from the menu */
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_Article'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_Article_RSS'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_Article_Admin'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_url='admin/edit/article'");
Jojo::deleteQuery("DELETE FROM {page} WHERE pg_url='admin/edit/articlecategory'");

Jojo::structureQuery("DROP TABLE {article}");
Jojo::structureQuery("DROP TABLE {articlecomment}");
Jojo::structureQuery("DROP TABLE {articlecategory}");
Jojo::structureQuery("DROP TABLE {articlecommentsubscription}");