{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}" value="{$value}" />
{if $value}{$value}{/if}{if $fd_units} {$fd_units}{/if}
{else}
{if $counter > 0}
<input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}" {if $error != ""}class="error"{/if} {if $fd_maxsize > 0}maxlength="{$fd_maxsize}"{/if} {if $counter > 0}onkeydown="countDown('fm_{$fd_field}', 'fm_{$fd_field}_counter', {$counter});" onkeyup="countDown('fm_{$fd_field}','fm_{$fd_field}_counter', {$counter});"{/if} {if $readonly == "yes"}readonly="readonly"{/if} title="{$fd_help}" />{if $fd_units} {$fd_units}{/if}
<input readonly="readonly" type="text" class="counter" name="fm_{$fd_field}_counter" id="fm_{$fd_field}_counter" size="3" maxlength="3" value="{$counterstrlen}" />
<script type="text/javascript">countDown('fm_{$fd_field}','fm_{$fd_field}_counter',{$counter});</script>
{else}
<input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}" size="{$fd_size}" value="{$value}" {if $error != ""}class="error"{/if}  title="{$fd_help}" />{if $fd_units} {$fd_units}{/if}
{/if}
{/if}