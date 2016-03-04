<div class="login-form">
    <h3>{$loginmessage|default:"Log in"}</h3>
    {jojoHook hook="login_before_form"}
    <form method="post" action="{if $issecure}{$SECUREURL}{else}{$SITEURL}{/if}/{$RELATIVE_URL}" class="contact-form no-ajax no-validate">
        <input type="hidden" name="_jojo_authtype" value="local" />
        {if $redirect}<input type="hidden" name="redirect" id="redirect" value="{$redirect}" />{/if}
        <div class="form-group">
            <label for="username" class="control-label">Username{if $OPTIONS.allow_email_login=='yes'} or Email Address{/if}</label>
            <input class="form-control" type="text" name="username" tabindex=10 id="username" value="{if $username}{$username}{/if}" />
        </div>
        <div class="form-group">
            <label for="password" class="control-label">Password</label>
            <input class="form-control" type="password" name="password" tabindex=20 id="password" value="{if $password}{$password}{/if}" title="Passwords are case-sensitive." />
        </div>
        {* 
        <div class="form-group">
            <div class="checkbox"><label for="remember" title="This option will log you in automatically from this computer."><input type="checkbox" name="remember" tabindex=30 id="remember" value="1" {if $remember=="1"} checked{/if} />Remember Password</label></div>
        </div>
        *}
        <div class="form-group submit">
            <label for="submit" class="control-label"></label><button type="submit" class="btn btn-primary" name="submit" tabindex=40 id="submit">Login</button>
        </div>
      </form>
    <p><a href="forgot-password/" class="note" title="Options for recovering a lost password" rel="nofollow">Forgotten Password?</a></p>
</div>
