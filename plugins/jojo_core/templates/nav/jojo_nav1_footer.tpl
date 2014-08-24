{assign var='_footerClass' value='first-child'}
{foreach from=$footernav item=n}
<a class='{$_footerClass}' href="{$n.url}" title="{$n.title|escape:"html"}"{if $n.pg_followto=='no'} rel="nofollow"{/if}>{$n.label}</a>
{assign var='_footerClass' value=''}{/foreach}