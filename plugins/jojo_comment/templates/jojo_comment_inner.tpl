        {if !$c.nofollow}
        <h4>{if $c.website && $c.useanchortext && $c.anchortext}{$c.name} - <a href="{$c.website}" target="new">{$c.anchortext}</a>{else}{if $c.website}<a href="{$c.website}" target="new">{/if}{$c.name}{if $c.website}</a>{/if}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {elseif $OPTIONS.comment_default_link_type == nofollow}
        <h4>{if $c.website && $c.useanchortext && $c.anchortext}{$c.name} - <a href="{$c.website}" target="new">{$c.anchortext}</a>{else}{if $c.website}<a href="{$c.website}" target="new"{if $c.nofollow=='yes'} rel="nofollow"{/if}>{/if}{$c.name}{if $c.website}</a>{/if}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {elseif $OPTIONS.comment_default_link_type == text}
        <h4>{$c.name}{if $c.website} - {$c.website}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {else}
        <h4>{if $c.website}<a href="#comments" rel="nofollow" onclick="redirectSubmit('{$c.website|replace:'http://':''}'); return false;" title="{$c.website|replace:'http://':''}">{$c.name}</a>{else}{$c.name}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {/if}
        <p id="comment-{$c.commentid}" class="comment-text">{$c.body}</p>
        {if $editperms}
        <div class="comment_actions">
              <a href="#comments" onclick="frajax('jojo_edit_comment',{$c.commentid}); return false;" title="Edit Body"><img class="icon" src="images/cms/icons/comment_edit.png" alt="" /></a>
              <a href="#comments" id="delete-comment-{$c.commentid}" onclick="deleteComment({$c.commentid}); return false;" title="Delete Comment"><img class="icon" src="images/cms/icons/comment_delete.png" alt="" /></a>
            {if $c.website}
                {if $c.nofollow}
              <a href="#comments" onclick="nofollowComment({$c.commentid}, 0); return false;" title="Follow Comment - Link juice will be given to this link"><img class="icon" src="images/cms/icons/link.png" alt="" /></a>
                {else}
              <a href="#comments" onclick="nofollowComment({$c.commentid}, 1); return false;" title="Nofollow Comment - Link juice will not be given to this link"><img class="icon" src="images/cms/icons/link_break.png" alt="" /></a>
                {/if}
                {if $c.anchortext && !$c.useanchortext}
              <a href="#comments" onclick="anchorComment({$c.commentid}, 1); return false;" title="Follow Comment and use anchor text - Link juice will be given to this link"><img class="icon" src="images/cms/icons/text_smallcaps.png" alt="" /></a>
                {elseif $c.anchortext}
              <a href="#comments" onclick="anchorComment({$c.commentid}, 0); return false;" title="Do not use chosen anchor text for this link"><img class="icon" src="images/cms/icons/text_smallcaps.png" alt="" /></a>
                {/if}
            {/if}
        </div>
        {elseif $user && $user.userid==$c.userid}
        <div class="comment_actions">
              <a href="#comments" onclick="frajax('jojo_edit_comment',{$c.commentid}, {$c.userid}); return false;" title="Edit Body"><img class="icon" src="images/cms/icons/comment_edit.png" alt="" /></a>
        </div>
        {/if}