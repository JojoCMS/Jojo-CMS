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
 * @package jojo_comment
 */

class Jojo_Plugin_Jojo_Comment extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty, $_USERGROUPS;
        $content = array();
        $messages = array();

        /* What's the code? */
        $urlParts = explode('/', _SITEURI);
        $code = $urlParts[count($urlParts) - 1];
        if (strlen($code) != 16 || !preg_match('%^([0-9]+)$%', $code)) {
            /* Should never get here as getCorrectUrl checks the code format */
            $content['content'] = 'Invalid code';
            return $content;
        }

        /* Approve, dofollow and use anchortextcomments that match the code */
        $useanchortext = Jojo::selectQuery("SELECT * FROM {comment} WHERE anchortextcode = ?", $code);
        Jojo::updateQuery("UPDATE {comment} SET nofollow = '0', useanchortext='1' WHERE anchortextcode = ?", $code);
        foreach ($useanchortext as $a) {
            $messages[] = "Updating comment by " . $a['name'] . " to DOFOLLOW, and USE ANCHOR TEXT";
        }

        /* Approve and dofollow comments that match the code */
        $active = Jojo::selectQuery("SELECT * FROM {comment} WHERE approvecode = ?", $code);
        Jojo::updateQuery("UPDATE {comment} SET nofollow = '0' WHERE approvecode = ?", $code);
        foreach ($active as $a) {
            $messages[] = "Updating comment by " . $a['name'] . " to DOFOLLOW";
        }

        /* Delete comments that match the code */
        $delete = Jojo::selectQuery("SELECT * FROM {comment} WHERE deletecode = ?", $code);
        Jojo::deleteQuery("DELETE FROM {comment} WHERE deletecode = ?", $code);
        foreach ($delete as $d) {
           $messages[] = "Deleting  comment by " . $d['name'];
        }

        /* Didn't find anything */
        if (!count($useanchortext) && !count($active) && !count($delete)) {
           $messages[] = "No matching comments were found. This comment may have already been deleted.";
        }

        /* Redirect to index page which will show the messages */
        $content['content'] = implode('<br/>', $messages);
        return $content;
    }

    function getCorrectUrl()
    {
        /**
         * Url will be in the format:
         *   /commentadmin/1234567890123456/
         */

        /* What's in our url */
        $urlParts = explode('/', _SITEURI);
        $lastPart = $urlParts[count($urlParts) - 1];
        if (preg_match('%^([0-9]+)$%', $lastPart) && strlen($lastPart) == 16) {
            return _SITEURL . '/' . $this->getValue('pg_url') . '/' . $lastPart . '/';
        }
        return _SITEURL;
    }

    static public function isUrl($uri)
    {
        $prefix = false;
        $getvars = array();

        /* Check the suffix matches and extra the prefix */
        if (preg_match('#^(.+)/([a-zA-Z0-9]{16})$#', $uri, $matches)) {
            $prefix = $matches[1];
            $getvars = array('code' => $matches[2]);
        } else {
            /* Didn't match */
            return false;
        }
        /* Check the prefix matches */
        if (self::checkPrefix($prefix)) {
            /* The prefix is good, pass through uri parts */
            foreach($getvars as $k => $v) {
                $_GET[$k] = $v;
            }
            return true;
        }
        return false;
    }

    /**
     * Check if a prefix belongs to this plugin
     */
    static public function checkPrefix($prefix)
    {
        static $_prefixes;

        /* Check if it's in the cache */
        if (isset($_prefixes[$prefix])) {
            return $_prefixes[$prefix];
        }
        /* Check everything */
        $testPrefix = self::_getPrefix('commentadmin');
        $_prefixes[$testPrefix] = true;
        if ($testPrefix == $prefix) {
            /* The prefix is good */
            return true;
        }
        /* Didn't match */
        $_prefixes[$testPrefix] = false;
        return false;
    }

    /**
     * Get the url prefix for a particular part of this plugin
     */
    static function _getPrefix($for='commentadmin') {
        $cacheKey = $for;
        /* Have we got a cached result? */
        static $_cache;
        if (isset($_cache[$cacheKey])) {
            return $_cache[$cacheKey];
        }

        /* Cache some stuff */
        $query = "SELECT pageid, pg_title, pg_url FROM {page} WHERE `pg_link` = 'jojo_plugin_jojo_comment'";
        $res = Jojo::selectRow($query);

        if ($res) {
            $_cache[$cacheKey] = !empty($res['pg_url']) ? $res['pg_url'] : $res['pageid'] . '/' . $res['pg_title'];
        } else {
            $_cache[$cacheKey] = '';
        }
        return $_cache[$cacheKey];
    }


    static function getComments($itemid, $plugin, $pageid, $allowcomments=false)
    {
        global $smarty, $_USERGROUPS, $_USERID, $templateoptions;

        $allowcomments = Jojo::applyFilter("jojo_comment:allow_new", $allowcomments);

        /* assign user variables for pre-populating fields for logged in users */
        if (!empty($_USERID)) {
            $user = Jojo::selectRow("SELECT userid, us_login, us_firstname, us_lastname, us_email FROM {user} WHERE userid = ?", array($_USERID));
            $user['isadmin'] = (boolean)(in_array('admin', $_USERGROUPS));
            if (empty($_SESSION['name']) && (isset($user['us_firstname']) || isset($user['us_lastname']))) {
                $_SESSION['name'] = (isset($user['us_firstname']) ? $user['us_firstname'] : '') . ' ' . (isset($user['us_lastname']) ? $user['us_lastname'] : '');
            } elseif (empty($_SESSION['name'])) {
                $_SESSION['name'] = isset($user['us_login']) ? $user['us_login'] : 'admin';
            }
            if (empty($_SESSION['email']) && isset($user['us_email']))   $_SESSION['email'] = $user['us_email'];

            if (self::isSubscribed($_USERID, $itemid, $plugin)) {
                $user['email_subscribe'] = true;
                self::markSubscriptionsViewed($_USERID, $itemid, $plugin);
            }
            $smarty->assign('user', $user);
        }
        /* Remember user fields from session */
        if (!empty($_SESSION['name'])) $smarty->assign('name', $_SESSION['name']);
        if (!empty($_SESSION['email'])) $smarty->assign('email', $_SESSION['email']);
        if (!empty($_SESSION['website'])) $smarty->assign('website', $_SESSION['website']);
        if (!empty($_SESSION['anchortext'])) $smarty->assign('anchortext', $_SESSION['anchortext']);

        $comments = Jojo::selectQuery("SELECT * FROM {comment} WHERE itemid = ? AND plugin = ? ORDER BY timestamp", array($itemid, $plugin));
        $smarty->assign('comments', $comments);
        $smarty->assign('numcomments', count($comments));

       /* If a file called post-comment.gif exists, use this instead of a text link */
        foreach (Jojo::listPlugins('images/post-comment.gif') as $pluginfile) {
            $smarty->assign('commentbutton', true);
        }

        /* Calculate if user is admin or not. Admins can edit comments */
        $pagePermissions = new Jojo_Permissions();
        $pagePermissions->getPermissions('page', $pageid);
        if ($pagePermissions->hasPerm($_USERGROUPS, 'edit')) {
            $smarty->assign('editperms', true);
        }
        $templateoptions['frajax'] = true;
        $smarty->assign('templateoptions', $templateoptions);
        $smarty->assign('commentweblink', (boolean)(Jojo::getOption('comment_optional_website', 'yes')=='yes'));

        if (Jojo::getOption('new_comment_position', 'below') == 'above') {
            /* New Comment form above the existing comments */
            $commenthtml = ($allowcomments ? $smarty->fetch('jojo_post_comment.tpl') : '') . $smarty->fetch('jojo_comment.tpl');
        } else {
            /* New Comment form below the existing comments */
            $commenthtml = $smarty->fetch('jojo_comment.tpl') . ($allowcomments ? $smarty->fetch('jojo_post_comment.tpl') : '');
        }

        return  $commenthtml;
    }

    static function postComment($item)
    {
        $plugin = $item['plugin'];
        $page = $item['pagetitle'];
        $itemid = $item['id'];
        $title = $item['title'];
        $url = $item['url'];

        if (!$itemid || !$plugin) return false;
        global $smarty, $_USERID;

        $errors = array();
        $commentsubscriptions = Jojo::getOption('comment_subscriptions', 'no') == 'yes' ? true : false;

        /* Get variables from POST */
        $name            = htmlspecialchars(Jojo::getFormData('name', ''), ENT_COMPAT, 'UTF-8', false);
        $authorcomment   = (Jojo::getFormData('authorcomment', 'no') == 'yes');
        $email           = trim(Jojo::getFormData('email',           ''));
        $email_subscribe = Jojo::getFormData('email_subscribe', false) ? true : false;
        $website         = Jojo::getFormData('website',         '');
        $anchortext      = htmlspecialchars(Jojo::getFormData('anchortext',      ''), ENT_COMPAT, 'UTF-8', false);
        $bbcomment       = Jojo::getFormData('comment',         '');
        $captchacode     = Jojo::getFormData('CAPTCHA',     '');
        if (!empty($website)) $website = Jojo::addhttp($website);
        $ip = Jojo::getIP();

        /* Check CAPTCHA is entered correctly */
        if (empty($_USERID) && Jojo::getOption('contactcaptcha') == 'yes' && !PhpCaptcha::Validate($captchacode)) {
            $errors[] = 'Invalid code entered';
        }

        /* Error checking */
        if ($name == '') {
            $errors[] = 'Please enter your name';
        }
        if ($bbcomment == '') {
            $errors[] = 'Please enter a comment';
        }
        if ((Jojo::getOption('comment_optional_email', 'no') == 'no') && empty($email)) {
            $errors[] = 'Please enter your email address (this will not be shown to the public)';
        }
        if (($email != '') && !Jojo::checkEmailFormat($email)) {
            $errors[] = 'Email format is invalid';
        }
        if (($website != '') && !Jojo::checkUrlFormat($website)) {
            $errors[] = 'Website format is invalid';
        }

        if (Jojo::getOption('comment_useronly', 'no') == 'yes' && $email && !Jojo::selectRow("SELECT userid FROM {user} WHERE us_email=?", array($email)) ) {
            $errors[] = 'Comments are restricted to registered users or subscribers only';
        }

        /* rate limiting to prevent spam */
        $rate_comments = Jojo::getOption('comment_spam_num', 3); // maximum X posts
        $rate_mins = Jojo::getOption('comment_spam_time', 5);  // in X mins
        $ratelimit = Jojo::selectQuery("SELECT * FROM {comment} WHERE ip='" . $ip . "' AND timestamp >" . strtotime('-' . $rate_mins . ' minutes'));
        if (count($ratelimit)>$rate_comments) {
            $errors[] = 'We limit comments to ' . $rate_comments . ' every ' . $rate_mins . ' minutes to prevent automated spam. Please try posting your comment again in a few minutes, and sorry for any inconvenience';
        }

        $thisiscrap = false;
        if (Jojo::getOption('comment_spam_keywords', '')) {
            $crap = Jojo::ta2array(Jojo::getOption('comment_spam_keywords'));
            foreach($crap as $c) {
                if(strpos(strtolower($website), $c) !== false || strpos(strtolower($name), $c) !== false ) {
                    $thisiscrap = true;
                    break;
                }
            }
        }
        if (!$thisiscrap && substr_count($bbcomment, 'http://')>Jojo::getOption('comment_spam_links', 5)) {
            $thisiscrap = true;
        }
        if ($thisiscrap) {
            return false;
            //could also at this point add $ip to a banned ips list rather than just silent failing them
        }
        $bbcomment = strip_tags($bbcomment);
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
        $query = 'SELECT * FROM {comment} WHERE approvecode = ? OR deletecode = ? OR anchortextcode = ?';
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
        $userid = $_USERID ? $_USERID : '';
        Jojo::insertQuery("INSERT INTO {comment} SET
                timestamp = UNIX_TIMESTAMP(), itemid = ?, plugin = ?,
                userid = ?, name = ?, email = ?, website = ?, anchortext = ?,
                ip = ?, useanchortext = '0', authorcomment = ?,
                bbbody = ?, body = ?, approvecode = ?,
                anchortextcode = ?, deletecode = ?",
                array($itemid, $plugin, $userid, $name, $email, $website, $anchortext, Jojo::getIP(),
                      $authorcomment, $bbcomment, $htmlcomment, $approvecode, $anchortextcode, $deletecode));

        /* Store details in the session so the user doesn't have to reenter on every comment */
        $_SESSION['name']       = $name;
        $_SESSION['email']      = $email;
        $_SESSION['website']    = $website;
        $_SESSION['anchortext'] = $anchortext;

        /* Send article details for email to webmaster */
        $message  = 'A comment has been added to ' . $page . ' on '  . $title . ' at ' . _SITEURL . "/" . $url . "\n\n";
        $message .= "Comment by: " . $name . "\n";
        $message .= $email != '' ? "Email: " . $email. "\n" : '';
        $message .= $website != '' ? "Website: " . $website . "\n" : '';
        $message .= $anchortext != '' ? "Anchor text: " . $anchortext . "\n" : '';
        $message .= "Comment:\n" . $htmlcomment . "\n";

        $message .= "\n\nThis comment is currently live on the site, but has a nofollow link\n\n";

        if (!empty($anchortext)) {
            $message .= "To FOLLOW the link on this comment AND use the chosen anchor text (for great comments), click the following link\n";
            $message .= _SITEURL . '/' . self::_getPrefix() . '/' . $anchortextcode . "/\n\n";
        }
        $message .= "To FOLLOW the link on this comment (for good comments), click the following link\n";
        $message .= _SITEURL . '/' . self::_getPrefix() . '/' . $approvecode . "/\n\n";
        $message .= "To DELETE this comment, click the following link\n";
        $message .= _SITEURL . '/' . self::_getPrefix() . '/' . $deletecode . "/\n";
        $message .= Jojo::emailFooter();

        /* Email comment to webmaster and site contact */
        if (Jojo::getOption('comment_webmaster', 'yes')=='yes') Jojo::simplemail(_WEBMASTERNAME, , Jojo::getOption('sitetitle') . ' Comment - ' . $title, $message, $name, $email);
        if(_CONTACTADDRESS != _WEBMASTERADDRESS) Jojo::simplemail(_FROMNAME, _CONTACTADDRESS, Jojo::getOption('sitetitle') . ' Comment - ' . $title, $message, $name, $email);

        /* add subscription if needed, and update all subscriptions to say the topic has a new comment */
        if ($commentsubscriptions && $email_subscribe && !empty($_USERID)) {
            self::addSubscription($_USERID, $itemid, $plugin);
        }
        self::markSubscriptionsUpdated($itemid, $plugin);

        /* log a copy of the comment */
        $log = new Jojo_Eventlog();
        $log->code = 'comment';
        $log->importance = 'normal';
        $log->shortdesc = 'Comment by ' . $name . ' on ' . $title;
        $log->desc = $message;
        $log->savetodb();
        unset($log);

        /* Delete cache for this page - forcing regeneration next view */
        if (_CONTENTCACHE) {
            $query = "DELETE FROM {contentcache} WHERE cc_url=? LIMIT 1";
            $values = array(_SITEURL . '/' . $url);
            Jojo::deleteQuery($query, $values);
        }

        /* Redirect back to the to see the comment on the page */
        header('location: ' . _SITEURL . '/' . $url);
        exit();
    }

    static function getItemsById($ids)
    {
        $query  = "SELECT *";
        $query .= " FROM {comment} ";
        $query .=  is_array($ids) ? " WHERE commentid IN ('". implode("',' ", $ids) . "')" : " WHERE commentid=$ids";
        $items = Jojo::selectQuery($query);
        $items = is_array($ids) ? $items : $items[0];
        return $items;
    }

    static function getItemHtml($comment)
    {
        global $smarty, $_USERGROUPS, $_USERID;
        /* Calculate if user is admin or not. Admins can edit comments */
        $page = Jojo_Plugin::getPage(Jojo::parsepage('admin'));
        if ($page->perms->hasPerm($_USERGROUPS, 'view')) {
            $smarty->assign('editperms', true);
        }
        $smarty->assign('c', $comment);
        $commenthtml = $smarty->fetch('jojo_comment_inner.tpl');
        return $commenthtml;
    }


    static function addSubscription($userid, $itemid, $plugin)
    {
        /* attempt to update existing subscription */
        $updated = Jojo::updateQuery("UPDATE {commentsubscription}  SET lastviewed=?, lastemailed=0 WHERE userid=? AND itemid=? AND plugin = ? LIMIT 1", array(time(), $userid, $itemid, $plugin));
        if ($updated) return true;

        /* create new subscription */
        $code = Jojo::randomString(16);
        Jojo::insertQuery("INSERT INTO {commentsubscription} SET userid=?, itemid=?, plugin = ?, lastviewed=?, lastemailed=0, code=?", array($userid, $itemid, $plugin, time(), $code));
        return true;
    }

    static function removeSubscription($userid, $itemid, $plugin)
    {
        $data = Jojo::selectQuery("SELECT * FROM {commentsubscription} WHERE userid=? AND itemid=? AND plugin = ? ", array($userid, $itemid, $plugin));
        if (!count($data)) return false; //nothing to delete
        Jojo::deleteQuery("DELETE FROM {commentsubscription} WHERE userid=? AND itemid=? AND plugin = ?", array($userid, $itemid, $plugin));
        return true;
    }

    static function removeSubscriptionByCode($code, $itemid, $plugin)
    {
        $data = Jojo::selectQuery("SELECT * FROM {commentsubscription} WHERE code=? AND itemid=? AND plugin = ?", array($code, $itemid, $plugin));
        if (!count($data)) return false; //nothing to delete
        Jojo::deleteQuery("DELETE FROM {commentsubscription} WHERE code=? AND itemid=? AND plugin = ? ", array($code, $itemid, $plugin));
        return true;
    }

    static function isSubscribed($userid, $itemid, $plugin)
    {
        $data = Jojo::selectQuery("SELECT * FROM {commentsubscription} WHERE userid=? AND itemid=? AND plugin = ? ", array($userid, $itemid, $plugin));
        return (count($data)) ? true : false;
    }

    static function markSubscriptionsUpdated($itemid, $plugin)
    {
        Jojo::insertQuery("UPDATE {commentsubscription} SET lastupdated=? WHERE itemid=? AND plugin = ? ", array(time(), $itemid, $plugin));
    }

    static function markSubscriptionsViewed($userid, $itemid, $plugin)
    {
        Jojo::insertQuery("UPDATE {commentsubscription} SET lastviewed=? WHERE userid=? AND itemid=? AND plugin = ? LIMIT 1", array(time(), $userid, $itemid, $plugin));
    }

    static function processSubscriptionEmails($limit=3) {
        $subscriptions = Jojo::selectQuery("SELECT cs.*, us_firstname, us_login, us_email FROM {commentsubscription} cs LEFT JOIN {user} us ON (cs.userid=us.userid) WHERE (lastupdated > lastviewed) AND (lastupdated > lastemailed) LIMIT ?", $limit);
        $from_name = Jojo::either(_FROMNAME, _WEBMASTERNAME);
        $from_email = Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS);
        foreach ($subscriptions as $sub) {
            $class = 'Jojo_Plugin_' . $sub['plugin'];
            $id = $sub['itemid'];
            if (class_exists($class) && method_exists($class, 'getItemsById') && $id) {
                $item = call_user_func($class . '::getItemsById', $id);
                $subject  = 'New comment notification: ' . $item['title'];
                $message  = 'A new comment has been added to "' . $item['title'] . '" on ' . Jojo::getOption('sitetitle') . '. You are subscribed to receive email notifications notifications of any new comments on this post.';
                $message .= "To view the new comments, please visit the following link.\n";
                $message .= _SITEURL.'/'. $item['url']."\n\n";
                $message .= "You can unsubscribe from this notification by using the following link.\n";
                $message .= _SITEURL.'/'. $item['pageurl'] . "unsubscribe/" . $id . "/".$sub['code']."/\n";
                $message .= "\nRegards,\n" . Jojo::getOption('webmastername') . "\n" . Jojo::getOption('sitetitle')."\n".Jojo::getOption('fromaddress')."\n";
                if (Jojo::simpleMail($sub['us_login'], $sub['us_email'], $subject, $message, $from_name, $from_email)) {
                    Jojo::updateQuery("UPDATE {commentsubscription} SET lastemailed=? WHERE userid=? AND itemid=? AND plugin = ? ", array(time(), $sub['userid'], $id, $sub['plugin']));
               } else {
                    /* log a copy of the message */
                    $log             = new Jojo_Eventlog();
                    $log->code       = 'comment';
                    $log->importance = 'high';
                    $log->shortdesc  = 'Failed comment notification to ' . $sub['us_login'] . ' (' . $sub['us_email'] . ')';
                    $log->desc       = $message;
                    $log->savetodb();
                    unset($log);
                }
            }
        }
    }
}
