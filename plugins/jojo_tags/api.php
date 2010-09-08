<?php
/**
 *
 * Copyright 2007-2008 Michael Cochrane <mikec@jojocms.org>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_tags
 */

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_tags' => 'Tags - Tag Cloud and listing',
        );

/* Tag Cloud filter */
Jojo::addFilter('output', 'tagcloudfilter', 'jojo_tags');

/* XML Sitemap filter */
Jojo::addFilter('jojo_xml_sitemap', 'xmlsitemap', 'jojo_tags');

/* Register URI patterns for all languages */
foreach(Jojo::selectQuery('SELECT DISTINCT pg_url FROM {page} where pg_link = ?', 'jojo_plugin_jojo_tags') as $row) {
    Jojo::registerURI($row['pg_url'] . "/[tags:(.*)]", 'jojo_plugin_jojo_tags'); // "tags/name-of-tag/"
}

$_provides['fieldTypes'] = array(
        'tag' => 'Tags',
        'replacelist' => 'Replace List',
        );

$_options[] = array(
    'id'          => 'article_tag_cloud_related',
    'category'    => 'Articles',
    'label'       => 'Show Related Tag Cloud',
    'description' => 'Related tag cloud can take processing time.  Set to no to remove.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_tags'
);

$_options[] = array(
    'id'          => 'tag_stricturl',
    'category'    => 'Tags',
    'label'       => 'Strict URL',
    'description' => 'Restrict tags to url friendly characters and replace spaces with hyphens in URLs, or allow free-form tags and encode URLs.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_tags'
);