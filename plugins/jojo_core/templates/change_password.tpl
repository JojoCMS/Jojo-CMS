{if $message}<div class="message">{$message}</div>{/if}
{if $error}<div class="error">{$error}</div>{/if}

<p>Please confirm your existing password, and enter a new password.</p>

<h3>Guidelines for Passwords</h3>
<ul>
  <li>Passwords are case sensitive</li>
  <li>Must be betweeen {$minlength} and {$maxlength} characters</li>
  <li>Must contain a number</li>
</ul>

<div>
  <h3>Change your password</h3>
  <form method="post" action="{$REQUEST_URI}"  class="standard-form" onsubmit="return checkme_changepass()">

    <label for="oldp">Current Password:</label>
    <input type="password" name="oldp" id="oldp" value="{$oldp}" size="25" /> *<br />

    <label for="newp">New Password:</label>
    <input type="password" name="newp" id="newp" value="{$newp}" size="25" onkeyup="checkPassword(this.value)" /> *<br />

    <label for="newp2">Confirm New Password:</label>
    <input type="password" name="newp2" id="newp2" value="{$newp2}" size="25" /> *<br />

    <label>Password Strength:</label>
    <div style="float: left; border: 1px solid #888; background: #fff; width: 80px; padding: 1px;"><div id="progressBar" style=" height: 20px; width: 0;"></div></div><br />

    <label></label>
    <input type="submit" class="button" name="reset" value="Change" />

  </form>
  <div class="clear"></div>
</div>
