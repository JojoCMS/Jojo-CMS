{if $OPTIONS.comment_show_form == 'no'}<p><a href="#" id="post-comment-link" onclick="$('#post-comment').show(); $('#post-comment-link').hide(); return false;" class="btn btn-default">post a comment</a></p>
{/if}
<div class="post-comment" id="post-comment" style="clear: both;{if $OPTIONS.comment_show_form == 'yes'}display:block;{else}display:none;{/if}">
  <h3>Post Comment</h3>
  {if !$user && $OPTIONS.comment_useronly=='yes'}<p class="note">Comments are restricted to registered users or subscribers only - make sure you enter an email address this site will recognise</p>{/if}
  <form id="commentform-{$pageid}" method="post" action="{$correcturl}" class="contact-form no-ajax">
  <fieldset>
<p class="note"><span class="required">*</span> <em>required fields</em></p>
{if !$user}<input type="hidden" name="userid" id="userid" value="{if $userid}{$userid}{/if}" />{/if}
    <div class="form-group">
        <label for="name" class="control-label">Name <span class="required">*</span></label>
        <input type="text" class="form-control text required" size="40" name="name" id="name" value="{if $name}{$name}{/if}" /> 
    </div>
    {if $user.isadmin}
    <div class="form-group">
        <div class="checkbox">
         <label for="authorcomment"><input type="checkbox" name="authorcomment" id="authorcomment" value="yes" checked="checked" title="Different styling will be used on this comment to indicate it was made by the author" />Author Comment</label>
        </div>
    </div>
    {/if}
    <div class="form-group">
        <label for="email" class="control-label">Email{if $OPTIONS.comment_optional_email != 'yes'} <span class="required">*</span>{/if}</label>
        <input type="text" class="form-control text email{if $OPTIONS.comment_optional_email != 'yes'} required{/if}" size="40" name="email" id="email" value="{if $email}{$email}{/if}" />
    </div>
    {if $user && $OPTIONS.comment_subscriptions == 'yes'}
    <div class="form-group">
        <div class="checkbox">
        <label for="email_subscribe"><input type="checkbox" size="40" name="email_subscribe" id="email_subscribe" value="subscribe"{if $article_user.email_subscribe} checked="checked"{/if} />Send me email notifications of new comments</label>
        </div>
    </div>
    {/if}{if $commentweblink}
    <div class="form-group">
        <label for="website" class="control-label">Website</label>
        <input type="text" class="form-control text" size="40" name="website" id="website" value="{if $website}{$website}{/if}" />
    </div>
    {if $OPTIONS.comment_anchor_text=='yes'}
    <div class="form-group">
        <label for="anchortext" class="control-label">Anchor text</label>
        <input type="text" class="form-control text" size="40" name="anchortext" id="anchortext" value="{if $anchortext}{$anchortext}{/if}" title="If we think your comment is especially good, we will use this anchor text for your link" />
    </div>
    {/if}{/if}
    {if $OPTIONS.comment_captcha=='yes'}{if $OPTIONS.captcha_recaptcha=="yes" && $OPTIONS.captcha_sitekey}<div class="form-group captcha">
        <div class="g-recaptcha" data-sitekey="{$OPTIONS.captcha_sitekey}"></div>
    </div>
    {else}
        <div class="form-group captcha">
            <p class="note">Please enter the {$OPTIONS.captcha_num_chars|default:3} letter code below. This helps us prevent spam. <em>Code is not case-sensitive</em></p>
            <p><img src="external/php-captcha/visual-captcha.php" width="200" height="60" alt="Visual CAPTCHA" /></p>
        </div>
        <div class="form-group">
            <label for="CAPTCHA" class="control-label">Spam prevention<span class="required">*</span></label>
            <input type="text" class="form-control input text required" size="8" name="CAPTCHA" id="CAPTCHA" value="" autocomplete="off" />
        </div>
    {/if}{/if}
    <div class="form-group">
        <label for="comment" class="control-label">Comment <span class="required">*</span></label>
        <textarea name="comment" class="form-control textarea required" id="comment" rows="10" cols="40">{if $comment}{$comment}{/if}</textarea>
    </div>
    <div class="form-group">
        <button type="submit" name="submit" id="submit" class="btn btn-primary">Post Comment</button>
    </div>
  </fieldset>
  </form>
</div>
