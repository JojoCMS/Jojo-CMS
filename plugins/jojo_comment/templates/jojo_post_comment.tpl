<a name="add-comment"></a>
{if $OPTIONS.comment_show_form == 'no'}<a href="#" id="post-comment-link" onclick="$('#post-comment').show(); $('#post-comment-link').hide(); return false;" class="btn btn-default">post a comment</a>{/if}
<div class="post-comment" id="post-comment" style="clear: both;{if $OPTIONS.comment_show_form == 'yes'}display:block;{else}display:none;{/if}">
  <h3>Post Comment</h3>
  <form id="commentform-{$pageid}" method="post" action="" class="contact-form no-ajax">
  <fieldset>
<p class="note"><span class="required">*</span> <em>required fields</em></p>
{if !$user}<input type="hidden" name="userid" id="userid" value="{if $userid}{$userid}{/if}" />{/if}
    <div class="form-fieldset control-group">
        <label for="name" class="control-label">Name <span class="required">*</span></label>
        <input type="text" class="input text required" size="40" name="name" id="name" value="{if $name}{$name}{/if}" /> 
    </div>
    {if $user.isadmin}
    <div class="form-fieldset control-group">
        <div class="form-field controls">
         <label for="authorcomment" class="checkbox"><input type="checkbox" name="authorcomment" id="authorcomment" value="yes" checked="checked" title="Different styling will be used on this comment to indicate it was made by the author" />Author Comment</label>
        </div>
    </div>
    {/if}
    <div class="form-fieldset control-group">
        <label for="email" class="control-label">Email{if $OPTIONS.comment_optional_email != 'yes'} <span class="required">*</span>{/if}</label>
        <input type="text" class="input text email{if $OPTIONS.comment_optional_email != 'yes'} required{/if}" size="40" name="email" id="email" value="{if $email}{$email}{/if}" />
    </div>
    {if $user && $OPTIONS.comment_subscriptions == 'yes'}
    <div class="form-fieldset control-group">
        <div class="form-field controls">
        <label for="email_subscribe" class="checkbox"><input type="checkbox" size="40" name="email_subscribe" id="email_subscribe" value="subscribe"{if $article_user.email_subscribe} checked="checked"{/if} />Send me email notifications of new comments</label>
        </div>
    </div>
    {/if}{if $commentweblink}
    <div class="form-fieldset control-group">
        <label for="website" class="control-label">Website</label>
        <input type="text" class="input text" size="40" name="website" id="website" value="{if $website}{$website}{/if}" />
    </div>
    {if $OPTIONS.comment_anchor_text=='yes'}
    <div class="form-fieldset control-group">
        <label for="anchortext" class="control-label">Anchor text</label>
        <input type="text" class="input text" size="40" name="anchortext" id="anchortext" value="{if $anchortext}{$anchortext}{/if}" title="If we think your comment is especially good, we will use this anchor text for your link" />
    </div>
    {/if}{/if}
    {if !$user}
    <div class="form-fieldset control-group">
        <label for="CAPTCHA" class="control-label">Spam prevention <span class="required">*</span></label>
        <input type="text" class="input text required" size="8" name="CAPTCHA" id="CAPTCHA" value="" />
    </div>
    <div class="form-fieldset control-group captcha">
        <p class="note">Please enter the {$OPTIONS.captcha_num_chars|default:3} letter code below. This helps us prevent spam. <em>Code is not case-sensitive</em></p>
        <p><img src="{$SITEURL}/external/php-captcha/visual-captcha.php" width="200" height="60" alt="Visual CAPTCHA" /></p>
    </div>
    {/if}
    <div class="form-fieldset control-group">
        <label for="comment" class="control-label">Comment <span class="required">*</span></label>
        <textarea name="comment" class="input textarea required" id="comment" rows="10" cols="40">{if $comment}{$comment}{/if}</textarea>
    </div>
    <div class="form-fieldset control-group">
        <label for="submit" class="control-label">&nbsp;</label>
        <input type="submit" name="submit" id="submit" value="Post Comment" class="btn btn-default" />
  </div>
  <div class="clear"></div>
  </fieldset>
  </form>
  <div class="note">We welcome comments provided they have something to contribute. Please note that all links will be created using the nofollow attribute. This is a spam free zone. HTML is stripped from comments, but BBCode is allowed.</div>
</div>
