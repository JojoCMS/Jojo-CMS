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

class Jojo_Plugin_Jojo_article extends Jojo_Plugin
{
    /*
    * Comments
    */
    static function postComment()
    {
        global $smarty, $_USERID;

        $errors = array();

        $commentsubscriptions = Jojo::getOption('article_comment_subscriptions', 'no') == 'yes' ? true : false;

        /* Get variables from POST */
        $name            = Jojo::getFormData('name',            '');
        $authorcomment   = Jojo::getFormData('authorcomment',   'no');
        $email           = Jojo::getFormData('email',           '');
        $email_subscribe = Jojo::getFormData('email_subscribe', false) ? true : false;
        $website         = Jojo::getFormData('website',         '');
        $anchortext      = Jojo::getFormData('anchortext',      '');
        $bbcomment       = Jojo::getFormData('comment',         '');
        $articleid       = Jojo::getFormData('id',              0);
        $url             = Jojo::getFormData('url',             '');
        $captchacode     = Jojo::getFormData('captchacode',     '');
        if (!empty($website)) $website = Jojo::addhttp($website);

        /* sanitise input */
        $name       = htmlentities($name);
        $anchortext = htmlentities($anchortext);

        /* trim whitespace just in case people copy-paste the email address wrong */
        $email = trim($email);

        /* get the article ID if we don't have it already */
        if ($articleid) {
            $article = Jojo::selectRow("SELECT * FROM {article} WHERE articleid = ?", $articleid);
        } elseif ($url) {
            $article = Jojo::selectRow("SELECT * FROM {article} WHERE ar_url = ?", $url);
            if (count($article)) $articleid = $article['articleid'];
        } else {
            return false;
        }

        $ip = Jojo::getIP();

        /* Check CAPTCHA is entered correctly */
        if (empty($_USERID) && !PhpCaptcha::Validate($captchacode)) {
            $errors[] = 'Invalid code entered';
        }

        if (Jojo::getOption('articlecomments', 'no') != 'yes') {
            $errors[] = 'Article Comments are currently disabled';
        }

        /* are comments enabled for this post? */
        $articlecommentsenabled = !empty($article['ar_comments']) ? Jojo::yes2True($article['ar_comments']) : true;
        if (!$articlecommentsenabled) {
            $errors[] = 'Comments are disabled for this post';
        }

        /* Error checking */
        if ($name == '') {
            $errors[] = 'Please enter your name';
        }
        if ($bbcomment == '') {
            $errors[] = 'Please enter a comment';
        }
        if ((Jojo::getOption('article_optional_email', 'no') == 'no') && empty($email)) {
            $errors[] = 'Please enter your email address (this will not be shown to the public)';
        }
        if (($email != '') && !Jojo::checkEmailFormat($email)) {
            $errors[] = 'Email format is invalid';
        }
        if (($website != '') && !Jojo::checkUrlFormat($website)) {
            $errors[] = 'Website format is invalid';
        }

        /* rate limiting to prevent spam */
        $rate_comments = 5; // maximum X posts
        $rate_mins = 5;  // in X mins
        $ratelimit = Jojo::selectQuery("SELECT * FROM {articlecomment} WHERE ac_ip='" . $ip . "' AND ac_timestamp>" . strtotime('-' . $rate_mins . ' minutes'));
        if (count($ratelimit)>$rate_comments) {
            $errors[] = 'We limit comments to ' . $rate_comments . ' every ' . $rate_mins . ' minutes to prevent automated spam. Please try posting your comment again in a few minutes, and sorry for any inconvenience';
        }

        /* Convert BBCode to HTML */
        $bb = new bbconverter();
        $bb->truncateurl = 30;
        $bb->nofollow = true;
        $bb->setBBCode($bbcomment);
        $htmlcomment = $bb->convert('bbcode2html');

        /* Return errors */
        if (count($errors)) {
            /* Error */
            $smarty->assign('error',      implode("<br />\n", $errors));
            $smarty->assign('name',       $name);
            $smarty->assign('email',      $email);
            $smarty->assign('website',    $website);
            $smarty->assign('anchortext', $anchortext);
            $smarty->assign('comment',    $bbcomment);
            return false;
        }

        /* Create unique approve and delete codes, ensure they are not already in the database for another comment */
        $query = 'SELECT * FROM {articlecomment} WHERE ac_approvecode = ? OR ac_deletecode = ? OR ac_anchortextcode = ?';
        do {
            $approvecode = Jojo::randomString(16, '0123456789');
            $res = Jojo::selectQuery($query, array($approvecode, $approvecode, $approvecode));
        } while (count($res) > 0);
        do {
            $anchortextcode = Jojo::randomString(16, '0123456789');
            $res = Jojo::selectQuery($query, array($anchortextcode, $anchortextcode, $anchortextcode));
        } while (count($res) > 0);
        do {
            $deletecode = Jojo::randomString(16, '0123456789');
            $res = Jojo::selectQuery($query, array($deletecode, $deletecode, $deletecode));
        } while (count($res) > 0);

        Jojo::insertQuery("INSERT INTO {articlecomment} SET
                ac_timestamp = UNIX_TIMESTAMP(NOW()), ac_articleid = ?,
                ac_name = ?, ac_email = ?, ac_website = ?, ac_anchortext = ?,
                ac_ip = ?, ac_useanchortext = 'no', ac_authorcomment = ?,
                ac_bbbody = ?, ac_body = ?, ac_approvecode = ?,
                ac_anchortextcode = ?, ac_deletecode = ?",
                array($articleid, $name, $email, $website, $anchortext, Jojo::getIP(),
                      $authorcomment, $bbcomment, $htmlcomment, $approvecode, $anchortextcode, $deletecode));

        /* Store details in the session so the user doesn't have to reenter on every comment */
        $_SESSION['name']       = $name;
        $_SESSION['email']      = $email;
        $_SESSION['website']    = $website;
        $_SESSION['anchortext'] = $anchortext;

        /* Send article details for email to webmaster */
        $article['fullurl'] = Jojo_Plugin_Jojo_article::getArticleUrl($article['articleid'], $article['ar_url'], $article['ar_title'], $article['ar_language'], $article['ar_category']);
        $message  = "A comment has been added to an article on " . _SITEURL . "/". $article['fullurl'] . "\n\n";
        $message .= "Comment by: " . $name . "\n";
        $message .= $email != '' ? "Email: " . $email. "\n" : '';
        $message .= $website != '' ? "Website: " . $website . "\n" : '';
        $message .= $anchortext != '' ? "Anchor text: " . $anchortext . "\n" : '';
        $message .= "Comment:\n" . $htmlcomment . "\n";

        $message .= "\n\nThis comment is currently live on the site, but has a nofollow link\n\n";

        if (!empty($anchortext)) {
            $message .= "To FOLLOW the link on this comment AND use the chosen anchor text (for great comments), click the following link\n";
            $message .= _SITEURL . '/' . Jojo_Plugin_Jojo_article::_getPrefix('admin') . '/' . $anchortextcode . "/\n\n";
        }
        $message .= "To FOLLOW the link on this comment (for good comments), click the following link\n";
        $message .= _SITEURL . '/' . Jojo_Plugin_Jojo_article::_getPrefix('admin') . '/' . $approvecode . "/\n\n";
        $message .= "To DELETE this comment, click the following link\n";
        $message .= _SITEURL . '/' . Jojo_Plugin_Jojo_article::_getPrefix('admin') . '/' . $deletecode . "/\n";
        $message .= Jojo::emailFooter();

        /* Email comment to webmaster and site contact */
        Jojo::simplemail(_WEBMASTERNAME, _WEBMASTERADDRESS, Jojo::getOption('sitetitle') . ' Article Comment - ' . $article['ar_title'], $message, $name, $email);
        if(_WEBMASTERADDRESS != _CONTACTADDRESS) Jojo::simplemail(_FROMNAME, _CONTACTADDRESS, Jojo::getOption('sitetitle') . ' Article Comment - ' . $article['ar_title'], $message, $name, $email);

        /* add subscription if needed, and update all subscriptions to say the topic has a new comment */
        if ($commentsubscriptions && $email_subscribe && !empty($_USERID)) {
            Jojo_Plugin_Jojo_article::addSubscription($_USERID, $articleid);
            Jojo_Plugin_Jojo_article::markSubscriptionsUpdated($articleid);
        }

        /* log a copy of the comment */
        $log = new Jojo_Eventlog();
        $log->code = 'article comment';
        $log->importance = 'normal';
        $log->shortdesc = 'Article comment by ' . $name . ' on ' . $article['ar_title'];
        $log->desc = $message;
        $log->savetodb();
        unset($log);

        /* Delete cache for this page - forcing regeneration next view */
        if (_CONTENTCACHE) {
            $query = "DELETE FROM {contentcache} WHERE cc_url=? LIMIT 1";
            $values = array(_SITEURL . '/' . $article['fullurl']);
            Jojo::deleteQuery($query, $values);
        }

        /* Redirect back to the article to see the comment on the page */
        header('location: ' . Jojo_Plugin_Jojo_article::getCorrectUrl());
        exit();
    }

    static function addSubscription($userid, $articleid)
    {
        /* attempt to update existing subscription */
        $updated = Jojo::updateQuery("UPDATE {articlecommentsubscription}  SET lastviewed=?, lastemailed=0 WHERE userid=? AND articleid=? LIMIT 1", array(time(), $userid, $articleid));
        if ($updated) return true;

        /* create new subscription */
        $code = Jojo::randomString(16);
        Jojo::insertQuery("INSERT INTO {articlecommentsubscription} SET userid=?, articleid=?, lastviewed=?, lastemailed=0, code=?", array($userid, $articleid, time(), $code));
        return true;
    }

    static function removeSubscription($userid, $articleid)
    {
        $data = Jojo::selectQuery("SELECT * FROM {articlecommentsubscription} WHERE userid=? AND articleid=?", array($userid, $articleid));
        if (!count($data)) return false; //nothing to delete
        Jojo::deleteQuery("DELETE FROM {articlecommentsubscription} WHERE userid=? AND articleid=?", array($userid, $articleid));
        return true;
    }

    static function removeSubscriptionByCode($code, $articleid)
    {
        $data = Jojo::selectQuery("SELECT * FROM {articlecommentsubscription} WHERE code=? AND articleid=?", array($code, $articleid));
        if (!count($data)) return false; //nothing to delete
        Jojo::deleteQuery("DELETE FROM {articlecommentsubscription} WHERE code=? AND articleid=?", array($code, $articleid));
        return true;
    }

    static function isSubscribed($userid, $articleid)
    {
        $data = Jojo::selectQuery("SELECT * FROM {articlecommentsubscription} WHERE userid=? AND articleid=?", array($userid, $articleid));
        return (count($data)) ? true : false;
    }

    static function markSubscriptionsUpdated($articleid)
    {
        Jojo::insertQuery("UPDATE {articlecommentsubscription} SET lastupdated=? WHERE articleid=?", array(time(), $articleid));
    }

    static function markSubscriptionsViewed($userid, $articleid)
    {
        Jojo::insertQuery("UPDATE {articlecommentsubscription} SET lastviewed=? WHERE userid=? AND articleid=? LIMIT 1", array(time(), $userid, $articleid));
    }

    static function processSubscriptionEmails($limit=3) {
        $subscriptions = Jojo::selectQuery("SELECT acs.*, ar_title, us_firstname, us_login, us_email FROM {articlecommentsubscription} acs LEFT JOIN {article} ar ON (acs.articleid=ar.articleid) LEFT JOIN {user} us ON (acs.userid=us.userid) WHERE (lastupdated > lastviewed) AND (lastviewed > lastemailed) LIMIT ?", $limit);
        foreach ($subscriptions as $sub) {
            $subject  = 'New comment notification: ' . $sub['ar_title'];
            $message  = 'A new comment has been added to "' . $sub['ar_title'] . '" on ' . Jojo::getOption('sitetitle') . '. You are subscribed to receive email notifications notifications of any new comments on this post.';
            $message .= "To view the new comments, please visit the following link.\n";
            $message .= _SITEURL.'/'. JOJO_Plugin_Jojo_article::getArticleUrl($sub['articleid'])."\n\n";
            $message .= "You can unsubscribe from this notification by using the following link.\n";
            $message .= _SITEURL.'/'. JOJO_Plugin_Jojo_article::_getPrefix() . "/unsubscribe/".$sub['articleid']."/".$sub['code']."/\n";
            $message .= "\nRegards,\n" . Jojo::getOption('webmastername') . "\n" . Jojo::getOption('sitetitle')."\n".Jojo::getOption('fromaddress')."\n";
            if (@Jojo::simpleMail($sub['us_login'], $sub['us_email'], $subject, $message)) {
                Jojo::updateQuery("UPDATE {articlecommentsubscription} SET lastemailed=? WHERE userid=? AND articleid=?", array(time(), $sub['userid'], $sub['articleid']));
            }
        }
    }


/*
* Tags
*/

    static function saveTags($record, $tags = array())
    {
        /* Ensure the tags class is available */
        if (!class_exists('Jojo_Plugin_Jojo_Tags')) {
            return false;
        }

        /* Delete existing tags for this item */
        Jojo_Plugin_Jojo_Tags::deleteTags('jojo_article', $record['articleid']);

        /* Save all the new tags */
        foreach($tags as $tag) {
            Jojo_Plugin_Jojo_Tags::saveTag($tag, 'jojo_article', $record['articleid']);
        }
    }

    static function getTagSnippets($ids)
    {
        /* Convert array of ids to a string */
        $ids = "'" . implode($ids, "', '") . "'";

        /* Get the articles */
        $articles = Jojo::selectQuery("SELECT *
                                       FROM {article}
                                       WHERE
                                            articleid IN ($ids)
                                         AND
                                           ar_livedate < ?
                                         AND
                                           ar_expirydate<=0 OR ar_expirydate > ?
                                       ORDER BY
                                         ar_date DESC",
                                      array(time(), time()));

        /* Create the snippets */
        $snippets = array();
        foreach ($articles as $i => $a) {
            $image = !empty($a['ar_image']) ? 'articles/' . $a['ar_image'] : '';
            $snippets[] = array(
                    'id'    => $a['articleid'],
                    'image' => $image,
                    'title' => htmlspecialchars($a['ar_title'], ENT_COMPAT, 'UTF-8', false),
                    'text'  => strip_tags($a['ar_body']),
                    'url'   => Jojo::urlPrefix(false) . self::getArticleUrl($a['articleid'], $a['ar_url'], $a['ar_title'], $a['ar_language'], $a['ar_category'])
                );
        }

        /* Return the snippets */
        return $snippets;
    }

/*
* Core
*/

    /* Gets $num articles sorted by date (desc) for use on homepages and sidebars */
    static function getArticles($num, $start = 0, $categoryid='all', $sortby=false, $exclude=false,$usemultilanguage=true) {
        global $page;
        if (_MULTILANGUAGE) $language = !empty($page->page['pg_language']) ? $page->page['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
        $_CATEGORIES      = (Jojo::getOption('article_enable_categories', 'no') == 'yes') ? true : false;
        /* if calling page is an article, Get current articleid, and exclude from the list  */
        $excludethisid = ($exclude && Jojo::getOption('article_sidebar_exclude_current', 'no')=='yes' && $page->page['pg_link']=='jojo_plugin_jojo_article' && Jojo::getFormData('id')) ? Jojo::getFormData('id') : '';
        $excludethisurl = ($exclude && Jojo::getOption('article_sidebar_exclude_current', 'no')=='yes' && $page->page['pg_link']=='jojo_plugin_jojo_article' && Jojo::getFormData('url')) ? Jojo::getFormData('url') : '';
        $shownumcomments = (Jojo::getOption('articlecomments') == 'yes' && Jojo::getOption('article_show_num_comments', 'no') == 'yes') ? true : false;

        $now    = time();
        $query  = "SELECT ar.*";
        $query .= $_CATEGORIES ? ", ac.ac_url, p.pg_menutitle, p.pg_title" : '';
        $query .= $shownumcomments ? ", COUNT(acom.ac_articleid) AS numcomments" : '';
        $query .= " FROM {article} ar";
        $query .= $_CATEGORIES ? " LEFT JOIN {articlecategory} ac ON (ar.ar_category=ac.articlecategoryid) LEFT JOIN {page} p ON (ac.ac_url=p.pg_url)" : '';
        $query .= $shownumcomments ? " LEFT JOIN {articlecomment} acom ON (acom.ac_articleid = articleid)" : '';
        $query .= " WHERE ar_livedate<$now AND (ar_expirydate<=0 OR ar_expirydate>$now)";
        $query .= (_MULTILANGUAGE && $usemultilanguage) ? " AND (ar_language = '$language')" : '';
        $query .= ($_CATEGORIES && _MULTILANGUAGE && $usemultilanguage) ? " AND (pg_language = '$language')" : '';
        $query .= ($_CATEGORIES && $categoryid && $categoryid!='all') ? " AND (ar_category = '$categoryid')" : '';
        $query .= $excludethisid ? " AND (articleid != '$excludethisid')" : '';
        $query .= $excludethisurl ? " AND (ar_url != '$excludethisurl')" : '';
        $query .= $shownumcomments ? " GROUP BY articleid" : '';
        $query .= " ORDER BY " . ($sortby ? $sortby : "ar_date DESC, articleid DESC");
        $query .= $num ? " LIMIT $start,$num" : '';
        $articles = Jojo::selectQuery($query);
        foreach ($articles as &$a){
            $a['id']           = $a['articleid'];
            $a['title']        = htmlspecialchars($a['ar_title'], ENT_COMPAT, 'UTF-8', false);
            $a['bodyplain']    = strip_tags($a['ar_body']);
            $a['date']         = Jojo::strToTimeUK($a['ar_date']);
            $a['datefriendly'] = Jojo::mysql2date($a['ar_date'], "medium");
            $a['url']          = Jojo_Plugin_Jojo_article::getArticleUrl($a['articleid'], $a['ar_url'], $a['ar_title'], $a['ar_language'], ($_CATEGORIES ? $a['ar_category'] : '') );
            $a['category']     = ($_CATEGORIES && !empty($a['pg_menutitle'])) ? $a['pg_menutitle'] : $a['pg_title'];
            $a['categoryurl']  = ($_CATEGORIES && !empty($a['ac_url'])) ? (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($language, true) : '') . $a['ac_url'] . '/' : '';
            if(!$shownumcomments) $a['numcomments'] = 0;
            //$a['numcomments']  = $shownumcomments ? $a['numcomments'] : 0;
        }
        return $articles;
    }

    /*
     * calculates the URL for the article - requires the article ID, but works without a query if given the URL or title from a previous query
     *
     */
    static function getArticleUrl($articleid=false, $url=false, $title=false, $language=false, $categoryid=false )
    {
        if (_MULTILANGUAGE) {
            $language = !empty($language) ? $language : Jojo::getOption('multilanguage-default', 'en');
            $mldata = Jojo::getMultiLanguageData();
            $lclanguage = $mldata['longcodes'][$language];
        }

        /* URL specified */
        if (!empty($url)) {
        	$category = Jojo::selectRow("SELECT ar_category FROM article WHERE ar_url = ? ORDER BY ar_category = ?", array($url, $categoryid));
            $fullurl = (_MULTILANGUAGE ? Jojo::getMultiLanguageString($language, false) : '') . self::_getPrefix('article', $language, $category['ar_category']) . '/' . $url . '/';
            return $fullurl;
         }
        /* ArticleID + title specified */
        if ($articleid && !empty($title)) {
        	$category = Jojo::selectRow("SELECT ar_category FROM article WHERE articleid = ? ORDER BY ar_category = ?", array($articleid, $categoryid));
            $fullurl = (_MULTILANGUAGE ? Jojo::getMultiLanguageString($language, false) : '') . self::_getPrefix('article', $language, $categoryid) . '/' . $articleid . '/' .  Jojo::cleanURL($title) . '/';
            return $fullurl;
        }
        /* use the article ID to find either the URL or title */
        if ($articleid) {
            $article = Jojo::selectRow("SELECT ar_url, ar_title, ar_language, ar_category FROM {article} WHERE articleid = ?", $articleid);
            if (count($article)) {
                return self::getArticleUrl($articleid, $article['ar_url'], $article['ar_title'], $article['ar_language'], $article['ar_category']);
            }
         }
        /* No article matching the ID supplied or no ID supplied */
        return false;
    }


    function _getContent()
    {
        global $smarty, $_USERGROUPS, $_USERID;
        $content = array();
        $language = !empty($this->page['pg_language']) ? $this->page['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
        $mldata = Jojo::getMultiLanguageData();
        $lclanguage = $mldata['longcodes'][$language];

        $commentsubscriptions = Jojo::getOption('article_comment_subscriptions', 'no');
        $commentsubscriptions = ($commentsubscriptions == 'yes') ? true : false;

        if ($commentsubscriptions) {
            Jojo_Plugin_Jojo_article::processSubscriptionEmails();
        }

        /* Was a comment submitted? */
        if (Jojo::getFormData('submit', false)) {
            Jojo_Plugin_Jojo_article::postComment();
        }

        /* Are we looking at an article or the index? */
        $articleid = Jojo::getFormData('id',        0);
        $url       = Jojo::getFormData('url',      '');
        $action    = Jojo::getFormData('action',   '');
        $category  = Jojo::getFormData('category', '');
        $findby = ($category) ? $category : $this->page['pg_url'];

        /* handle unsubscribes */
        if ($action == 'unsubscribe') {
            $code      = Jojo::getFormData('code',      '');
            $articleid = Jojo::getFormData('articleid', '');
            if (Jojo_Plugin_Jojo_article::removeSubscriptionByCode($code, $articleid)) {
                $content['content'] = 'Subscription removed.<br />';
            } else {
                $content['content'] = 'This unsubscribe link is inactive, or you have already been unsubscribed.<br />';
            }

            $content['content'] .= 'Return to <a href="' . Jojo_Plugin_Jojo_article::getArticleUrl($articleid) . '">article</a>.';
            return $content;
        }

        /* Get category url and id if needed */
        $pg_url = $this->page['pg_url'];
        $_CATEGORIES = (Jojo::getOption('article_enable_categories', 'no') == 'yes') ? true : false ;
        $categorydata =  ($_CATEGORIES) ? Jojo::selectRow("SELECT * FROM {articlecategory} WHERE ac_url = ?", $findby) : '';
        $categoryid = ($_CATEGORIES && count($categorydata)) ? $categorydata['articlecategoryid'] : 0;
        $sortby = ($_CATEGORIES && count($categorydata)) ? $categorydata['sortby'] : '';
		// For some reason the page url gets set wrong.
		$smarty->assign('pg_url', $categorydata['pc_url']);

        $articles = Jojo_Plugin_Jojo_article::getArticles('', '', $categoryid, $sortby);

        if ($articleid || !empty($url)) {

            /* find the current, next and previous profiles */
            $article = '';
            $prevarticle = array();
            $nextarticle = array();
            $next = false;
            foreach ($articles as $a) {
                if (!_MULTILANGUAGE && !empty($url) && $url==$a['ar_url']) {
                    $article = $a;
                    $next = true;
               } elseif (_MULTILANGUAGE && !empty($url) && $url==$a['ar_url'] && $language==$a['ar_language']) {
                    $article = $a;
                    $next = true;
               } elseif ($articleid==$a['articleid']) {
                    $article = $a;
                    $next = true;
                } elseif ($next==true) {
                    $nextarticle = $a;
                     break;
                } else {
                    $prevarticle = $a;
                }
            }

            /* If the article can't be found, return a 404 */
            if (!$article) {
                include(_BASEPLUGINDIR . '/jojo_core/404.php');
                exit;
            }

            /* Get the specific article */
            $articleid = $article['articleid'];
            $article['ar_datefriendly'] = Jojo::mysql2date($article['ar_date'], "long");
            $article['fullurl'] = Jojo_Plugin_Jojo_article::getArticleUrl($article['articleid'], $article['ar_url'], $article['ar_title'], $article['ar_language'], $article['ar_category']);

            /* assign user variables for pre-populating fields for logged in users */
            if (!empty($_USERID)) {
                $user = Jojo::selectRow("SELECT userid, us_login, us_firstname, us_lastname, us_email, us_website FROM {user} WHERE userid = ?", array($_USERID));
                $user['isadmin'] = in_array('admin', $_USERGROUPS) ? true : false;
                if (empty($_SESSION['name']) && (isset($user['us_firstname']) || isset($user['us_lastname']))) {
                    $_SESSION['name'] = (isset($user['us_firstname']) ? $user['us_firstname'] : '') . ' ' . (isset($user['us_lastname']) ? $user['us_lastname'] : '');
                } elseif (empty($_SESSION['name'])) {
                    $_SESSION['name'] = $user['us_login'];
                }
                if (empty($_SESSION['email']) && isset($user['us_email']))   $_SESSION['email'] = $user['us_email'];
                if (empty($_SESSION['website']) && isset($user['website'])) $_SESSION['website'] = $user['website'];

                if ($commentsubscriptions && Jojo_Plugin_Jojo_article::isSubscribed($_USERID, $articleid)) {
                    $user['email_subscribe'] = true;
                    Jojo_Plugin_Jojo_article::markSubscriptionsViewed($_USERID, $articleid);
                }
                $smarty->assign('article_user', $user);
            }

            /* calculate the next and previous articles */
            if (Jojo::getOption('article_next_prev') == 'yes') {
                if (!empty($nextarticle)) {
                    $nextarticle['url'] = Jojo_Plugin_Jojo_article::getArticleUrl($nextarticle['articleid'], $nextarticle['ar_url'], $nextarticle['ar_title'], $nextarticle['ar_language'], $nextarticle['ar_category']);
                    $smarty->assign('nextarticle', $nextarticle);
                }

                if (!empty($prevarticle)) {
                    $prevarticle['url'] = Jojo_Plugin_Jojo_article::getArticleUrl($prevarticle['articleid'], $prevarticle['ar_url'], $prevarticle['ar_title'], $prevarticle['ar_language'], $prevarticle['ar_category']);
                    $smarty->assign('prevarticle', $prevarticle);
                }
            }

            /* Ensure the tags class is available */
            if (class_exists('Jojo_Plugin_Jojo_Tags')) {
                /* Split up tags for display */
                $tags = Jojo_Plugin_Jojo_Tags::getTags('jojo_article', $articleid);
                $smarty->assign('tags', $tags);

                /* generate tag cloud of tags belonging to this article */
                $article_tag_cloud_minimum = Jojo::getOption('article_tag_cloud_minimum');
                if (!empty($article_tag_cloud_minimum) && ($article_tag_cloud_minimum < count($tags))) {
                    $itemcloud = Jojo_Plugin_Jojo_Tags::getTagCloud('', $tags);
                    $smarty->assign('itemcloud', $itemcloud);
                }
            }

            /* Calculate whether the article has expired or not */
            $now = strtotime('now');
            if (($now < $article['ar_livedate']) || (($now > $article['ar_expirydate']) && ($article['ar_expirydate'] > 0)) ) {
                $this->expired = true;
            }

            /* Get Comments */
            if (Jojo::getOption('articlecomments') == 'yes') {
                $articlecomments = Jojo::selectQuery("SELECT * FROM {articlecomment} WHERE ac_articleid = ? ORDER BY ac_timestamp", array($articleid));
                $smarty->assign('jojo_articlecomments', $articlecomments);
                $articlecommentsenabled = !empty($article['ar_comments']) ? Jojo::yes2True($article['ar_comments']) : true;
                $articlecommentsenabled = Jojo::applyFilter('jojo_article:comments_enabled', $articlecommentsenabled);
                $smarty->assign('jojo_articlecommentsenabled', $articlecommentsenabled);
            }

            /* Calculate URL to POST comments to */
            $smarty->assign('jojo_articleposturl', $article['fullurl']);

            /* Add article breadcrumb */
            $breadcrumbs                      = $this->_getBreadCrumbs();
            $breadcrumb                       = array();
            $breadcrumb['name']               = $article['title'];
            $breadcrumb['rollover']           = $article['ar_desc'];
            $breadcrumb['url']                = $article['fullurl'];
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;

            /* Remember user fields from session */
            if (!empty($_SESSION['name'])) {
                $smarty->assign('name', $_SESSION['name']);
            }
            if (!empty($_SESSION['email'])) {
                $smarty->assign('email', $_SESSION['email']);
            }
            if (!empty($_SESSION['website'])) {
                $smarty->assign('website', $_SESSION['website']);
            }
            if (!empty($_SESSION['anchortext'])) {
                $smarty->assign('anchortext', $_SESSION['anchortext']);
            }

            /* If a file called post-comment.gif exists, use this instead of a text link */
            foreach (Jojo::listPlugins('images/post-comment.gif') as $pluginfile) {
                $smarty->assign('commentbutton', true);
            }

            /* Calculate if user is admin or not. Admins can edit comments */
            if ($this->perms->hasPerm($_USERGROUPS, 'edit')) {
                $smarty->assign('editperms', true);
            }

            /* Assign article content to Smarty */
            $smarty->assign('jojo_article', $article);

            /* Prepare fields for display */
            if (isset($article['ar_htmllang'])) {
                // Override the language setting on this page if necessary.
                $content['pg_htmllang'] = $article['ar_htmllang'];
                $smarty->assign('pg_htmllang', $article['ar_htmllang']);
            }
            $content['title']            = $article['title'];
            $content['seotitle']         = Jojo::either($article['ar_seotitle'], $article['title']);
            $content['breadcrumbs']      = $breadcrumbs;
            if (!empty($article['ar_metadesc'])) {
                $content['meta_description'] = $article['ar_metadesc'];
            } else {
                $meta_description_template = Jojo::getOption('article_meta_description', '[article], an article on [site] - Read all about [article] here.');
                $content['meta_description'] = str_replace(array('[article]', '[site]'), array($article['title'], _SITETITLE), $meta_description_template);
            }
            $content['metadescription']  = $content['meta_description'];
        } else {
            /* Article index section */

            // Snip the article for the index description
            foreach ($articles as $key => $article) {
              $article = Jojo::iExplode('[[snip]]', $article['bodyplain']);
              $articles[$key]['bodyplain'] = $article[0];
            }

            $pagenum = Jojo::getFormData('pagenum', 1);
            if ($pagenum[0] == 'p') {
                $pagenum = substr($pagenum, 1);
            }

            $smarty->assign('article','');
            $articlesperpage = Jojo::getOption('articlesperpage', 40);
            $start = ($articlesperpage * ($pagenum-1));

            /* get number of articles for pagination */
            $now = strtotime('now');
            $numarticles = count($articles);
            $numpages = ceil($numarticles / $articlesperpage);
            /* calculate pagination */
            if ($numpages == 1) {
                $pagination = '';
            } elseif ($numpages == 2 && $pagenum == 2) {
                $pagination = sprintf('<a href="%s/p1/">previous...</a>', (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($language, false) : '') . Jojo_Plugin_Jojo_article::_getPrefix('article', (_MULTILANGUAGE ? $language : ''), (!empty($categoryid) ? $categoryid : '')) );
            } elseif ($numpages == 2 && $pagenum == 1) {
                $pagination = sprintf('<a href="%s/p2/">more...</a>', (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($language, false) : '') . Jojo_Plugin_Jojo_article::_getPrefix('article', (_MULTILANGUAGE ? $language : ''), ($_CATEGORIES ? $categoryid : '')) );
            } else {
                $pagination = '<ul>';
                for ($p=1;$p<=$numpages;$p++) {
                    $url = (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($language, false) : '') . Jojo_Plugin_Jojo_article::_getPrefix('article', (_MULTILANGUAGE ? $language : ''), (!empty($categoryid) ? $categoryid : '')) . '/';
                    if ($p > 1) {
                        $url .= 'p' . $p . '/';
                    }
                    if ($p == $pagenum) {
                        $pagination .= '<li>&gt; Page '.$p.'</li>'. "\n";
                    } else {
                        $pagination .= '<li>&gt; <a href="'.$url.'">Page '.$p.'</a></li>'. "\n";
                    }
                }
                $pagination .= '</ul>';
            }
            $smarty->assign('pagination',$pagination);
            $smarty->assign('pagenum',$pagenum);
            if (_MULTILANGUAGE) {
                $smarty->assign('multilangstring', Jojo::getMultiLanguageString($language));
            }

            /* clear the meta description to avoid duplicate content issues */
            $content['metadescription'] = '';

            /* get article content and assign to Smarty */
            $articles = array_slice($articles, $start, $articlesperpage);
            $smarty->assign('jojo_articles', $articles);

            $content['content'] = $smarty->fetch('jojo_article_index.tpl');
            return $content;

        }

        /* get related articles if tags plugin installed and option enabled */
        $numrelated = Jojo::getOption('article_num_related');
        if ($numrelated && class_exists('Jojo_Plugin_Jojo_Tags')) {
            $related = Jojo_Plugin_Jojo_Tags::getRelated('jojo_article', $articleid, $numrelated, 'jojo_article'); //set the last argument to 'jojo_article' to restrict results to only articles
            $smarty->assign('related', $related);
        }

        $content['content'] = $smarty->fetch('jojo_article.tpl');

        return $content;
    }

    static function admin_action_after_save()
    {
        Jojo::updateQuery("UPDATE {option} SET `op_value`=? WHERE `op_name`='article_last_updated'", time());
        return true;
    }

    public static function sitemap($sitemap)
    {
        /* See if we have any article sections to display and find all of them */
        $articleindexes = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link = 'Jojo_Plugin_Jojo_article' AND pg_sitemapnav = 'yes'");
        if (!count($articleindexes)) {
            return $sitemap;
        }

        if (Jojo::getOption('article_inplacesitemap', 'separate') == 'separate') {
            /* Remove any existing links to the articles section from the page listing on the sitemap */
            foreach($sitemap as $j => $section) {
                $sitemap[$j]['tree'] = Jojo_Plugin_Jojo_article::_sitemapRemoveSelf($section['tree']);
            }
            $_INPLACE = false;
        } else {
            $_INPLACE = true;
        }

        $now = strtotime('now');
        $limit = 15;
        $articlesperpage = Jojo::getOption('articlesperpage', 40);
        $limit = ($articlesperpage >= 15) ? 15 : $articlesperpage ;
         /* Make sitemap trees for each articles instance found */
        foreach($articleindexes as $k => $i){
            /* Get language and language longcode if needed */
            if (_MULTILANGUAGE) {
                $language = !empty($i['pg_language']) ? $i['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $mldata = Jojo::getMultiLanguageData();
                $lclanguage = $mldata['longcodes'][$language];
            }
            /* Get category url and id if needed */
            $pg_url = $i['pg_url'];
            $_CATEGORIES = (Jojo::getOption('article_enable_categories', 'no') == 'yes') ? true : false ;
            $categorydata =  ($_CATEGORIES) ? Jojo::selectRow("SELECT articlecategoryid FROM {articlecategory} WHERE `ac_url` = '$pg_url'") : '';
            $categoryid = ($_CATEGORIES && count($categorydata)) ? $categorydata['articlecategoryid'] : '';

            /* Create tree and add index and feed links at the top */
            $articletree = new hktree();
            $indexurl = (_MULTILANGUAGE) ? Jojo::getMultiLanguageString($language, false) . Jojo_Plugin_Jojo_article::_getPrefix('article', $language, $categoryid) . '/' : Jojo_Plugin_Jojo_article::_getPrefix('article', '', $categoryid) . '/' ;
            if ($_INPLACE) {
                $parent = 0;
            } else {
               $articletree->addNode('index', 0, $i['pg_title'] . ' Index', $indexurl);
               $parent = 'index';
            }

            /* Get the article content from the database */
            $query =  "SELECT * FROM {article} WHERE ar_livedate<$now AND (ar_expirydate<=0 OR ar_expirydate>$now)";
            $query .= (_MULTILANGUAGE) ? " AND (ar_language = '$language')" : '';
            $query .= ($_CATEGORIES) ? " AND (ar_category = '$categoryid')" : '';
            $query .= " ORDER BY ar_date DESC LIMIT $limit";

            $articles = Jojo::selectQuery($query);
            $n = count($articles);
            foreach ($articles as $a) {
                $articletree->addNode($a['articleid'], $parent, $a['ar_title'], Jojo_Plugin_Jojo_article::getArticleUrl($a['articleid'], $a['ar_url'], $a['ar_title'], $a['ar_language'], $a['ar_category']));
            }

            /* Get number of articles for pagination */
            $countquery =  "SELECT COUNT(*) AS numarticles FROM {article} WHERE ar_livedate<$now AND (ar_expirydate<=0 OR ar_expirydate>$now)";
            $countquery .= (_MULTILANGUAGE) ? " AND (ar_language = '$language')" : '';
            $countquery .= ($_CATEGORIES) ? " AND (ar_category = '$categoryid')" : '';
            $articlescount = Jojo::selectQuery($countquery);
            $numarticles = $articlescount[0]['numarticles'];
            $numpages = ceil($numarticles / $articlesperpage);

            /* calculate pagination */
            if ($numpages == 1) {
                if ($limit < $numarticles) {
                    $articletree->addNode('p1', $parent, 'More ' . $i['pg_title'] , $indexurl );
                }
            } else {
                for ($p=1; $p <= $numpages; $p++) {
                    if (($limit < $articlesperpage) && ($p == 1)) {
                        $articletree->addNode('p1', $parent, '...More' , $indexurl );
                    } elseif ($p != 1) {
                        $url = $indexurl .'p' . $p .'/';
                        $nodetitle = $i['pg_title'] . ' Index - p'. $p;
                        $articletree->addNode('p' . $p, $parent, $nodetitle, $url);
                    }
                }
            }

            /* Check for child pages of the plugin page */
            foreach (Jojo::selectQuery("SELECT * FROM {page} WHERE pg_parent = '" . $i['pageid'] . "' AND pg_sitemapnav = 'yes'") as $c) {
                /* Check whether an RSS Feed page exists and is to be shown on the sitemap, and if so, add it to the sitemap array */
                if ($c['pg_link']=='Jojo_Plugin_Jojo_article_rss') {
                    $rssurl = ((_MULTILANGUAGE) ? Jojo::getMultiLanguageString($language, false) : '') . Jojo_Plugin_Jojo_article::_getPrefix('rss', ((_MULTILANGUAGE) ? $language : ''), $categoryid) . '/';
                    $articletree->addNode('index-rss', $parent, $c['pg_title'], $rssurl);
                } else {
                    $articletree->addNode($c['pageid'], $parent, $c['pg_title'], $c['pg_url'] . '/');
                }
            }

            /* Add to the sitemap array */
            if ($_INPLACE) {
                /* Add inplace */
                $url = ((_MULTILANGUAGE) ? Jojo::getMultiLanguageString ( $language, false ) : '') . Jojo_Plugin_Jojo_article::_getPrefix('article', ((_MULTILANGUAGE) ? $language : ''), $categoryid) . '/';
                $sitemap['pages']['tree'] = Jojo_Plugin_Jojo_article::_sitemapAddInplace($sitemap['pages']['tree'], $articletree->asArray(), $url);
            } else {
                /* Add to the end */
                $sitemap["articles$k"] = array(
                    'title' => $i['pg_title'] . ( _MULTILANGUAGE ? ' (' . ucfirst($lclanguage) . ')' : ''),
                    'tree' => $articletree->asArray(),
                    'order' => 3 + $k,
                    'header' => '',
                    'footer' => '',
                    );
            }
        }
        return $sitemap;
    }

    static function _sitemapAddInplace($sitemap, $toadd, $url)
    {
        foreach ($sitemap as $k => $t) {
            if ($t['url'] == $url) {
                $sitemap[$k]['children'] = $toadd;
            } elseif (isset($sitemap[$k]['children'])) {
                $sitemap[$k]['children'] = Jojo_Plugin_Jojo_article::_sitemapAddInplace($t['children'], $toadd, $url);
            }
        }
        return $sitemap;
    }

    static function _sitemapRemoveSelf($tree)
    {
        static $urls;
        $_CATEGORIES = (Jojo::getOption('article_enable_categories', 'no') == 'yes') ? true : false ;

        if (!is_array($urls)) {
            $urls = array();
            $articleindexes = Jojo::selectQuery("SELECT p.*" . ($_CATEGORIES ? ", ac.articlecategoryid" : '') . " FROM {page} p " . ($_CATEGORIES ? "LEFT JOIN {articlecategory} ac ON (pg_url=ac_url) " : '') . "WHERE pg_link = 'Jojo_Plugin_Jojo_article' AND pg_sitemapnav = 'yes'");
            if (count($articleindexes)==0) {
               return $tree;
            }

            foreach($articleindexes as $key => $i){
                $language = !empty($i['pg_language']) ? $i['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $mldata = Jojo::getMultiLanguageData();
                $lclanguage = $mldata['longcodes'][$language];
                $urls[] = ((_MULTILANGUAGE) ? $lclanguage . '/' : '') . Jojo_Plugin_Jojo_article::_getPrefix('article', ((_MULTILANGUAGE) ? $language : ''), ($_CATEGORIES ? $i['articlecategoryid'] : '')) . '/';
                $urls[] = ((_MULTILANGUAGE) ? $lclanguage . '/' : '') . Jojo_Plugin_Jojo_article::_getPrefix('rss', ((_MULTILANGUAGE) ? $language : ''), ($_CATEGORIES ? $i['articlecategoryid'] : '')) . '/';
            }
        }

        foreach ($tree as $k =>$t) {
            if (in_array($t['url'], $urls)) {
                unset($tree[$k]);
            } else {
                $tree[$k]['children'] = Jojo_Plugin_Jojo_article::_sitemapRemoveSelf($t['children']);
            }
        }
        return $tree;
    }

    /**
    /**
     * XML Sitemap filter
     *
     * Receives existing sitemap and adds article pages
     */
    static function xmlsitemap($sitemap)
    {
        /* Get articles from database */
        $articles = Jojo::selectQuery("SELECT * FROM {article} WHERE ar_livedate<".time()." AND (ar_expirydate<=0 OR ar_expirydate>".time().")");

        /* Add articles to sitemap */
        foreach($articles as $a) {
            $url = _SITEURL . '/'. Jojo_Plugin_Jojo_article::getArticleUrl($a['articleid'], $a['ar_url'], $a['ar_title'], $a['ar_language'], $a['ar_category']);
            $lastmod = strtotime($a['ar_date']);
            $priority = 0.6;
            $changefreq = '';
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }

        /* Return sitemap */
        return $sitemap;
    }

    /**
     * Site Search
     *
     */
    static function search($results, $keywords, $language, $booleankeyword_str=false)
    {
        global $_USERGROUPS;
        $_CATEGORIES = (Jojo::getOption('article_enable_categories', 'no') == 'yes') ? true : false ;
        $_TAGS = class_exists('Jojo_Plugin_Jojo_Tags') ? true : false ;
        $pagePermissions = new JOJO_Permissions();
        $boolean = ($booleankeyword_str) ? true : false;
        $keywords_str = ($boolean) ? $booleankeyword_str :  implode(' ', $keywords);
        if ($boolean && stripos($booleankeyword_str, '+') === 0  ) {
            $like = '1';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" AND (ar_body LIKE '%%%s%%' OR ar_title LIKE '%%%s%%')", Jojo::clean($keyword), Jojo::clean($keyword));
            }
        } elseif ($boolean && stripos($booleankeyword_str, '"') === 0) {
            $like = "(ar_body LIKE '%%%". implode(' ', $keywords). "%%' OR ar_title LIKE '%%%". implode(' ', $keywords) . "%%')";
        } else {
            $like = '(0';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" OR ar_body LIKE '%%%s%%' OR ar_title LIKE '%%%s%%'", Jojo::clean($keyword), Jojo::clean($keyword));
            }
            $like .= ')';
        }
        $tagid = ($_TAGS) ? Jojo_Plugin_Jojo_Tags::_getTagId(implode(' ', $keywords)): '';

        $query = "SELECT articleid, ar_url, ar_title, ar_desc, ar_body, ar_image, ar_language, ar_expirydate, ar_livedate, ar_category, ((MATCH(ar_title) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ") * 0.2) + MATCH(ar_title, ar_desc, ar_body) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ")) AS relevance";
        $query .= ", p.pg_url, p.pg_title";
        $query .= " FROM {article} AS article ";
        $query .= $_CATEGORIES ? " LEFT JOIN {articlecategory} ac ON (article.ar_category=ac.articlecategoryid) LEFT JOIN {page} p ON (ac.ac_url=p.pg_url)" : "LEFT JOIN {page} p ON (p.pg_link='jojo_plugin_jojo_article' AND p.pg_language=ar_language)";
        $query .= " LEFT JOIN {language} AS language ON (article.ar_language = languageid)";
        $query .= $tagid ? " LEFT JOIN {tag_item} AS tag ON (tag.itemid = article.articleid AND tag.plugin='jojo_article' AND tag.tagid = $tagid)" : '';
        $query .= " WHERE ($like";
        $query .= $tagid ? " OR (tag.itemid = article.articleid AND tag.plugin='jojo_article' AND tag.tagid = $tagid))" : ')';
        $query .= ($language) ? " AND ar_language = '$language' " : '';
        $query .= " AND language.active = 'yes' ";
        $query .= " AND ar_livedate<" . time() . " AND (ar_expirydate<=0 OR ar_expirydate>" . time() . ") ";
        $query .= " ORDER BY relevance DESC LIMIT 100";

        $data = Jojo::selectQuery($query, array($keywords_str, $keywords_str));

        if (_MULTILANGUAGE) {
            global $page;
            $mldata = Jojo::getMultiLanguageData();
            $homes = $mldata['homes'];
        } else {
            $homes = array(1);
        }

        foreach ($data as $d) {
            $pagePermissions->getPermissions('article', $d['articleid']);
            if (!$pagePermissions->hasPerm($_USERGROUPS, 'view')) {
                continue;
            }
            $result = array();
            $result['relevance'] = $d['relevance'];
            $result['title'] = $d['ar_title'];
            $result['body'] = $d['ar_body'];
            $result['image'] = 'articles/' . $d['ar_image'];
            $result['url'] = Jojo_Plugin_Jojo_article::getArticleUrl($d['articleid'], $d['ar_url'], $d['ar_title'], $d['ar_language'], $d['ar_category']);
            $result['absoluteurl'] = _SITEURL. '/' . $result['url'];
            $result['id'] = $d['articleid'];
            $result['plugin'] = 'jojo_article';
            $result['type'] = $d['pg_title'] ? $d['pg_title'] : 'Articles';

            if ($_TAGS) {
                $result['tags'] = Jojo_Plugin_Jojo_Tags::getTags('jojo_article', $d['articleid']);
                if ($result['tags'] && array_search(implode(' ', $keywords), $result['tags']) !== false) $result['relevance'] = $result['relevance'] + 1 ;
            }
            $results[] = $result;
        }


        /* Return results */
        return $results;
    }


   /**
     * RSS Icon filter
     * Places the RSS feed icon in the head of the document, sitewide
     */
    static function rssicon($data)
    {
        $link = Jojo::getOption('article_external_rss');
        $data['Articles'] = !empty($link) ? $link : _SITEURL . '/' . Jojo_Plugin_Jojo_article::_getPrefix('rss');

        /* add category RSS feed */
        $pg_url = _SITEURI;
        $_CATEGORIES = (Jojo::getOption('article_enable_categories', 'no') == 'yes') ? true : false ;
        $categorydata =  ($_CATEGORIES) ? Jojo::selectRow("SELECT articlecategoryid FROM {articlecategory} WHERE ac_url = '$pg_url'") : '';
        $categoryid = ($_CATEGORIES && count($categorydata)) ? $categorydata['articlecategoryid'] : '';

        if ( $_CATEGORIES && !empty($categoryid)) {
            $data['Articles - '.$pg_url] = _SITEURL . '/' . Jojo_Plugin_Jojo_article::_getPrefix('rss', false, $categoryid);
        }
        return $data;
    }

    /**
     * Remove Snip
     *
     * Removes any [[snip]] tags leftover in the content before outputting
     */
    static function removesnip($data)
    {
        $data = str_ireplace('[[snip]]','',$data);
        return $data;
    }



    /**
     * Get the url prefix for a particular part of this plugin
     */
    static function _getPrefix($for='article', $language=false, $categoryid=false) {
        $cacheKey = $for;
        $cacheKey .= ($language) ? $language : 'false';
        $cacheKey .= ($categoryid) ? $categoryid : 'false';

        /* Have we got a cached result? */
        static $_cache;
        if (isset($_cache[$cacheKey])) {
            return $_cache[$cacheKey];
        }

        if (!in_array($for, array('article', 'admin', 'rss'))) {
            return '';
        }
        /* Cache some stuff */
        $language = $language ? $language : Jojo::getOption('multilanguage-default', 'en');
        $_CATEGORIES = (Jojo::getOption('article_enable_categories', 'no') == 'yes') ? true : false ;
        $categorydata =  ($_CATEGORIES && $categoryid) ? Jojo::selectRow("SELECT `ac_url` FROM {articlecategory} WHERE `articlecategoryid` = '$categoryid';") : '';
        $category = ($_CATEGORIES && $categoryid) ? $categorydata['ac_url'] : '';
        $query = "SELECT pageid, pg_title, pg_url FROM {page} WHERE pg_link = ?";
        $query .= (_MULTILANGUAGE) ? " AND pg_language = '$language'" : '';
        $query .= $category ? " AND pg_url LIKE '%$category'": '';

        if ($for == 'article') {
            $values = array('Jojo_Plugin_Jojo_article');
        } elseif ($for == 'admin') {
            $values = array('Jojo_Plugin_Jojo_article_admin');
        } elseif ($for == 'rss') {
            $query = "SELECT pageid, pg_title, pg_url FROM {page} WHERE pg_link = ?";
            $query .= (_MULTILANGUAGE) ? " AND pg_language = '$language'" : '';
            $query .= (!empty($category)) ? " AND pg_url LIKE '$category%'": '';
            $values = array('Jojo_Plugin_Jojo_article_rss');
        }

        $res = Jojo::selectRow($query, $values);
        if ($res) {
            $_cache[$cacheKey] = !empty($res['pg_url']) ? $res['pg_url'] : $res['pageid'] . '/' . $res['pg_title'];
        } else {
            $_cache[$cacheKey] = '';
        }
        return $_cache[$cacheKey];
    }

    function getCorrectUrl()
    {
        global $page;
        $language  = $page->page['pg_language'];
        $pg_url    = $page->page['pg_url'];
        $articleid = Jojo::getFormData('id',     0);
        $url       = Jojo::getFormData('url',    '');
        $action    = Jojo::getFormData('action', '');
        $pagenum   = Jojo::getFormData('pagenum', 1);
		$category  = Jojo::getFormData('category', '');

        $data = array('ar_category' => '');
        if (Jojo::getOption('article_enable_categories', 'no') == 'yes') {
            $data = Jojo::selectRow("SELECT articlecategoryid FROM {articlecategory} WHERE ac_url=?", $category);
        }
        $categoryid = !empty($data['articlecategoryid']) ? $data['articlecategoryid'] : '';

        if ($pagenum[0] == 'p') {
            $pagenum = substr($pagenum, 1);
        }

        /* approving and deleting comments */
        if ($action == 'admin') return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        /* unsubscribing */
        if ($action == 'unsubscribe') return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        /* the special URL for the latest article */
        if ($action == 'latest') {
            $article = Jojo::selectRow("SELECT * FROM {article} ORDER BY ar_date DESC, ar_title");
            return _SITEURL . '/' . Jojo_Plugin_Jojo_article::getArticleUrl($article['articleid'], $article['ar_url'], $article['ar_title'], $article['ar_language'], $article['ar_category']);
        }
        $correcturl = Jojo_Plugin_Jojo_article::getArticleUrl($articleid, $url, null, $language, $categoryid);
        if ($correcturl) {
            return _SITEURL . '/' . $correcturl;
        }

        /* article index with pagination */
        if ($pagenum > 1) return parent::getCorrectUrl() . 'p' . $pagenum . '/';

        /* article index - default */
        return parent::getCorrectUrl();
    }

    static public function isArticleUrl($uri)
    {
        $prefix = false;
        $getvars = array();
        /* Check the suffix matches and extra the prefix */
        if (preg_match('#^(.+)/latest$#', $uri, $matches)) {
            /* "$prefix/[action:latest]" eg "articles/latest/" */
            $prefix = $matches[1];
            $getvars = array('action' => 'latest');
        } elseif (preg_match('#^(.+)/unsubscribe/([0-9]+)/([a-zA-Z0-9]{16})$#', $uri, $matches)) {
            /* "$prefix/[action:unsubscribe]/[articleid:integer]/[code:[a-zA-Z0-9]{16}]" eg "articles/unsubscribe/34/7MztlFyWDEKiSoB1/" */
            $prefix = $matches[1];
            $getvars = array(
                        'action' => 'unsubscribe',
                        'articleid' => $matches[2],
                        'code' => $matches[3]
                        );
        } elseif (preg_match('#^(.+)/([0-9]+)/([^/]+)$#', $uri, $matches)) {
            /* "$prefix/[id:integer]/[string]" eg "articles/123/name-of-article/" */
            $prefix = $matches[1];
            $getvars = array(
                        'id' => $matches[2],
                        'category' => $prefix
                        );
        } elseif (preg_match('#^(.+)/([0-9]+)$#', $uri, $matches)) {
            /* "$prefix/[id:integer]" eg "articles/123/" */
            $prefix = $matches[1];
            $getvars = array(
                        'id' => $matches[2],
                        'category' => $prefix
                        );
        } elseif (preg_match('#^(.+)/p([0-9]+)$#', $uri, $matches)) {
            /* "$prefix/p[pagenum:([0-9]+)]" eg "articles/p2/" for pagination of articles */
            $prefix = $matches[1];
            $getvars = array(
                        'pagenum' => $matches[2],
                        'category' => $prefix
                        );
        } elseif (preg_match('#^(.+)/((?!rss)([a-z0-9-_]+))$#', $uri, $matches)) {
            /* "$prefix/[url:((?!rss)string)]" eg "articles/name-of-article/" ignoring "artciles/rss" */
            $prefix = $matches[1];
            $getvars = array(
                        'url' => $matches[2],
                        'category' => $prefix
                        );
            $row = Jojo::selectRow("SELECT articlecategoryid FROM {articlecategory} WHERE ac_url LIKE ?", $uri);
            if ($row) return false;
        } else {
            /* Didn't match */
            return false;
        }

        /* Check the prefix matches */
        if ($res = Jojo_Plugin_Jojo_article::checkPrefix($prefix)) {
            /* The prefix is good, pass through uri parts */
            foreach($getvars as $k => $v) {
                $_GET[$k] = $v;
            }

            return true;
        }
        return false;
    }

    /**
     * Check if a prefix is an article prefix
     */
    static public function checkPrefix($prefix)
    {
        static $_prefixes, $languages, $categories;
        if (!isset($languages)) {
            /* Initialise cache */
            if (Jojo::tableExists('lang_country')) {
                $languages = Jojo::selectAssoc("SELECT lc_code, lc_code as lc_code2 FROM {lang_country}");
            } else {
                $languages = Jojo::selectAssoc("SELECT languageid, languageid as languageid2 FROM {language} WHERE active = 'yes'");
            }
            $categories = array(false);
            if (Jojo::getOption('article_enable_categories', 'no') == 'yes') {
                $categories = array_merge($categories, Jojo::selectAssoc("SELECT articlecategoryid, articlecategoryid as articlecategoryid2 FROM {articlecategory}"));
            }
            $_prefixes = array();
        }
        /* Check if it's in the cache */
        if (isset($_prefixes[$prefix])) {
            return $_prefixes[$prefix];
        }
        /* Check everything */
        foreach ($languages as $language) {
            $language = $language ? $language : Jojo::getOption('multilanguage-default', 'en');
            foreach($categories as $category) {
                $testPrefix = Jojo_Plugin_Jojo_article::_getPrefix('article', $language, $category);
                $_prefixes[$testPrefix] = true;
                if ($testPrefix == $prefix) {
                    /* The prefix is good */
                    return true;
                }
            }
        }

        /* Didn't match */
        $_prefixes[$testPrefix] = false;
        return false;
    }

    // Sync the articlecategory data over to the page table
    static function admin_action_after_save_articlecategory() {
   	    self::sync_articlecategory_to_page();
    }

    // Sync the articlecategory data over from the page table
    static function admin_action_after_save_page() {
   	    self::sync_page_to_articlecategory();
    }

    static function sync_articlecategory_to_page() {
        // Get the list of categories
        $categories = jojo::selectQuery("SELECT * FROM {articlecategory}");
        // And the list of pages
        $pages = jojo::selectQuery(
            "SELECT pageid, pg_url FROM {page} WHERE pg_link LIKE ?",
            'jojo_plugin_jojo_article'
        );
        // Set the array keys as the primary keys, I don't know why the system doesn't already do it, sigh
        if ($pages) {
            foreach ($pages as $id => $pagedata) {
                $pages[$pagedata['pageid']] = $pagedata;
                unset($pages[$id]);
            }
        }
        if (!$categories) { return false; }

        foreach ($categories as $category) {
            $newpageid = 0;
            // Page ID exists as well as the corrosponding page
            if ($category['ac_pageid'] && $pages[$category['ac_pageid']]) {
                // Does the url need updating?
                if ($pages[$category['ac_pageid']]['pg_url'] != $category['ac_url']) {
                    // Only thing to update is the URL
                    jojo::updateQuery(
                        "UPDATE {page} SET pg_url = ? WHERE pageid = ?",
                        array($category['ac_url'], $category['ac_pageid'])
                    );
                }
            } else {
                // There's no page set for this category, first check if the page exists, since it might already
                foreach ($pages as $pageid => $page) {
                    if ($page['pg_url'] == $category['ac_url']) {
                        $newpageid = $page['pageid'];
                    }
                }
                // If there's no page currently, so we'll have to create one
                if (!$newpageid) {
                    $newpageid = Jojo::insertQuery(
                        "INSERT INTO {page} SET pg_title = ?, pg_link = ?, pg_url = ?, pg_parent = ?",
                        array(
                            $category['ac_url'],  // Title
                            'Jojo_Plugin_Jojo_article',  // Link
                            $category['ac_url'],  // URL
                            0  // Parent - don't do anything smart, just put it at the top level for now
                        )
                    );
                }
                // If we successfully added the page, now lets update the product category
                if ($newpageid) {
                    jojo::updateQuery(
                        "UPDATE {articlecategory} SET ac_pageid = ?, ac_url = ? WHERE articlecategoryid = ?",
                        array(
                            $newpageid,
                            $category['ac_url'],
                            $category['articlecategoryid']
                        )
                    );
                }
            }
        }
        return true;
    }

    static function sync_page_to_articlecategory() {
        // Get the list of categories
        $categories = jojo::selectQuery("SELECT * FROM {articlecategory}");
        if (!$categories) { return false; }
        // And the list of pages
        $pages = jojo::selectQuery(
            "SELECT pageid, pg_url FROM {page} WHERE pg_link LIKE ?",
            'jojo_plugin_jojo_article'
        );
        // Set the array keys as the primary keys, I don't know why the system doesn't already do it, sigh
        if ($pages) {
            foreach ($pages as $id => $pagedata) {
                $pages[$pagedata['pageid']] = $pagedata;
                unset($pages[$id]);
            }
        }

        foreach ($categories as $category) {
            $newpageid = 0;
            // Page ID exists, as does the Page
            if ($category['ac_pageid'] && $pages[$category['ac_pageid']]) {
                // Does the url need updating?
                if ($pages[$category['ac_pageid']]['pg_url'] != $category['ac_url']) {
                    // Only thing to update is the URL
                    jojo::updateQuery(
                        "UPDATE {articlecategory} SET ac_url = ? WHERE articlecategoryid = ?",
                        array($pages[$category['ac_pageid']]['pg_url'], $category['articlecategoryid'])
                    );
                }
            }
            // No handling of missing or unlinked article categories here. If it's broken, fix it manually.
        }
        return true;
    }
}
