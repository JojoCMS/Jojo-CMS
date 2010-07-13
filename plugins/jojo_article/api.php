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

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_article'       => 'Article - Article Listing and View',
        'Jojo_Plugin_Jojo_article_admin' => 'Article - Article Comment Admin',
        );

/* Register URI handlers */
Jojo::registerURI(null, 'jojo_plugin_jojo_article', 'isArticleUrl');
Jojo::registerURI(null, 'Jojo_Plugin_Jojo_article_admin', 'isArticleUrl');

/* Article RSS icon filter */
Jojo::addFilter('rssicon', 'rssicon', 'jojo_article');

/* Sitemap filter */
Jojo::addFilter('jojo_sitemap', 'sitemap', 'jojo_article');

/* XML Sitemap filter */
Jojo::addFilter('jojo_xml_sitemap', 'xmlsitemap', 'jojo_article');

/* Search Filter */
if (class_exists('Jojo_Plugin_Jojo_search')) {
    Jojo::addFilter('jojo_search', 'search', 'jojo_article');
}
/* Content Filter */
Jojo::addFilter('content', 'removesnip', 'jojo_article');

/* capture the button press in the admin section */
Jojo::addHook('admin_action_after_save', 'admin_action_after_save', 'jojo_article');
Jojo::addHook('admin_action_after_save_page', 'admin_action_after_save_page', 'jojo_article');
Jojo::addHook('admin_action_after_save_articlecategory', 'admin_action_after_save_articlecategory', 'jojo_article');

$_options[] = array(
    'id'          => 'article_comment_subscriptions',
    'category'    => 'Articles',
    'label'       => 'Comment subscriptions',
    'description' => 'Commenters can subscribe to comments and receive email notifications.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_show_comment_form',
    'category'    => 'Articles',
    'label'       => 'Show comments form',
    'description' => 'When this option is enabled, the Post Comment form will be visible by default. Otherwise, the form is available by clicking a link / button',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_show_num_comments',
    'category'    => 'Articles',
    'label'       => 'Show number of comments on posts',
    'description' => 'Shows the number of comments made on each article on the article index page',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_tag_cloud_minimum',
    'category'    => 'Articles',
    'label'       => 'Minimum tags to form cloud',
    'description' => 'On the article pages, a tag cloud will be formed from tags if this number of tags is met (otherwise a plain text list of tags is shown). Set to zero to always use the plain text list.',
    'type'        => 'integer',
    'default'     => '0',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_feed_source_link',
    'category'    => 'Articles',
    'label'       => 'Append source link to RSS feed',
    'description' => 'Appends a source link to the bottom of the article in the RSS feed. This is to ensure scraper sites are providing a link back to the original article.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_show_date',
    'category'    => 'Articles',
    'label'       => 'Show Date on posts',
    'description' => 'Shows the publish date at the top of each article page',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'articlesperpage',
    'category'    => 'Articles',
    'label'       => 'Articles per page on index',
    'description' => 'The number of articles to show on the Articles index page before paginating',
    'type'        => 'integer',
    'default'     => '40',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_next_prev',
    'category'    => 'Articles',
    'label'       => 'Show Next / Previous links',
    'description' => 'Show a link to the next and previous article at the top of each article page',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_num_related',
    'category'    => 'Articles',
    'label'       => 'Show Related Articles',
    'description' => 'The number of related articles to show at the bottom of each article (0 means do not show)',
    'type'        => 'integer',
    'default'     => '5',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_external_rss',
    'category'    => 'Articles',
    'label'       => 'External Article RSS URL',
    'description' => 'The external URL (eg Feedburner) for the Article RSS feed (leave blank to use the Jojo defaults)',
    'type'        => 'text',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_rss_num_articles',
    'category'    => 'Articles',
    'label'       => 'Number of RSS articles',
    'description' => 'The number of articles to be displayed in the RSS feed (more articles will use more bandwidth))',
    'type'        => 'integer',
    'default'     => '15',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_full_rss_description',
    'category'    => 'Articles',
    'label'       => 'Full Article RSS Description',
    'description' => 'If YES, a full copy of the article is provided in the RSS feed. If NO, the RSS feed only includes content before the snip filter tag.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_rss_truncate',
    'category'    => 'Articles',
    'label'       => 'Article RSS default truncation',
    'description' => 'If Full Description is set to No above, truncate articles with no embedded snip filter tag to this length',
    'type'        => 'integer',
    'default'     => '800',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'articlecomments',
    'category'    => 'Articles',
    'label'       => 'Article comments',
    'description' => 'Allows user comments to be added to articles / blog posts.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'sitedesc',
    'category'    => 'Articles',
    'label'       => 'Site description',
    'description' => 'A one sentence unique description of what the site is about. Included in RSS feeds.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_anchor_text',
    'category'    => 'Articles',
    'label'       => 'Article Anchor Text',
    'description' => 'Allows users to choose the link text for their link. For good comments, this can be enabled.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_last_updated',
    'category'    => 'System',
    'label'       => 'Articles last updated',
    'description' => 'The timestamp of when the last update was made to an article.',
    'type'        => 'integer',
    'default'     => '1',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_num_sidebar_articles',
    'category'    => 'Articles',
    'label'       => 'Number of article teasers to show in the sidebar',
    'description' => 'The number of articles to be displayed as snippets in a teaser box on other pages - set to 0 to disable',
    'type'        => 'integer',
    'default'     => '3',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_sidebar_randomise',
    'category'    => 'Articles',
    'label'       => 'Randmomise selection of teasers out of',
    'description' => 'Pick the sidebar articles from a larger group, shuffle them, and then slice them back to the original number so that sidebar content is more dynamic  - set to 0 to disable',
    'type'        => 'integer',
    'default'     => '0',
    'options'     => '',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_sidebar_categories',
    'category'    => 'Articles',
    'label'       => 'Article teasers by category',
    'description' => 'Generate sidebar list from all articles and also create a list from each category',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_sidebar_exclude_current',
    'category'    => 'Articles',
    'label'       => 'Exclude current article from list',
    'description' => 'Exclude the article from the sidebar list when on that articles page',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_inplacesitemap',
    'category'    => 'Articles',
    'label'       => 'Articles sitemap location',
    'description' => 'Show artciles as a separate list on the site map, or in-place on the page list',
    'type'        => 'radio',
    'default'     => 'separate',
    'options'     => 'separate,inplace',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_optional_email',
    'category'    => 'Articles',
    'label'       => 'Optional email address',
    'description' => 'Allows users to comment without leaving their email address, which is usually a required field.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_default_link_type',
    'category'    => 'Articles',
    'label'       => 'Nofollow link type',
    'description' => 'As of June 2009, nofollowing links is no longer recommended as they bleed pagerank. To avoid this, POST links are recommended.',
    'type'        => 'radio',
    'default'     => 'post',
    'options'     => 'post,nofollow,text',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'article_meta_description',
    'category'    => 'Articles',
    'label'       => 'Dynamic article meta description',
    'description' => 'A dynamically built meta description template to use for articles, which will assist with SEO. Variables to use are [title], [author], [site], [body].',
    'type'        => 'textarea',
    'default'     => '[title], an article on [site]. [body]...',
    'options'     => '',
    'plugin'      => 'jojo_article'
);