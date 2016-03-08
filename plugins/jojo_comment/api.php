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
        'Jojo_Plugin_Jojo_comment' => 'Comment Admin',
        );

/* Register URI handlers */
Jojo::registerURI(null, 'jojo_plugin_jojo_comment', 'isUrl');

$_options[] = array(
    'id'          => 'comment_subscriptions',
    'category'    => 'Comments',
    'label'       => 'Comment subscriptions',
    'description' => 'Commenters can subscribe to comments and receive email notifications.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'new_comment_position',
    'category'    => 'Comments',
    'label'       => 'New Comment Form Position',
    'description' => 'Should the New Comment form be above or below the existing comments?',
    'type'        => 'radio',
    'default'     => 'below',
    'options'     => 'above,below',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_show_form',
    'category'    => 'Comments',
    'label'       => 'Show comments form',
    'description' => 'When this option is enabled, the Post Comment form will be visible by default. Otherwise, the form is available by clicking a link / button',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_anchor_text',
    'category'    => 'Comments',
    'label'       => 'Comments Anchor Text',
    'description' => 'Allows users to choose the link text for their link. For good comments, this can be enabled.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_article'
);

$_options[] = array(
    'id'          => 'comment_optional_email',
    'category'    => 'Comments',
    'label'       => 'Optional email address',
    'description' => 'Allows users to comment without leaving their email address, which is usually a required field.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_default_link_type',
    'category'    => 'Comments',
    'label'       => 'Nofollow link type',
    'description' => 'As of June 2009, nofollowing links is no longer recommended as they bleed pagerank. To avoid this, POST links are recommended.',
    'type'        => 'radio',
    'default'     => 'post',
    'options'     => 'post,nofollow,text',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_optional_website',
    'category'    => 'Comments',
    'label'       => 'Website link',
    'description' => 'Allow posters to add a web address link to their name (good for their SEO, bad for attracting spambots)',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_show_num',
    'category'    => 'Comments',
    'label'       => 'Show number of comments on posts',
    'description' => 'Shows the number of comments made on each article on the article index page',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_useronly',
    'category'    => 'Comments',
    'label'       => 'Registered Users only',
    'description' => 'Restrict comments to email addresses that match a current user',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_captcha',
    'category'    => 'Comments',
    'label'       => 'Use Captcha on post comment form',
    'description' => '',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_spam_num',
    'category'    => 'Comments',
    'label'       => 'Spam comment number',
    'description' => 'Number of comments allowed from one IP address within (#) mins',
    'type'        => 'integer',
    'default'     => '3',
    'options'     => '',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_spam_time',
    'category'    => 'Comments',
    'label'       => 'Spam timelimit',
    'description' => 'Time limit of (#) comments allowed from one IP address',
    'type'        => 'integer',
    'default'     => '5',
    'options'     => '',
    'plugin'      => 'jojo_comment'
);

$_options[] = array(
    'id'          => 'comment_webmaster',
    'category'    => 'Comments',
    'label'       => 'Comment Email to Webmaster',
    'description' => 'Copy admin emails to webmaster as well as site contact.',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_comment'
);

