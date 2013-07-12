<div class="center">
<p><strong>{$loginmessage|default:"Please login to view this page"}</strong></p>
{jojoHook hook="login_before_form"}
<div id="login-form">
  <form method="post" action="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/{$RELATIVE_URL}" class="contact-form no-ajax no-validate">
    <input type="hidden" name="_jojo_authtype" value="local" />
    {if $redirect}<input type="hidden" name="redirect" id="redirect" value="{$redirect}" />{/if}
    <div class="form-fieldset control-group">
        <label for="username" class="control-label">Username</label>
        <input type="text" name="username" tabindex=10 id="username" value="{if $username}{$username}{/if}" />
    </div>
    <div class="form-fieldset control-group">
        <label for="password" class="control-label">Password</label>
        <input type="password" name="password" tabindex=20 id="password" value="{if $password}{$password}{/if}" title="Passwords are case-sensitive." />
    </div>
    <div class="form-fieldset control-group">
        <div class="form-field controls"><label for="remember" class="checkbox" title="This option will log you in automatically from this computer."><input type="checkbox" name="remember" tabindex=30 id="remember" value="1" {if $remember=="1"} checked{/if} />Remember Password</label></div>
    </div>
    <div class="form-fieldset control-group submit">
        <label for="submit" class="control-label"></label><input type="submit" class="button btn" name="submit" tabindex=40 id="submit" value="Login" onmouseover="this.className='button btn buttonrollover';" onmouseout="this.className='button btn'" /><br />
    </div>
      </form>
    </div>
    <p><a href="forgot-password/" class="note" title="Options for recovering a lost password" rel="nofollow">Forgotten Password?</a></p>
</div>
