<div class="center">
<p><strong>{$loginmessage|default:"Please login to view this page"}</strong></p>
{jojoHook hook="login_before_form"}
<div id="login-form">
  <form method="post" action="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/{$RELATIVE_URL}" class="contact-form">
    <input type="hidden" name="_jojo_authtype" value="local" />
    {if $redirect}<input type="hidden" name="redirect" id="redirect" value="{$redirect}" />{/if}
    <div class="form-fieldset">
        <label for="username">Username:</label>
        <input type="text" name="username" tabindex=10 id="username" value="{if $username}{$username}{/if}" />
    </div>
    <div class="form-fieldset">
        <label for="password">Password:</label>
        <input type="password" name="password" tabindex=20 id="password" value="{if $password}{$password}{/if}" title="Passwords are case-sensitive." />
    </div>
    <div class="form-fieldset">
        <label for="remember" title="This option will log you in automatically from this computer." class="note">Remember Password:</label>
        <input type="checkbox" name="remember" tabindex=30 id="remember" value="1" {if $remember=="1"} checked{/if} />
    </div>
    <div class="form-fieldset">
        <label for="submit" class="hidden">Login:</label>
        <input type="submit" class="button" name="submit" tabindex=40 id="submit" value="Login &gt;&gt;" onmouseover="this.className='button buttonrollover';" onmouseout="this.className='button'" /><br />
    </div>
      </form>
    </div>
    <p><a href="forgot-password/" class="note" title="Options for recovering a lost password" rel="nofollow">Forgotten Password?</a></p>
</div>
