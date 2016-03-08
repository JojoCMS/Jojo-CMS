{if $messages}<div class="message alert alert-success">{$messages}</div>{/if}
{if $error}<div class="error alert alert-danger">{$error}</div>{/if}
{if !$success}
<p>Please confirm your existing password, and enter a new password.</p>

<h3>Guidelines for Passwords</h3>
<ul>
  <li>Passwords are case sensitive</li>
  <li>Must be between {$minlength} and {$maxlength} characters</li>
  <li>Must contain a number</li>
</ul>

<h3>Change your password</h3>
<form method="post" action="{$REQUEST_URI}" class="contact-form no-ajax no-validate" role="form" onsubmit="return checkme_changepass()">
    <div class="form-group">
        <label for="oldp" class="control-label">Current Password: <span class="required">*</span></label>
        <input class="form-control" type="password" name="oldp" id="oldp" value="{if $oldp}{$oldp}{/if}" size="25" />
    </div>
    <div class="form-group">
        <label for="newp" class="control-label">New Password: <span class="required">*</span></label>
        <input class="form-control" type="password" name="newp" id="newp" value="{if $newp}{$newp}{/if}" size="25" onkeyup="checkPassword(this.value)" />
    </div>
    <div class="form-group">
        <label for="newp2" class="control-label">Confirm New Password: <span class="required">*</span></label>
        <input class="form-control" type="password" name="newp2" id="newp2" value="{if $newp2}{$newp2}{/if}" size="25" />
    </div>
    <div class="form-group">
        <label class="control-label">Password Strength:</label>
        <div style="border: 1px solid #888; background: #fff; width: 80px; padding: 1px;vertical-align: middle; display: inline-block;"><div id="progressBar" style=" height: 20px; width: 0;"></div></div><br />
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary" name="reset" value="reset">Change</button>
    </div>
</form>
{/if}