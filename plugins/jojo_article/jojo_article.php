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
    static function postComment($article)
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
        $captchacode     = Jojo::getFormData('captchacode',     '');
        if (!empty($website)) $website = Jojo::addhttp($website);

        /* sanitise input */
        $name       = htmlentities($name);
        $anchortext = htmlentities($anchortext);

        /* trim whitespace just in case people copy-paste the email address wrong */
        $email = trim($email);

        if (!$article) return false;
        $articleid       = $article['articleid'];

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
        $message  = "A comment has been added to an article on " . _SITEURL . "/". $article['url'] . "\n\n";
        $message .= "Comment by: " . $name . "\n";
        $message .= $email != '' ? "Email: " . $email. "\n" : '';
        $message .= $website != '' ? "Website: " . $website . "\n" : '';
        $message .= $anchortext != '' ? "Anchor text: " . $anchortext . "\n" : '';
        $message .= "Comment:\n" . $htmlcomment . "\n";

        $message .= "\n\nThis comment is currently live on the site, but has a nofollow link\n\n";

        if (!empty($anchortext)) {
            $message .= "To FOLLOW the link on this comment AND use the chosen anchor text (for great comments), click the following link\n";
            $message .= _SITEURL . '/' . self::_getPrefix('admin') . '/' . $anchortextcode . "/\n\n";
        }
        $message .= "To FOLLOW the link on this comment (for good comments), click the following link\n";
        $message .= _SITEURL . '/' . self::_getPrefix('admin') . '/' . $approvecode . "/\n\n";
        $message .= "To DELETE this comment, click the following link\n";
        $message .= _SITEURL . '/' . self::_getPrefix('admin') . '/' . $deletecode . "/\n";
        $message .= Jojo::emailFooter();

        /* Email comment to webmaster and site contact */
        Jojo::simplemail(_WEBMASTERNAME, _WEBMASTERADDRESS, Jojo::getOption('sitetitle') . ' Article Comment - ' . $article['title'], $message, $name, $email);
        if(_WEBMASTERADDRESS != _CONTACTADDRESS) Jojo::simplemail(_FROMNAME, _CONTACTADDRESS, Jojo::getOption('sitetitle') . ' Article Comment - ' . $article['title'], $message, $name, $email);

        /* add subscription if needed, and update all subscriptions to say the topic has a new comment */
        if ($commentsubscriptions && $email_subscribe && !empty($_USERID)) {
            self::addSubscription($_USERID, $articleid);
        }
        self::markSubscriptionsUpdated($articleid);

        /* log a copy of the comment */
        $log = new Jojo_Eventlog();
        $log->code = 'article comment';
        $log->importance = 'normal';
        $log->shortdesc = 'Article comment by ' . $name . ' on ' . $article['title'];
        $log->desc = $message;
        $log->savetodb();
        unset($log);

        /* Delete cache for this page - forcing regeneration next view */
        if (_CONTENTCACHE) {
            $query = "DELETE FROM {contentcache} WHERE cc_url=? LIMIT 1";
            $values = array(_SITEURL . '/' . $article['url']);
            Jojo::deleteQuery($query, $values);
        }

        /* Redirect back to the article to see the comment on the page */
        header('location: ' . self::getCorrectUrl());
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
        $subscriptions = Jojo::selectQuery("SELECT acs.*, ar_title, ar_url, ar_category, ar_language, us_firstname, us_login, us_email FROM {articlecommentsubscription} acs LEFT JOIN {article} ar ON (acs.articleid=ar.articleid) LEFT JOIN {user} us ON (acs.userid=us.userid) WHERE (lastupdated > lastviewed) AND (lastviewed > lastemailed) LIMIT ?", $limit);
        $from_name = Jojo::either(_FROMNAME, _WEBMASTERNAME);
        $from_email = Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS);
        foreach ($subscriptions as $sub) {
            $subject  = 'New comment notification: ' . $sub['ar_title'];
            $message  = 'A new comment has been added to "' . $sub['ar_title'] . '" on ' . Jojo::getOption('sitetitle') . '. You are subscribed to receive email notifications notifications of any new comments on this post.';
            $message .= "To view the new comments, please visit the following link.\n";
            $message .= _SITEURL.'/'. self::getArticleUrl($sub['articleid'], $sub['ar_url'], $sub['ar_title'], $sub['ar_language'], $sub['ar_category'])."\n\n";
            $message .= "You can unsubscribe from this notification by using the following link.\n";
            $message .= _SITEURL.'/'. self::_getPrefix('article', $sub['ar_language'], $sub['ar_category']) . "/unsubscribe/".$sub['articleid']."/".$sub['code']."/\n";
            $message .= "\nRegards,\n" . Jojo::getOption('webmastername') . "\n" . Jojo::getOption('sitetitle')."\n".Jojo::getOption('fromaddress')."\n";
            if (Jojo::simpleMail($sub['us_login'], $sub['us_email'], $subject, $message, $from_name, $from_email)) {
                Jojo::updateQuery("UPDATE {articlecommentsubscription} SET lastemailed=? WHERE userid=? AND articleid=?", array(time(), $sub['userid'], $sub['articleid']));
           } else {
                /* log a copy of the message */
                $log             = new Jojo_Eventlog();
                $log->code       = 'enquiry';
                $log->importance = 'high';
                $log->shortdesc  = 'Failed comment notification to ' . $sub['us_login'] . ' (' . $sub['us_email'] . ')';
                $log->desc       = $message;
                $log->savetodb();
                unset($log);
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
    static function getArticles($num=false, $start = 0, $categoryid='all', $sortby='ar_date desc', $exclude=false, $usemultilanguage=true) {
        global $page;
        if (_MULTILANGUAGE) $language = !empty($page->page['pg_language']) ? $page->page['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
        if (is_array($categoryid)) {
             $categoryquery = " AND ar_category IN ('" . implode("','", $categoryid) . "')";
        } elseif (is_numeric($categoryid)) {
            $categoryquery = " AND ar_category = '$categoryid'";
        } else {
            $categoryquery = '';
        }
        /* if calling page is an article, Get current articleid, and exclude from the list  */
        $excludethisid = ($exclude && Jojo::getOption('article_sidebar_exclude_current', 'no')=='yes' && $page->page['pg_link']=='jojo_plugin_jojo_article' && Jojo::getFormData('id')) ? Jojo::getFormData('id') : '';
        $excludethisurl = ($exclude && Jojo::getOption('article_sidebar_exclude_current', 'no')=='yes' && $page->page['pg_link']=='jojo_plugin_jojo_article' && Jojo::getFormData('url')) ? Jojo::getFormData('url') : '';
        $shownumcomments = (Jojo::getOption('articlecomments') == 'yes' && Jojo::getOption('article_show_num_comments', 'no') == 'yes') ? true : false;
        // if one article is being excluded up the limit by one
        if ($num && ($excludethisid || $excludethisurl)) $num++;
        $now    = time();
        $query  = "SELECT ar.*, ac.*, pg_menutitle, pg_title, pg_url, pg_status";
        $query .= $shownumcomments ? ", COUNT(acom.ac_articleid) AS numcomments" : '';
        $query .= " FROM {article} ar";
        $query .= " LEFT JOIN {articlecategory} ac ON (ar.ar_category=ac.articlecategoryid) LEFT JOIN {page} p ON (ac.pageid=p.pageid)";
        $query .= $shownumcomments ? " LEFT JOIN {articlecomment} acom ON (acom.ac_articleid = ar.articleid)" : '';
        $query .= " WHERE 1";
        $query .= $categoryquery;
        $query .= (_MULTILANGUAGE && $categoryid == 'all') ? " AND (pg_language = '$language')" : '';
        $query .= $shownumcomments ? " GROUP BY articleid" : '';
        $query .= $num ? " ORDER BY $sortby" : '';
        $query .= $num ? " LIMIT $start,$num" : '';
        $articles = Jojo::selectQuery($query);
        foreach ($articles as $k=>&$a){
            if ($a['ar_livedate']>$now || (!empty($a['ar_expirydate']) && $a['ar_expirydate']<$now) || (!empty($a['articleid']) && $a['articleid']==$excludethisid)  || (!empty($a['ar_url']) && $a['ar_url']==$excludethisurl) || $a['pg_status']=='inactive') {
                unset($articles[$k]);
                continue;
            }
            $a['id']           = $a['articleid'];
            $a['title']        = htmlspecialchars($a['ar_title'], ENT_COMPAT, 'UTF-8', false);
            /* Strip all tags and template include code ie [[ ]] */
            $a['bodyplain'] = preg_replace('/\[\[.*?\]\]/', '',  trim(strip_tags($a['ar_body'])));
            $a['date']         = Jojo::strToTimeUK($a['ar_date']);
            $a['datefriendly'] = Jojo::mysql2date($a['ar_date'], "medium");
            $a['url']          = self::getArticleUrl($a['articleid'], $a['ar_url'], $a['ar_title'], $a['ar_language'], $a['ar_category']);
            $a['category']     = !empty($a['pg_menutitle']) ? htmlspecialchars($a['pg_menutitle'], ENT_COMPAT, 'UTF-8', false) : htmlspecialchars($a['pg_title'], ENT_COMPAT, 'UTF-8', false);
            $a['categoryurl']  = (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($language, true) : '') . (!empty($a['pg_url']) ? $a['pg_url'] : $a['pageid'] . '/' .  Jojo::cleanURL($a['pg_title'])) . '/';
            if(!$shownumcomments) $a['numcomments'] = 0;
            //$a['numcomments']  = $shownumcomments ? $a['numcomments'] : 0;
        }
        if (!$num) {
            $order = "date";
            switch ($sortby) {
              case "ar_date desc":
                $order="date";
                break;
              case "ar_title asc":
                $order="name";
                break;
              case "ar_author":
                $order="author";
                break;
              case "ar_livedate desc":
                $order="live";
                break;
            }
            usort($articles, array('Jojo_Plugin_Jojo_article', $order . 'sort'));
        }
        return $articles;
    }

    private static function namesort($a, $b)
    {
         if ($a['ar_title']) {
            return strcmp($a['ar_title'],$b['ar_title']);
        } 
    }

    private static function authorsort($a, $b)
    {
         if ($a['ar_author']) {
            return strcmp($a['ar_author'],$b['ar_author']);
        }
    }

    private static function datesort($a, $b)
    {
         if ($a['ar_date']) {
            return strcmp($b['ar_date'],$a['ar_date']);
         }
    }

    private static function livesort($a, $b)
    {
         if ($a['ar_livedate']) {
            return strcmp($b['ar_livedate'],$a['ar_livedate']);
         }
    }
    
    /*
     * calculates the URL for the article - requires the article ID, but works without a query if given the URL or title from a previous query
     *
     */
    static function getArticleUrl($id=false, $url=false, $title=false, $language=false, $category=false )
    {
        if (_MULTILANGUAGE) {
            $language = !empty($language) ? $language : Jojo::getOption('multilanguage-default', 'en');
        }

        /* URL specified */
        if (!empty($url)) {
            $fullurl = (_MULTILANGUAGE ? Jojo::getMultiLanguageString($language, false) : '') . self::_getPrefix('article', $language, $category) . '/' . $url . '/';
            return $fullurl;
         }
        /* ArticleID + title specified */
        if ($id && !empty($title)) {
            $fullurl = (_MULTILANGUAGE ? Jojo::getMultiLanguageString($language, false) : '') . self::_getPrefix('article', $language, $category) . '/' . $id . '/' .  Jojo::cleanURL($title) . '/';
          return $fullurl;
        }
        /* use the article ID to find either the URL or title */
        if ($id) {
            $article = Jojo::selectRow("SELECT ar_url, ar_title, ar_language, ar_category FROM {article} WHERE articleid = ?", array($id));
             if ($article) {
                return self::getArticleUrl($id, $article['ar_url'], $article['ar_title'], $article['ar_language'], $article['ar_category']);
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

        if (Jojo::getOption('article_comment_subscriptions', 'no') == 'yes') {
            self::processSubscriptionEmails();
        }

        /* Are we looking at an article or the index? */
        $articleid = Jojo::getFormData('id',        0);
        $url       = Jojo::getFormData('url',      '');
        $action    = Jojo::getFormData('action',   '');
        $pageid = $this->page['pageid'];
        $categorydata =  Jojo::selectRow("SELECT * FROM {articlecategory} WHERE pageid = ?", $pageid);
        $categorydata['type'] = isset($categorydata['type']) ? $categorydata['type'] : 'normal';
        if ($categorydata['type']=='index') {
            $categoryid = 'all';
        } elseif ($categorydata['type']=='parent') {
            $childcategories = Jojo::selectQuery("SELECT articlecategoryid FROM {page}p  LEFT JOIN {articlecategory} c ON (c.pageid=p.pageid) WHERE pg_parent = ? AND pg_link = 'jojo_plugin_jojo_article'", $pageid);
            foreach ($childcategories as $c) {
                $categoryid[] = $c['articlecategoryid'];
            }
            $categoryid[] = $categorydata['articlecategoryid'];
        } else {
            $categoryid = $categorydata['articlecategoryid'];
        }
        $sortby = $categorydata ? $categorydata['sortby'] : '';
        
        /* handle unsubscribes */
        if ($action == 'unsubscribe') {
            $code      = Jojo::getFormData('code',      '');
            $articleid = Jojo::getFormData('articleid', '');
            if (self::removeSubscriptionByCode($code, $articleid)) {
                $content['content'] = 'Subscription removed.<br />';
            } else {
                $content['content'] = 'This unsubscribe link is inactive, or you have already been unsubscribed.<br />';
            }
            $content['content'] .= 'Return to <a href="' . self::getArticleUrl($articleid) . '">article</a>.';
            return $content;
        } 

        $articles = self::getArticles('', '', $categoryid, $sortby);

        if ($action == 'rss') {
            self::getFeed($articles);
        }        
        
        if ($articleid || !empty($url)) {
            /* find the current, next and previous profiles */
            $article = array();
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

            /* Was a comment submitted? */
            if (Jojo::getFormData('submit', false)) {
                self::postComment($article);
            }

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

                if ($commentsubscriptions && self::isSubscribed($_USERID, $articleid)) {
                    $user['email_subscribe'] = true;
                    self::markSubscriptionsViewed($_USERID, $articleid);
                }
                $smarty->assign('article_user', $user);
            }

            /* calculate the next and previous articles */
            if (Jojo::getOption('article_next_prev') == 'yes') {
                if (!empty($nextarticle)) {
                    $nextarticle['url'] = self::getArticleUrl($nextarticle['articleid'], $nextarticle['ar_url'], $nextarticle['ar_title'], $nextarticle['ar_language'], $nextarticle['ar_category']);
                    $smarty->assign('nextarticle', $nextarticle);
                }

                if (!empty($prevarticle)) {
                    $prevarticle['url'] = self::getArticleUrl($prevarticle['articleid'], $prevarticle['ar_url'], $prevarticle['ar_title'], $prevarticle['ar_language'], $prevarticle['ar_category']);
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
            $smarty->assign('jojo_articleposturl', $article['url']);

            /* Add article breadcrumb */
            $breadcrumbs                      = $this->_getBreadCrumbs();
            $breadcrumb                       = array();
            $breadcrumb['name']               = $article['title'];
            $breadcrumb['rollover']           = $article['ar_desc'];
            $breadcrumb['url']                = $article['url'];
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
                $meta_description_template = Jojo::getOption('article_meta_description', '[article] - [body]... ');
                $articlebody = (strlen($article['bodyplain']) >400) ?  substr($mbody=wordwrap($article['bodyplain'], 400, '$$'), 0, strpos($mbody,'$$')) : $article['bodyplain'];
                $metafilters = array(
                        '[title]', 
                        '[site]', 
                        '[body]', 
                        '[author]'
                        );
                $metafilterreplace = array(
                        $article['title'], 
                        _SITETITLE, 
                        !empty($article['description']) ? $article['description'] : $articlebody,
                        $article['ar_author']
                        );
                        $content['meta_description'] = str_replace($metafilters, $metafilterreplace, $meta_description_template);
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
                $pagination = sprintf('<a href="%s/p1/">previous...</a>', (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($language, false) : '') . self::_getPrefix('article', (_MULTILANGUAGE ? $language : ''), $categoryid) );
            } elseif ($numpages == 2 && $pagenum == 1) {
                $pagination = sprintf('<a href="%s/p2/">more...</a>', (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($language, false) : '') . self::_getPrefix('article', (_MULTILANGUAGE ? $language : ''), $categoryid) );
            } else {
                $pagination = '<ul>';
                for ($p=1;$p<=$numpages;$p++) {
                    $url = (_MULTILANGUAGE ? Jojo::getMultiLanguageString ($language, false) : '') . self::_getPrefix('article', (_MULTILANGUAGE ? $language : ''), $categoryid) . '/';
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
        global $page;
        /* See if we have any article sections to display and find all of them */
        $articleindexes = Jojo::selectAssoc("SELECT p.pageid as id, p.pageid, p.*, c.* FROM {page} p LEFT JOIN {articlecategory} c ON (p.pageid=c.pageid) WHERE pg_link = 'jojo_plugin_jojo_article' AND pg_sitemapnav = 'yes' ORDER BY pg_parent");
        if (!count($articleindexes)) {
            return $sitemap;
        }
        
        if (Jojo::getOption('article_inplacesitemap', 'separate') == 'separate') {
            /* Remove any existing links to the articles section from the page listing on the sitemap */
            foreach($sitemap as $j => $section) {
                $sitemap[$j]['tree'] = self::_sitemapRemoveSelf($section['tree']);
            }
            $_INPLACE = false;
        } else {
            $_INPLACE = true;
        }

        $now = strtotime('now');
        $limit = 15;
        $articlesperpage = Jojo::getOption('articlesperpage', 40);
         /* Make sitemap trees for each articles instance found */
        foreach($articleindexes as $k => $i){
            /* Set language */
            $language = (_MULTILANGUAGE && !empty($i['pg_language'])) ? $i['pg_language'] : '';
            if (_MULTILANGUAGE) $page->page['pg_language'] = $i['pg_language'];
            /* Set category */
            $categoryid = $i['articlecategoryid'];
            $sortby = $i['sortby'];

            /* Create tree and add index and feed links at the top */
            $articletree = new hktree();
            $indexurl = (_MULTILANGUAGE ? Jojo::getMultiLanguageString($language, false) : '' ) . self::_getPrefix('article', $language, $categoryid) . '/';
            if ($_INPLACE) {
                $parent = 0;
            } else {
               $articletree->addNode('index', 0, $i['pg_title'] . ' Index', $indexurl);
               $parent = 'index';
            }

            $articles = self::getArticles('', '', $categoryid, $sortby);
            $n = count($articles);

            /* Trim items down to first page and add to tree*/
            $articles = array_slice($articles, 0, $articlesperpage);
            foreach ($articles as $a) {
                $articletree->addNode($a['articleid'], $parent, $a['ar_title'], self::getArticleUrl($a['articleid'], $a['ar_url'], $a['ar_title'], $a['ar_language'], $a['ar_category']));
            }

            /* Get number of pages for pagination */
            $numpages = ceil($n / $articlesperpage);
            /* calculate pagination */
            if ($numpages > 1) {
                for ($p=2; $p <= $numpages; $p++) {
                    $url = $indexurl .'p' . $p .'/';
                    $nodetitle = $i['pg_title'] . '  - page '. $p;
                    $articletree->addNode('p' . $p, $parent, $nodetitle, $url);
                }
            }
            /* Add RSS link for the plugin page */
           $articletree->addNode('rss', $parent, $i['pg_title'] . ' RSS Feed', $indexurl . 'rss/');

            /* Check for child pages of the plugin page */
            foreach (Jojo::selectQuery("SELECT pageid, pg_title, pg_url FROM {page} WHERE pg_parent = '" . $i['pageid'] . "' AND pg_sitemapnav = 'yes'") as $c) {
                    if ($c['pg_url']) {
                        $articletree->addNode($c['pageid'], $parent, $c['pg_title'], (_MULTILANGUAGE ? Jojo::getMultiLanguageString($language, false) : '') . $c['pg_url'] . '/');
                    } else {
                        $articletree->addNode($c['pageid'], $parent, $c['pg_title'], (_MULTILANGUAGE ? Jojo::getMultiLanguageString($language, false) : '') . $c['pageid']  . '/' .  Jojo::cleanURL($c['pg_title']) . '/');
                    }
            }

            /* Add to the sitemap array */
            if ($_INPLACE) {
                /* Add inplace */
                $url = (_MULTILANGUAGE ? Jojo::getMultiLanguageString($language, false) : '') . self::_getPrefix('article', $language, $categoryid) . '/';
                $sitemap['pages']['tree'] = self::_sitemapAddInplace($sitemap['pages']['tree'], $articletree->asArray(), $url);
            } else {
                if (_MULTILANGUAGE) {
                    $mldata = Jojo::getMultiLanguageData();
                    $lclanguage = $mldata['longcodes'][$language];
                }
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
                $sitemap[$k]['children'] = self::_sitemapAddInplace($t['children'], $toadd, $url);
            }
        }
        return $sitemap;
    }

    static function _sitemapRemoveSelf($tree)
    {
        static $urls;

        if (!is_array($urls)) {
            $urls = array();
            $articleindexes = Jojo::selectQuery("SELECT p.*, ac.* FROM {page} p  LEFT JOIN {articlecategory} ac ON (ac.pageid=p.pageid) WHERE pg_link = 'jojo_plugin_jojo_article' AND pg_sitemapnav = 'yes'");
            if (count($articleindexes)==0) {
               return $tree;
            }

            foreach($articleindexes as $key => $i){
                $language = !empty($i['pg_language']) ? $i['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $mldata = Jojo::getMultiLanguageData();
                $lclanguage = $mldata['longcodes'][$language];
                $urls[] = ((_MULTILANGUAGE) ? $lclanguage . '/' : '') . self::_getPrefix('article', ((_MULTILANGUAGE) ? $language : ''), $i['articlecategoryid']) . '/';
                $urls[] = ((_MULTILANGUAGE) ? $lclanguage . '/' : '') . self::_getPrefix('rss', ((_MULTILANGUAGE) ? $language : ''), $i['articlecategoryid']) . '/';
            }
        }

        foreach ($tree as $k =>$t) {
            if (in_array($t['url'], $urls)) {
                unset($tree[$k]);
            } else {
                $tree[$k]['children'] = self::_sitemapRemoveSelf($t['children']);
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
        $articles = self::getArticles('', '', 'all');
        $now = time();
        $articleindexes = Jojo::selectAssoc("SELECT pageid as id, pageid, p.*, c.* FROM {page} p LEFT JOIN {articlecategory} c ON (p.pageid=c.pageid) WHERE pg_link = 'jojo_plugin_jojo_article'");
        /* Add articles to sitemap */
        foreach($articles as $k => $a) {
            $apage =  $articleindexes[$a['pageid']];
            // strip out articles from expired pages
            if ($apage['pg_index'] != 'yes' || $apage['pg_xmlsitemapnav'] != 'yes' || $apage['pg_livedate']>$now || (!empty($apage['pg_expirydate']) && $apage['pg_expirydate']<$now) || $apage['pg_status']!='active') {
                unset($articles[$k]);
                continue;
            }
            $url = _SITEURL . '/'. $a['url'];
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
        $query .= " LEFT JOIN {articlecategory} ac ON (article.ar_category=ac.articlecategoryid) LEFT JOIN {page} p ON (ac.pageid=p.pageid)";
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
            $result['url'] = self::getArticleUrl($d['articleid'], $d['ar_url'], $d['ar_title'], $d['ar_language'], $d['ar_category']);
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
        global $page;
        $link = Jojo::getOption('article_external_rss');
        if ($link) {
            $data['Articles'] =  $link;
        }
        /* add category RSS feeds */
        $categories =  Jojo::selectQuery("SELECT articlecategoryid, pg_title, pg_language FROM {articlecategory} c LEFT JOIN {page} p ON (c.pageid=p.pageid)" . (_MULTILANGUAGE ? " WHERE pg_language = '" . $page->page['pg_language'] . "'" : ''));
        foreach ($categories as $c) {
            $prefix =  self::_getPrefix('article', (_MULTILANGUAGE ? $c['pg_language'] : ''), $c['articlecategoryid']) . '/rss/';
            if ($prefix) {
                $data[$c['pg_title']] = _SITEURL . '/' .  (_MULTILANGUAGE ? $c['pg_language'] . '/' : '') . $prefix;
            }
        }
        return $data;
    }

    /**
     * Removes any [[snip]] tags leftover in the content before outputting
     */
    static function removesnip($data)
    {
        $data = str_ireplace('[[snip]]','',$data);
        return $data;
    }

    static function getPrefixById($id=false) {
        if ($id) {
            $data = Jojo::selectRow("SELECT ar_category, ar_language FROM {article} WHERE articleid = ?", array($id));
            if ($data) {
                $prefix = self::_getPrefix('article', $data['ar_language'], $data['ar_category']);
                return $prefix;
            }
        }
        return false;
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

        if (!in_array($for, array('article', 'admin'))) {
            return '';
        }
        /* Cache some stuff */
        $language = $language ? $language : Jojo::getOption('multilanguage-default', 'en');

        if ($for == 'article') {
            $query = "SELECT p.pageid, pg_title, pg_url FROM {page} p LEFT JOIN {articlecategory} c ON (c.pageid=p.pageid) WHERE `articlecategoryid` = '$categoryid'";
            $values = array('jojo_plugin_jojo_article');
        } elseif ($for == 'admin') {
            $query = "SELECT pageid, pg_title, pg_url FROM {page} WHERE pg_link = ?";
            $values = array('Jojo_Plugin_Jojo_article_admin');
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
        $id = Jojo::getFormData('id',     0);
        $url       = Jojo::getFormData('url',    '');
        $action    = Jojo::getFormData('action', '');
        $pagenum   = Jojo::getFormData('pagenum', 1);

        $data = Jojo::selectRow("SELECT articlecategoryid FROM {articlecategory} WHERE pageid=?", $page->page['pageid']);
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
            return _SITEURL . '/' . self::getArticleUrl($article['articleid'], $article['ar_url'], $article['ar_title'], $article['ar_language'], $article['ar_category']);
        }
        $correcturl = self::getArticleUrl($id, $url, null, $language, $categoryid);

        if ($correcturl) {
            return _SITEURL . '/' . $correcturl;
        }

        /* article index with pagination */
        if ($pagenum > 1) return parent::getCorrectUrl() . 'p' . $pagenum . '/';

        if ($action == 'rss') return parent::getCorrectUrl() . 'rss/';

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
                        'id' => $matches[2]
                        );
         } elseif (preg_match('#^(.+)/([0-9]+)$#', $uri, $matches)) {
            /* "$prefix/[id:integer]" eg "articles/123/" */
            $prefix = $matches[1];
            $getvars = array(
                        'id' => $matches[2]
                        );
        } elseif (preg_match('#^(.+)/p([0-9]+)$#', $uri, $matches)) {
            /* "$prefix/p[pagenum:([0-9]+)]" eg "articles/p2/" for pagination of articles */
            $prefix = $matches[1];
            $getvars = array(
                        'pagenum' => $matches[2]
                        );
        } elseif (preg_match('#^(.+)/rss$#', $uri, $matches)) {
            /* eg "articles/rss/" for rss feeds */
            $prefix = $matches[1];
            $getvars = array(
                        'action' => 'rss'
                        );
        } elseif (preg_match('#^(.+)/([a-z0-9-_]+)$#', $uri, $matches)) {
            /* "$prefix/[url:((?!rss)string)]" eg "articles/name-of-article/" ignoring "articles/rss" */
            $prefix = $matches[1];
            $getvars = array(
                        'url' => $matches[2]
                        );
        } else {
            /* Didn't match */
            return false;
        }

        /* Check the prefix matches */
        if ($res = self::checkPrefix($prefix)) {
            /* If full uri matches a prefix it's an index page so ignore it and let the page plugin handle it */
            if (self::checkPrefix(trim($uri, '/'))) return false;
      
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
            $categories = array_merge($categories, Jojo::selectAssoc("SELECT articlecategoryid, articlecategoryid as articlecategoryid2 FROM {articlecategory}"));
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
                $testPrefix = self::_getPrefix('article', $language, $category);
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

    // Sync the articategory data over to the page table
    static function admin_action_after_save_articlecategory() {
        if (!Jojo::getFormData('fm_pageid', 0)) {
            // no pageid set for this category (either it's a new category or maybe the original page was deleted)
            self::sync_articlecategory_to_page();
       }
    }

    // Sync the category data over from the page table
    static function admin_action_after_save_page() {
        if (strtolower(Jojo::getFormData('fm_pg_link',    ''))=='jojo_plugin_jojo_article') {
           self::sync_page_to_articlecategory();
       }
    }

    static function sync_category_to_page() {
        // Get the category id (if an existing category being saved where the page has been deleted)
        $catid = Jojo::getFormData('fm_articlecategoryid', 0);
        if (!$catid) {
        // no id because this is a new category - shouldn't really be done this way, new categories should be added by adding a new page
            $cats = Jojo::selectQuery("SELECT articlecategoryid FROM {articlecategory} ORDER BY articlecategoryid");
            // grab the highest id (assumes this is the newest one just created)
            $cat = array_pop($cats);
            $catid = $cat['articlecategoryid'];
        }
        // add a new hidden page for this category and make up a title
            $newpageid = Jojo::insertQuery(
            "INSERT INTO {page} SET pg_title = ?, pg_link = ?, pg_url = ?, pg_parent = ?, pg_status = ?",
            array(
                'Orphaned Articles',  // Title
                'jojo_plugin_jojo_article',  // Link
                'orphaned-articles',  // URL
                0,  // Parent - don't do anything smart, just put it at the top level for now
                'hidden' // hide new page so it doesn't show up on the live site until it's been given a proper title and url
            )
        );        
        // If we successfully added the page, update the category with the new pageid
        if ($newpageid) {
            jojo::updateQuery(
                "UPDATE {articlecategory} SET pageid = ? WHERE articlecategoryid = ?",
                array(
                    $newpageid,
                    $catid
                )
            );
       }
    return true;
    }

    static function sync_page_to_articlecategory() {
        // Get the list of categories
        $categories = jojo::selectAssoc("SELECT pageid AS id, pageid FROM {articlecategory}");
        // And the page data
        $pageid = Jojo::getFormData('fm_pageid', 0);
        // if it's a new page it won't have an id in the form data, so get it from the title
        if (!$pageid) {
           $title = Jojo::getFormData('fm_pg_title', 0);
           $page =  Jojo::selectRow("SELECT pageid, pg_url FROM {page} WHERE pg_title= ? AND pg_link = ? AND pg_language = ?", array($title, Jojo::getFormData('fm_pg_link', ''), Jojo::getFormData('fm_pg_language', '')));
           $pageid = $page['pageid'];
        }
        // no category for this page id
        if (!count($categories) || !isset($categories[$pageid])) { 
            jojo::insertQuery("INSERT INTO {articlecategory} (pageid) VALUES ('$pageid')");
        }
        return true;
    }

    static function getFeed($articles)
    {
        $full = (Jojo::getOption('article_full_rss_description') == 'yes') ? true : false;
        $site = mb_convert_encoding(_SITETITLE, 'HTML-ENTITIES', 'UTF-8');
        $page = mb_convert_encoding($articles[0]['category'], 'HTML-ENTITIES', 'UTF-8');
        $pageurl = _SITEURL . '/' . $articles[0]['categoryurl'];
        $description = mb_convert_encoding(Jojo::getOption('sitedesc', Jojo::getOption('sitetitle')), 'HTML-ENTITIES', 'UTF-8');
        $rss  = "<?xml version=\"1.0\" ?".">\n";
        $rss .= "<rss version=\"2.0\">\n";
        $rss .= "<channel>\n";
        $rss .= "<title>" . $site . ': ' . $page . "</title>\n";
        $rss .= "<description>" . $description . "</description>\n";
        $rss .= "<link>" . $pageurl . "</link>\n";
        $rss .= "<copyright>" . htmlentities(_SITETITLE) . " " . date('Y', strtotime('now')) . "</copyright>\n";

        $limit = Jojo::getOption('article_rss_num_articles', 15);
        $articles = array_slice($articles, 0, $limit);
        foreach ($articles as &$a) {
            $a['body'] = Jojo::relative2absolute($a['ar_body'], _SITEURL);
            /* chop up to the first [[snip]] */
            if ($full) {
                $a['body'] = str_ireplace('[[snip]]','',$a['body']);
            } else {
                $arr = Jojo::iExplode('[[snip]]', $a['body']);
                if (count($arr) === 1) {
                    $a['body'] = substr($a['body'], 0, Jojo::getOption('article_rss_truncate', 800)) . ' ...';
                } else {
                    $a['body'] = $arr[0];
                }
            } 
            $source = _SITEURL . "/" . $a['url'];
            $a['body'] = mb_convert_encoding($a['ar_body'], 'HTML-ENTITIES', 'UTF-8');
            $a['title'] = mb_convert_encoding($a['title'], 'HTML-ENTITIES', 'UTF-8');
            if (Jojo::getOption('article_feed_source_link') == 'yes') $a['body'] .= '<p>Source: <a href="' . $source . '">' . $a['title'] . '</a></p>';
            $rss .= "<item>\n";
            $rss .= "<title>" . Jojo::xmlEscape($a['title']) . "</title>\n";
            $rss .= "<description>" . Jojo::xmlEscape($a['body']) . "</description>\n";
            $rss .= "<link>". $source . "</link>\n";
            $rss .= "<pubDate>" . Jojo::mysql2date($a['ar_date'], 'rss') . "</pubDate>\n";
            $rss .= "</item>\n";
        }
        $rss .= "</channel>\n";
        $rss .= "</rss>\n";

        header('Content-type: application/xml');
        echo $rss;
        exit;
    }


}
