        {if !$c.nofollow}
        <h4>{if $c.website && $c.useanchortext && $c.anchortext}{$c.name} - <a href="{$c.website}" target="new">{$c.anchortext}</a>{else}{if $c.website}<a href="{$c.website}" target="new">{/if}{$c.name}{if $c.website}</a>{/if}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {elseif $OPTIONS.comment_default_link_type == nofollow}
        <h4>{if $c.website && $c.useanchortext && $c.anchortext}{$c.name} - <a href="{$c.website}" target="new">{$c.anchortext}</a>{else}{if $c.website}<a href="{$c.website}" target="new"{if $c.nofollow=='yes'} rel="nofollow"{/if}>{/if}{$c.name}{if $c.website}</a>{/if}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {elseif $OPTIONS.comment_default_link_type == text}
        <h4>{$c.name}{if $c.website} - {$c.website}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {else}
        <h4>{if $c.website}<a href="#comments" rel="nofollow" onclick="redirectSubmit('{$c.website|replace:'http://':''}'); return false;" title="{$c.website|replace:'http://':''}">{$c.name}</a>{else}{$c.name}{/if}<span class="date"> - {$c.timestamp|date_format}</span></h4>
        {/if}
        <p id="comment-{$c.commentid}" class="comment-text edit-comment">{$c.body}</p>
        <textarea class="edit-comment form-control" style="display:none;">{$c.bbbody}</textarea>
        <a class="edit-comment btn btn-default btn-xs clearfix" href="#comments" onclick="saveComment({$c.commentid});return false;" title="Edit Body" style="display: none;">Save</a>
        {if $editperms}
        <div class="comment_actions pull-right">
              <a class="edit-comment btn btn-default btn-xs" href="#comments" onclick="editComment({$c.commentid});return false;" title="Edit Body">Edit</a>
              <a class="btn btn-danger btn-xs" href="#comments" id="delete-comment-{$c.commentid}" onclick="deleteComment({$c.commentid}); return false;" title="Delete Comment">Delete</a>
            {if $c.website}
                {if $c.nofollow}
              <a class="btn btn-default btn-xs" href="#comments" onclick="nofollowComment({$c.commentid}, 0); return false;" title="Follow Comment - Link juice will be given to this link">Follow</a>
                {else}
              <a class="btn btn-default btn-xs" href="#comments" onclick="nofollowComment({$c.commentid}, 1); return false;" title="Nofollow Comment - Link juice will not be given to this link">No Follow</a>
                {/if}
                {if $c.anchortext && !$c.useanchortext}
              <a class="btn btn-default btn-xs" href="#comments" onclick="anchorComment({$c.commentid}, 1); return false;" title="Follow Comment and use anchor text - Link juice will be given to this link">+ Link Text</a>
                {elseif $c.anchortext}
              <a class="btn btn-default btn-xs" href="#comments" onclick="anchorComment({$c.commentid}, 0); return false;" title="Do not use chosen anchor text for this link">- Link Text</a>
                {/if}
            {/if}
        </div>
        {elseif $user && $user.userid==$c.userid}
        <div class="comment_actions">
              <a class="edit-comment btn btn-default btn-xs" href="#comments" onclick="editComment({$c.commentid});return false;" title="Edit Body">Edit</a>
        </div>
        {/if}