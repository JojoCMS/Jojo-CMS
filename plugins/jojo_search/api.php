<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Cochrane <mikec@jojocms.org>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_search
 */

/* Register URI patterns */
Jojo::registerURI("search/[q:(.*)]", 'Jojo_Plugin_Jojo_search'); // "search/query string/"

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_search' => 'Search - Site search page',
        );

Jojo::addHook('SearchForm', 'searchHTML', 'jojo_search');

$_options[] = array(
    'id'          => 'search_relevance',
    'category'    => 'Search',
    'label'       => 'Show Result Relevance',
    'description' => 'Show the search relevance of the result with the result',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_search'
);

$_options[] = array(
    'id'          => 'search_images',
    'category'    => 'Search',
    'label'       => 'Show Result Images',
    'description' => 'Show result page image thumbnails with the result',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_search'
);

$_options[] = array(
    'id'          => 'searchform_label',
    'category'    => 'Search',
    'label'       => 'Search form label',
    'description' => 'Label to use on the search form. Set to -1 to hide',
    'type'        => 'text',
    'default'     => 'Search',
    'options'     => '',
    'plugin'      => 'jojo_search'
);

$_options[] = array(
    'id'          => 'searchform_submit',
    'category'    => 'Search',
    'label'       => 'Search form submit',
    'description' => 'Text to use on the search submit. Set to -1 for no text (and use css to add a background image instead)',
    'type'        => 'text',
    'default'     => 'Go',
    'options'     => '',
    'plugin'      => 'jojo_search'
);

$_options[] = array(
    'id'          => 'search_image_format',
    'category'    => 'Search',
    'label'       => 'Result Image Format',
    'description' => 'Size / format of the search result image - eg 100, w50, s80 etc. See docs on image resizing.',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_search'
);

$_options[] = array(
    'id'          => 'search_filtering',
    'category'    => 'Search',
    'label'       => 'Show Result Filtering',
    'description' => 'Show a filter for results by plugin/category',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_search'
);

$_options[] = array(
    'id'          => 'search_urlquery',
    'category'    => 'Search',
    'label'       => 'URL or Query',
    'description' => 'Format the URL as a plain url : search/searchterm/ or as a query : search/?q=searchterm',
    'type'        => 'radio',
    'default'     => 'url',
    'options'     => 'url,query',
    'plugin'      => 'jojo_search'
);
