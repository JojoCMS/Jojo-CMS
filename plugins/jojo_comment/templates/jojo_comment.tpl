<a name="comments"></a>
<div id="comments">
    <h3>{if $numcomments}<span id="numcomments">{$numcomments}</span> Comment{if $numcomments >1}s{/if}{else}Comments{/if}</h3>
{foreach from=$comments item=c}
    <div class="comment{if $c.authorcomment} author{/if}" id="comment-wrap-{$c.commentid}">
        {if !$c.nofollow}
        <h4>{if $c.website && $c.useanchortext && $c.anchortext}{$c.name} - <a href="{$c.website}" target="new">{$c.anchortext}</a>{else}{if $c.website}<a href="{$c.website}" target="new">{/if}{$c.name}{if $c.website}</a>{/if}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {elseif $OPTIONS.comment_default_link_type == nofollow}
        <h4>{if $c.website && $c.useanchortext && $c.anchortext}{$c.name} - <a href="{$c.website}" target="new">{$c.anchortext}</a>{else}{if $c.website}<a href="{$c.website}" target="new"{if $c.nofollow=='yes'} rel="nofollow"{/if}>{/if}{$c.name}{if $c.website}</a>{/if}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {elseif $OPTIONS.comment_default_link_type == text}
        <h4>{$c.name}{if $c.website} - {$c.website}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {else}
        <h4>{if $c.website}<form class="post_redirect_form" method="post" action="redirect/" target="_BLANK"><input type="hidden" name="uri" value="{$c.website}" /><button class="post_redirect_submit" title="{$c.website}"><span>{$c.name}</span></button></form>{else}{$c.name}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {/if}
        <p id="comment-{$c.commentid}" class="comment-text">{$c.body}</p>
        {if $editperms}
        <div class="comment_actions">
              <a href="#comments" onclick="frajax('jojo_edit_comment',{$c.commentid}); return false;" title="Edit Body"><img class="icon" src="images/cms/icons/comment_edit.png" alt="" /></a>
              <a href="#comments" class="delete-comment" id="delete-comment-{$c.commentid}" onclick="if (confirmdelete()) {literal}{{/literal}frajax('jojo_delete_comment',{$c.commentid});{literal}}{/literal} return false;" title="Delete Comment"><img class="icon" src="images/cms/icons/comment_delete.png" alt="" /></a>
            {if $c.website}
                {if $c.nofollow}
              <a href="#comments" id="follow-{$c.commentid}" onclick="frajax('jojo_follow_comment',{$c.commentid},'follow'); return false;" title="Follow Comment - Link juice will be given to this link"><img class="icon" src="images/cms/icons/link.png" alt="" /></a>
                {else}
              <a href="#comments" id="follow-{$c.commentid}" onclick="frajax('jojo_follow_comment',{$c.commentid},'nofollow'); return false;" title="Nofollow Comment - Link juice will not be given to this link"><img class="icon" src="images/cms/icons/link_break.png" alt="" /></a>
                {/if}
                {if !$c.anchortext}
              <a href="#comments" onclick="frajax('jojo_anchortext_comment',{$c.commentid},'yes'); return false;" title="Follow Comment and use anchor text - Link juice will be given to this link"><img class="icon" src="images/cms/icons/text_smallcaps.png" alt="" /></a>
                {else}
              <a href="#comments" onclick="frajax('jojo_anchortext_comment',{$c.commentid},'no'); return false;" title="Do not use chosen anchor text for this link"><img class="icon" src="images/cms/icons/text_smallcaps.png" alt="" /></a>
                {/if}
            {/if}
        </div>
        {/if}
    </div>
{/foreach}
</div>
