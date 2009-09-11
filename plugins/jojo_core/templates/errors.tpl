{* 

This is a standard template for displaying errors within Jojo plugins. It assumes you have an array called $errors containing error messages. You may also like to define a CSS style for div.errors
To include this code in your plugin, simply add {include file='errors.tpl'} where you would like the error to appear.

*}
{if $errors}<div class="errors">
<ul>
{foreach  item=e from=$errors}
  <li>{$e}</li>
{/foreach}
</ul>
</div>{/if}