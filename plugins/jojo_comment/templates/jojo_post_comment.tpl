<a name="add-comment"></a>
{if $OPTIONS.comment_show_form == 'no'}<a href="#" id="post-comment-link" onclick="$('#post-comment').show(); $('#post-comment-link').hide(); return false;">{if $commentbutton}<img src="images/post-comment.gif" alt="Post Comment" style="border: 0;" />{else}post a comment{/if}</a>{/if}
<div class="post-comment" id="post-comment" style="clear: both;{if $OPTIONS.comment_show_form == 'yes'}display:block;{/if}">
  <h3>Post Comment</h3>
  <form method="post" action="" onsubmit="return checkComment({if $OPTIONS.comment_optional_email != 'yes'}true{else}false{/if});">
    {if !$user}<input type="hidden" name="userid" id="userid" value="{if $userid}{$userid}{/if}" />{/if}
    <label for="name">Name</label>
    <input type="text" size="40" name="name" id="name" value="{if $name}{$name}{/if}" /> *<br />

    {if $user.isadmin}
    <label for="authorcomment">Author Comment</label>
    <input type="checkbox" name="authorcomment" id="authorcomment" value="yes" checked="checked" title="Different styling will be used on this comment to indicate it was made by the author" /><br />
    {/if}

    <label for="email">Email</label>
    <input type="text" size="40" name="email" id="email" value="{if $email}{$email}{/if}" />{if $OPTIONS.comment_optional_email != 'yes'} *{/if}<br />

    {if $user && $OPTIONS.comment_subscriptions == 'yes'}
    <label for="email_subscribe">Subscribe to updates</label>
    <input type="checkbox" size="40" name="email_subscribe" id="email_subscribe" value="subscribe"{if $article_user.email_subscribe} checked="checked"{/if} /><span>Receive email notifications for new comments</span><br />
    {/if}

    <label for="website">Website</label>
    <input type="text" size="40" name="website" id="website" value="{if $website}{$website}{/if}" /><br />

    {if $OPTIONS.comment_anchor_text=='yes'}
    <label for="anchortext">Anchor text</label>
    <input type="text" size="40" name="anchortext" id="anchortext" value="{if $anchortext}{$anchortext}{/if}" title="If we think your comment is especially good, we will use this anchor text for your link" /><br />
    {/if}
    {if !$user}
    <label for="captchacode">Are you Human?</label>
    <div class="post-comment-field">
      <img src="external/php-captcha/visual-captcha.php" width="200" height="60" alt="Visual CAPTCHA" /><br />
      <label for="captchacode">Enter code shown above</label><br />
      <input type="text" size="8" name="captchacode" id="captchacode" value="{if $captchacode}{$captchacode}{/if}" /> *<br />
      <em>Code is not case-sensitive</em>
    </div>
    {/if}

    <label for="comment">Comment</label>
    <div class="post-comment-field">
      <textarea name="comment" id="comment" rows="10" cols="40">{if $comment}{$comment}{/if}</textarea> *<br />
    </div>

    <label for="submit">&nbsp;</label>
    <input type="submit" name="submit" id="submit" value="Post Comment &gt;&gt;" class="button" /><br />
  </form>
  <div class="note">We welcome comments provided they have something to contribute. Please note that all links will be created using the nofollow attribute. This is a spam free zone. HTML is stripped from comments, but BBCode is allowed.</div>
</div>