{if $selectededitor == "wysiwyg"}
<div id="editor_{$fd_field}">

<button style="float: right; margin-right: 11px;" onclick="return showWysiwygEditor('fm_{$fd_field}');" title="Launch Fullscreen Editor">
<img class="icon" src="images/cms/icons/page_white_wrench.png" alt="" /> Launch Fullscreen Editor
</button>

<textarea name="fm_{$fd_field}" style="clear: both; display:block; width:98%; height:200px;" id="fm_{$fd_field}" rows="40" cols="40" title="{$fd_help}">
    {$value}
</textarea>
</div>
{/if}