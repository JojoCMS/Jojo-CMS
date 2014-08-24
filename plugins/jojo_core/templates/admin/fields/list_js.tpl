{if $allextras}
$(function() {ldelim}
{foreach from=$allextras item=ae}
$('#row_{$ae}').hide();
{/foreach}
{if $extras.$value}$('#row_$extras.$value').show();{/if}
{rdelim});
{/if}