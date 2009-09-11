{if $ac_nofollow=='no'}
<h4>{if $ac_website && ($ac_useanchortext=='yes') && $ac_anchortext}{$ac_name} - <a href="{$ac_website}" target="new">{$ac_anchortext}</a>{else}{if $ac_website}<a href="{$ac_website}" target="new">{/if}{$ac_name}{if $ac_website}</a>{/if}{/if}<span class="date"> - {$ac_timestamp|date_format}</span></h4>
{elseif $OPTIONS.article_default_link_type == nofollow}
<h4>{if $ac_website && ($ac_useanchortext=='yes') && $ac_anchortext}{$ac_name} - <a href="{$ac_website}" target="new">{$ac_anchortext}</a>{else}{if $ac_website}<a href="{$ac_website}" target="new"{if $ac_nofollow=='yes'} rel="nofollow"{/if}>{/if}{$ac_name}{if $ac_website}</a>{/if}{/if}<span class="date"> - {$ac_timestamp|date_format}</span></h4>
{elseif $OPTIONS.article_default_link_type == text}
<h4>{$ac_name}{if $ac_website} - {$ac_website}{/if}<span class="date"> - {$ac_timestamp|date_format}</span></h4>
{else}
<h4>{if $ac_website}<form class="post_redirect_form" method="post" action="redirect/" target="_BLANK"><input type="hidden" name="uri" value="{$ac_website}" /><button class="post_redirect_submit" title="{$ac_website}"><span>{$ac_name}</span></button></form>{else}{$ac_name}{/if}<span class="date"> - {$ac_timestamp|date_format}</span></h4>
{/if}
<p id="article-comment-{$commentid}" class="comment-text">{$ac_body}</p>
{if $editperms}
<div class="article_actions">
      <a href="{$ADMIN}/edit/article/{$jojo_article.articleid}/" onclick="frajax('jojo_article_edit_comment',{$commentid}); return false;" title="Edit Body"><img class="icon" src="images/cms/icons/comment_edit.png" alt="" /></a>
      <a href="{$ADMIN}/edit/article/{$jojo_article.articleid}/" onclick="if (confirmdelete()) {literal}{{/literal}frajax('jojo_article_delete_comment',{$commentid});{literal}}{/literal} return false;" title="Delete Comment"><img class="icon" src="images/cms/icons/comment_delete.png" alt="" /></a>
{if $ac_website}
{if $ac_nofollow == 'yes'}
      <a href="{$ADMIN}/edit/article/{$jojo_article.articleid}/" onclick="frajax('jojo_article_follow_comment',{$commentid},'follow'); return false;" title="Follow Comment - Link juice will be given to this link"><img class="icon" src="images/cms/icons/link.png" alt="" /></a>
{else}
      <a href="{$ADMIN}/edit/article/{$jojo_article.articleid}/" onclick="frajax('jojo_article_follow_comment',{$commentid},'nofollow'); return false;" title="Nofollow Comment - Link juice will not be given to this link"><img class="icon" src="images/cms/icons/link_break.png" alt="" /></a>
{/if}
{if $ac_anchortext}
{if $ac_useanchortext == 'no'}
      <a href="{$ADMIN}/edit/article/{$jojo_article.articleid}/" onclick="frajax('jojo_article_anchortext_comment',{$commentid},'yes'); return false;" title="Follow Comment and use anchor text - Link juice will be given to this link"><img class="icon" src="images/cms/icons/text_smallcaps.png" alt="" /></a>
      {else}
      <a href="{$ADMIN}/edit/article/{$jojo_article.articleid}/" onclick="frajax('jojo_article_anchortext_comment',{$commentid},'no'); return false;" title="Do not use chosen anchor text for this link"><img class="icon" src="images/cms/icons/text_smallcaps.png" alt="" /></a>
{/if}
{/if}
{/if}
</div>
{/if}