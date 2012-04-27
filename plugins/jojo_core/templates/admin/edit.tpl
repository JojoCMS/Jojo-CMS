{include file="admin/header.tpl"}

<script type="text/javascript">{literal}
/* <![CDATA[ */
    var varname;
    var wysiwyg = "{/literal}{$OPTIONS.wysiwyg}{literal}";
	function setTextEditorContent(id) {
		$('.jTagEditor').val('');
		var f = id.replace(/fm_(.*)/ig, "$1");
		if ($("input[name='editor_"+f+"']:checked").val() == 'bb') {
			//$('#'+id+'_bb').val($('#'+id).val());
			$('textarea[name='+id+'_bb]').val($('#'+id).val());
		} else {
			$('textarea[name='+id+'_html]').val($('#'+id).val());
			if (function_exists("{/literal}{$OPTIONS.wysiwyg}{literal}_setEditorContent")) {
				{/literal}{$OPTIONS.wysiwyg}{literal}_setEditorContent(id);
			}
		}
	}
{/literal}
/* ]]> */
</script>

{jojoHook hook="admin_edit_top"}

<div id="records">
    <div id="recordlist">
        {$recordlist}
    </div>
    <div class="pad"></div>
</div><!-- [end records] -->

<!-- [Fields] -->
<div id="fields-wrap">
    <div id="message"><h4>Jojo CMS</h4>{if $message}{$message}{/if}</div>
    <div id="error" style="display:none;">{if $error}{$error}{/if}</div>
    <form name="{$tablename}_form" id="{$tablename}_form" method="post" enctype="multipart/form-data" action="actions/admin-action.php?t={$tablename}" target="frajax-iframe">
        <!-- [Hidden field with ID here] -->
        <input type="hidden" name="id" id="id" value="{$currentrecord|replace:" ":"-"}" />
        <input type="hidden" name="prefix" id="prefix" value="{$prefix}" />

        <div id="buttons">
            {if $addbutton}<input type="submit" onclick="{if false}window.location='{$addnewlink}';{/if}frajax('load','{$tablename}',''); return false;" name="btn_add" value="New" class="button-wrap-add jojo-admin-button" title="Add new {$displayname}" />{/if}

            <input type="submit" name="btn_save" id="btn_save" accesskey="s" value="Save" class="button-wrap-save jojo-admin-button" title="Save the changes to this {$displayname}" />
            {if $deletebutton}<input type="submit" name="btn_delete" id="btn_delete" value="Delete" onclick="return confirmdelete();" class="button-wrap-delete jojo-admin-button" title="Delete to this {$displayname} - this action cannot be undone" />{/if}
            {if $addsimilarbutton}<input type="submit" name="btn_addsimilar" id="btn_addsimilar" value="Copy" class="button-wrap-duplicate jojo-admin-button" title="Create another {$displayname} using selected {$displayname} as a starting point " />{/if}
            {if $addchildbutton}<input type="submit" name="btn_addchild" id="btn_addchild" value="Child" class="button-wrap-addchild jojo-admin-button" title="Add a new {$displayname} underneath this one" />{/if}

            <!--<input type="submit" name="btn_help" id="btn_help" value="Help" class="button-wrap-help button" onmouseover="this.className='button-wrap-help button button-hover';" onmouseout="this.className='button-wrap-help button'" onclick="showhide('help'); return false;" title="Show help information" />-->
            <div class="clear"></div>
        </div><!-- [end buttons] -->



        <div id="tabs">
            <script type="text/javascript">
            /* <![CDATA[ */
            {literal}
                function selecttab(showid) {
                    if ( (showid == '') || (isNull(showid)) || (!document.getElementById('tab-'+showid)) ) {
                        if (document.getElementById('tab-{/literal}{$defaulttab|replace:" ":""}{literal}')) {
                            var showid = '{/literal}{$defaulttab|replace:" ":""}{literal}';
                        } else {
                            var showid = 'Fields';
                        }
                    }
                    {/literal}

                {foreach from=$tabnames item=t}
                    if (document.getElementById('tab-{if $t.tabname == ""}Fields{else}{$t.tabname|replace:" ":""}{/if}')) {ldelim}document.getElementById('tab-{if $t.tabname == ""}Fields{else}{$t.tabname|replace:" ":""}{/if}').style.display = 'none';{rdelim}
                    $('#tabbutton-{if $t.tabname == ""}Fields{else}{$t.tabname|replace:" ":""}{/if}').removeClass('selected');
                {/foreach}
                    {literal}
                    if (document.getElementById('tab-' + showid)) {
                        document.getElementById('tab-' + showid).style.display = 'block';
                        $('#tabbutton-' + showid).addClass('selected');
                    }

                }
            {/literal}
            /* ]]> */
            </script>

            <ul class="tabs">
            {if $numtabs > 1}{foreach from=$tabnames item=t}
                <li id="tabbutton-{if $t.tabname == ""}Fields{else}{$t.tabname|replace:" ":""}{/if}" class="tab"><a href="#" onclick="selecttab('{if $t.tabname == ""}Fields{else}{$t.tabname|replace:" ":""}{/if}'); return false;">{if $t.tabname == ""}Fields{else}{$t.tabname}{/if}</a></li>
            {/foreach}{/if}
            </ul>
            <div class="clear"></div>
        </div><!-- [end tabs] -->

        <div id="fields">
        {foreach from=$tabnames item=tab}
        {if $numtabs > 1}
            <div class="field-page" id="tab-{if $tab.tabname == ""}Fields{else}{$tab.tabname|replace:" ":""}{/if}">
        {/if}
            <table class="stdtable" width="100%">
    {assign var=private value=false}
    {foreach from=$fields key=fieldname item=field}
                {if $field.tabname == $tab.tabname && $field.flags.PRIVACY && $private==false}
                <tr>
                    <td></td><td></td><td><span class="note">Keep private</span></td>
                </tr>
                {assign var=private value=true}
                {/if}
    {/foreach}            
    {foreach from=$fields key=fieldname item=field}
        {if $field.tabname == $tab.tabname}
            {if $field.error}<tr id="row_{$fieldname}" class="error">{else}<tr id="row_{$fieldname}" class="{if $field.type=='hidden' || $field.type=='privacy'}hidden {/if}">{/if}
            {if $field.type=='texteditor' ||  $field.type=='wysiwygeditor' || $field.type=='bbeditor' || $field.showlabel=='no'}
            <td class="col2" colspan="2" id="wrap_{$fieldname}">
            {else}
            <td class="col1">{if $field.type=='permissions'}{$field.name}:{else}<label for="fm_{$fieldname}">{$field.name}:</label>{/if}</td>
            <td class="col2" title="{$field.help|replace:"\"":""}" id="wrap_{$fieldname}">
            {/if}
                {$field.html}
                {if $field.error}<img src="images/cms/icons/error.png" border="0" alt="Error: {$field.error}"  title="Error: {$field.error}" />{/if}
                {if $field.required=="yes"} <img src="images/cms/icons/star.png" title="Required Field" alt="" />{/if}
            </td>

            <td style="width:10px">{if $field.flags.PRIVACY}<input type="hidden" name="hasprivacy[{$fieldname}]" value="1" /><input type="checkbox" name="privacy[{$fieldname}]" id="privacy_{$fieldname}" value="Y"{if $field.privacy=='y' || $field.privacy=='Y'} checked="checked"{/if} />{else}&nbsp;{/if}</td>
            </tr>
        {/if}
    {/foreach}
            </table>


        {if $numtabs > 1}
            </div>
        {/if}
        {/foreach}
        </div>
    </form>
</div>

{jojoHook hook="admin_edit_bottom"}

<script type="text/javascript">{literal}/* <![CDATA[ */

$('#btn_save').click(function(){$('.jTagEditor-editor:visible').change();});


  if (document.getElementById('tabs')) {
    selecttab('Content');
  }
  /* add mouseover effects to new, save, delete buttons */
  $('#buttons input').hover(function(){$(this).addClass('jojo-admin-button-hover');},function(){$(this).removeClass('jojo-admin-button-hover');});
/* ]]> */{/literal}</script>

{include file="admin/footer.tpl"}