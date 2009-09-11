<div class="center">
<strong>{$loginmessage|default:"Please login to view this page"}</strong><br />
{jojoHook hook="login_before_form"}
<div id="login-form">
  <form method="post" action="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/{$RELATIVE_URL}">
    <input type="hidden" name="_jojo_authtype" value="local" />
    {if $redirect}<input type="hidden" name="redirect" id="redirect" value="{$redirect}" />{/if}

    <label for="username">Username:</label>
    <input type="text" name="username" id="username" value="{if $username}{$username}{/if}" /><br />

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" value="{if $password}{$password}{/if}" title="Passwords are case-sensitive." /><br />

    <label for="remember" title="This option will log you in automatically from this computer.">Remember Password:</label>
    <input type="checkbox" name="remember" id="remember" value="1" {if $remember=="1"} checked{/if} /><br />

    <label for="submit">Login:</label>
    <input type="submit" class="button" name="submit" id="submit" value="Login &gt;&gt;" onmouseover="this.className='button buttonrollover';" onmouseout="this.className='button'" /><br />

  </form>
</div>

<a href="forgot-password/" title="Options for recovering a lost password" rel="nofollow">Forgotten Password?</a>
</div>