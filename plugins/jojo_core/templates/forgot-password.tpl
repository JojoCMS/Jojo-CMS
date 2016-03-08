{if $changed}
<p>Your password has been changed.<br />
New password: <strong>{$newpassword}</strong><br />
To change this password to something more familiar, <b>copy this one first</b> (you'll need to provide it along with your choice for a new one), and use the <a href="change-password/" rel="nofollow">change password form</a>.</p>

{else}

{if $messages}<div class="message alert alert-success">
  <ul>{section name=m loop=$messages}
  <li>{$messages[m]}</li>
  {/section}</ul>
</div>{/if}
{if $errors}<div class="error alert alert-danger">
  <ul>{section name=e loop=$errors}
  <li>{$errors[e]}</li>
  {/section}</ul>
</div>{/if}

{if $message == "" && $error == ""}<p>Passwords are one-way encrypted so we can't resend your password to you but we can email you your password reminder if you saved one, or a link to reset the password.</p>
{/if}
Please enter your email address or username and select an option below.<br /><br />
<form method="post" action="{$pg_url}/">
    <div class="form-group">
        <label for="type_reminder" class="radio-inline"><input type="radio" name="type" id="type_reminder" value="reminder" {if $type == "reminder"} checked="checked"{/if}/>Send Reminder</label>
        <label for="type_reset" class="radio-inline"><input type="radio" name="type" id="type_reset" value="reset" {if $type == "reset"} checked="checked"{/if}/>Send Reset Link</label>
    </div>
    <div class="form-group">
        <label for="search" class="control-label">Email Address or username</label>
        <input class="form-control" type="text" size="40" name="search" value="{$search}" />
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary" name="btn_reset">Send</button>
    </div>
</form>
{/if}