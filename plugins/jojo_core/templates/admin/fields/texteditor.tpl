
{* HTML Editor *}
{if $OPTIONS.wysiwyg_style=='popup'}
<div id="editor_{$fd_field}_html" class="HTMLEditor"  style="display: none;color:#666677;">
    <button class="jojo-admin-button-launcheditor" onclick="{strip}
        $('#xinha-iframe').attr('src','{$SITEURL}/external/wysiwyg-interface/xinha.php?field=fm_{$fd_field}_html');
        jpop($('#wysiwyg-popup'),820,525);
        jpopOnClose(function(){literal}{{/literal}document.getElementById('xinha-iframe').contentWindow.commit();{literal}}{/literal});
        return false;{/strip}" title="Open WYSIWYG Editor">
        Open Editor
    </button>
    <div class="clear" id="fm_{$fd_field}_html_outer">
        <textarea class="markItUp" style="width:100%; margin: 0 auto; height:200px;" name="fm_{$fd_field}_html" id="fm_{$fd_field}_html" rows="{$rows}" cols="{$cols}" {if $readonly}readonly="readonly"{/if} ></textarea>
    </div>
</div>
{else}
<textarea class="xinha" id="fm_{$fd_field}_xinha" name="fm_{$fd_field}_html" style="width:100%"></textarea>

{/if}

{* BB Editor *}
<div id="editor_{$fd_field}_bb" class="BBEditor" style="display: none;color:#666677;">{$fd_name}:

    <button class="jojo-admin-button-launcheditor" onclick="{strip}
        jpop($('#fm_{$fd_field}_bb_outer'),800,500);
        $('#fm_{$fd_field}_bb').height('450px');
        $('#jpop_overlay').click(function(){literal}{{/literal}$('#fm_{$fd_field}_bb_outer').removeClass('jpop_content').height('auto').width('auto').show(); $('#fm_{$fd_field}_bb').height('200px');{literal}}{/literal});
        return false;{/strip}" title="Launch Fullscreen Editor">
        <img class="icon" src="images/cms/icons/page_white_wrench.png" alt="Launch Fullscreen Editor" />Launch Fullscreen Editor
    </button>
    <div class="clear" id="fm_{$fd_field}_bb_outer" style="overflow: auto;">
        <textarea class="markItUp" istyle="width:90%; margin: 0 auto; height:200px;" name="fm_{$fd_field}_bb" id="fm_{$fd_field}_bb" rows="{$rows}" cols="{$cols}" {if $readonly}readonly="readonly"{/if}></textarea>
    </div>
</div>

{* Select an Editor *}
<div class="editor-format" style="float:right;">
  <label class="control-label">Editor Format:</label>
  <label for="type_fm_{$fd_field}_html" class="radio-inline">
      {if $editortype != "bb"}{* this required for AJAX loading *}<!-- [editor:html] -->{/if}
      <input type="radio"{if $editortype != "bb"} checked="checked"{/if} name="editor_{$fd_field}" id="type_fm_{$fd_field}_html" onclick="$('#editor_{$fd_field}_bb').hide(); $('#editor_{$fd_field}_html').show(); setTextEditorContent('fm_{$fd_field}');" value="html" /> HTML
  </label>
  <label for="type_fm_{$fd_field}_bb" class="radio-inline">
    {if $editortype == "bb"}{* this required for AJAX loading *}<!-- [editor:bb] -->{/if}
    <input type="radio"{if $editortype == "bb"} checked="checked"{/if} name="editor_{$fd_field}" id="type_fm_{$fd_field}_bb" onclick="$('#editor_{$fd_field}_bb').show(); $('#editor_{$fd_field}_html').hide(); setTextEditorContent('fm_{$fd_field}');" value="bb" /> BBCode
  </label>
  <a href="http://www.jojocms.org/docs/38/wysiwyg-vs-bbcode/" target="_BLANK">
      <img src="images/cms/icons/help.png" alt="" title="The difference between WYSIWYG editor and BBCode editor" />
  </a>
</div>

{* Raw content *}
<div class="hidden"><textarea name="fm_{$fd_field}" id="fm_{$fd_field}" rows="10" cols="30">{$value}</textarea></div>

{* Initialize editors *}

<script type="text/javascript">
$('textarea[name=fm_{$fd_field}_bb]').change(function(){ldelim}if($('#type_fm_{$fd_field}_bb').attr('checked')){ldelim}$('#fm_{$fd_field}').val($(this).val());{rdelim}{rdelim});
$('textarea[name=fm_{$fd_field}_html]').change(function(){ldelim}if($('#type_fm_{$fd_field}_html').attr('checked')){ldelim}$('#fm_{$fd_field}').val($(this).val());{rdelim}{rdelim});

$(document).ready(function() {ldelim}
    {if $OPTIONS.wysiwyg_style=='popup'}$("#fm_{$fd_field}_html").markItUp(myHtmlSettings);
    $("#fm_{$fd_field}_bb").markItUp(myBbSettings);
    {/if}setTextEditorContent("fm_{$fd_field}");
    $('#editor_{$fd_field}_{$editortype}').show();
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {ldelim}
        startVisibleXinhaEditors();
    {rdelim})
{rdelim});
</script>