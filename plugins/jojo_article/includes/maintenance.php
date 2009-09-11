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

/* if any changes have been made today, use www.pingomatic.com to tell the world */

$article_last_updated = Jojo::getOption('article_last_updated');
if (($lastmaintenence + 86400) > time()) { //60 * 60 * 24 = 86400 seconds
    foreach (Jojo::listPlugins('jojo_article.php') as $pluginfile) {
        require_once($pluginfile);
        break;
    }

    $rssurl = Jojo_Plugin_Jojo_article::_getPrefix('rss');
    $pingomatic = 'http://pingomatic.com/ping/?title='.urlencode(Jojo::getOption('sitetitle')).'&blogurl='.urlencode(Jojo::getOption('siteurl')).'&rssurl='.urlencode(Jojo::getOption('siteurl')).'/'.urlencode($rssurl).'&chk_weblogscom=on&chk_blogs=on&chk_technorati=on&chk_feedburner=on&chk_syndic8=on&chk_newsgator=on&chk_myyahoo=on&chk_pubsubcom=on&chk_blogdigger=on&chk_blogstreet=on&chk_moreover=on&chk_weblogalot=on&chk_icerocket=on&chk_newsisfree=on&chk_topicexchange=on';

    if (!Jojo::isLocalServer()) {
        /* do the ping here */
    }
}

/*
//currently disabled until we can block pings from local development servers



*/