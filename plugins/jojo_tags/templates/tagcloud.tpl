<div class="tag-cloud">
{if $related}
{foreach from=$cloudwords key=word item=c}
    <span style="font-size: {$c.fontsize}em;" title="{$word}"><a href="{$prefix}/{$c.url}/" >{$word}</a></span>
{/foreach}
{else}
{foreach from=$cloudwords key=word item=c}
    <span style="font-size: {$c.fontsize}em;" title="{$word}"><a href="{$prefix}/{$c.url}/">{$word}</a></span>
{/foreach}
{/if}
</div>