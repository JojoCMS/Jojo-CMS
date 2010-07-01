{if $cloudwords}
<div class="tag-cloud">
<p>{foreach from=$cloudwords key=word item=c}<span> <a href="{$prefix}/{$c.url}/"  style="font-size: {$c.fontsize}em;"  title="{$c.cleanword}" >{$c.cleanword}</a> </span>{/foreach}</p>
</div>
{/if}