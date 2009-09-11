<?php

class Jojo_Auth_Local {
    public static function authenticate() {
        $username = Jojo::getFormData('username', '');
        $password = Jojo::getFormData('password', '');
        $remember = Jojo::getFormData('remember', false);

        if ($username) {
            /* Look up user by username and password from login form submission */
            $values = array($username, $password, $password);
            $logindata = Jojo::selectRow("SELECT * FROM {user} WHERE us_login = ? AND (us_password = SHA1(CONCAT(?, us_salt)) OR us_password = MD5(CONCAT(?, us_salt))) AND us_locked = 0", $values);

            /* Make sure all users have salted passwords - if it's not salted, add salt */
            if ($logindata && empty($logindata['us_salt'])) {
                $salt = Jojo::randomString(16);
                Jojo::updateQuery("UPDATE {user} SET us_salt = ?, us_password = ? WHERE userid = ?", array($salt, sha1($password . $salt), $logindata['userid']));
            }

            if ($logindata && $logindata['us_failures'] > 0) {
                /* Reset login failure count */
                Jojo::updateQuery("UPDATE {user} SET us_failures = 0 WHERE userid = ?", $logindata['userid']);
            }

            if ($logindata && $remember) {
                /* Set remember password cookie */
                $code = Jojo::randomstring(16);
                setcookie("jojoR", base64_encode($logindata['userid'] . ':' . $code), time() + (60 * 60 * 24 * 365), '/' . _SITEFOLDER);
                $values = array((int)$logindata['userid'], $code, time());
                $res = Jojo::insertQuery("INSERT INTO {auth_token} SET userid = ?, token = ?, lastused = ?", array($values));
            }

            if ($logindata) {
                /* After login hook */
                 $_SESSION['loggingin'] = true;
                return $logindata['userid'];
            }

            /* Submitted a username but the password didn't match */
            $loginmessage = 'Your username or password is incorrect';
            Jojo::updateQuery("UPDATE {user} SET us_lastfailure = NOW(), us_failures = us_failures + 1 WHERE us_login = ? LIMIT 1", array($username));

            /* Find out how many times user has failed - warn and lock if too many times */
            $failures = Jojo::selectRow("SELECT us_failures FROM {user} WHERE us_login = ?", array($username));
            if (isset($failures['us_failures'])) {
                /* Warn after 5 failures */
                if ($failures['us_failures'] >= 5) {
                    $loginmessage = "You have entered your password incorrectly " . $failures['us_failures'] . " times.<br />You will be locked out after 10 incorrect attempts.";
                }

                /* Lock account after 10 failures */
                if ($failures['us_failures'] >= 10) {
                    $loginmessage = 'This account has been locked and must be unlocked by the administrator.<br/>Please contact <a href="mailto:' . Jojo::getOption('webmasteraddress') . '">' . Jojo::getOption('webmasteraddress') . "</a>";
                    Jojo::updateQuery("UPDATE {user} SET us_locked = 1 WHERE us_login = ? LIMIT 1", array($username));
                }
            }

            /* Delete Cookie */
            if (isset($_COOKIE['remember'])) {
                setcookie('jojoR', '', time() - 3600, _SITEFOLDER);
            }

            /* Error message to return to user */
            global $smarty;
            $smarty->assign('loginmessage', $loginmessage);
            return false;
        }
    }
}