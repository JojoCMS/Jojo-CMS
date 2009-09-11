{if $changed}
<p>Your password has been changed.<br />
New password: <strong>{$newpassword}</strong><br />
You can change this password to something more familiar <a href="change-password/" rel="nofollow">here</a>.</p>

{else}

{if $messages}<div class="message">
  <ul>{section name=m loop=$messages}
  <li>{$messages[m]}</li>
  {/section}</ul>
</div>{/if}
{if $errors}<div class="error">
  <ul>{section name=e loop=$errors}
  <li>{$errors[e]}</li>
  {/section}</ul>
</div>{/if}

{if $message == "" && $error == ""}
<p>Because passwords are stored encrypted, we are unable to resend your password to you.
We are able to email you a password reminder if we have one stored, or a link to reset the password.</p>
{/if}
Please enter your email address or username and select an option below.<br /><br />
<form method="post" action="{$pg_url}/">
<input type="radio" name="type" id="type_reminder" value="reminder" {if $type == "reminder"} checked="checked"{/if}/> <label for="type_reminder">Send Reminder</label>
<input type="radio" name="type" id="type_reset" value="reset" {if $type == "reset"} checked="checked"{/if}/> <label for="type_reset">Send Reset Link</label>
<br />
Email Address or username: <input type="text" size="40" name="search" value="{$search}" />
<input type="submit" class="button" name="btn_reset" value="Send" />
</form>
{/if}