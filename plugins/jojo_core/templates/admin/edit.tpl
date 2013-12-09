{include file="admin/header.tpl"}
<div id="wysiwyg-popup" class="jpop"><iframe name="xinha-iframe" id="xinha-iframe" src="" style="width: 820px; height: 525px;"></iframe></div>

<script type="text/javascript">{literal}
/* <![CDATA[ */
    var varname;
{/literal}
/* ]]> */
</script>

{jojoHook hook="admin_edit_top"}
<div class="row">
<div id="records" class="col-md-4">
    <div id="recordlist">
        {$recordlist}
    </div>
    <div class="pad"></div>
</div><!-- [end records] -->

<!-- [Fields] -->
<div id="fields-wrap" class="col-md-8">
    <div id="message" class="alert alert-block alert-info"><h4>Jojo CMS</h4>{if $message}{$message}{/if}</div>
    <div id="error" style="display:none;" class="alert alert-block alert-error">{if $error}{$error}{/if}</div>
    <form name="{$tablename}_form" id="{$tablename}_form" method="post" enctype="multipart/form-data" action="actions/admin-action.php?t={$tablename}" target="frajax-iframe" class="form-horizontal">
        <!-- [Hidden field with ID here] -->
        <input type="hidden" name="id" id="id" value="{$currentrecord|replace:" ":"-"}" />
        <input type="hidden" name="prefix" id="prefix" value="{$prefix}" />

        <div id="buttons" class="btn-group">
            {if $addbutton}<input type="submit" onclick="{if false}window.location='{$addnewlink}';{/if}frajax('load','{$tablename}',''); return false;" name="btn_add" value="New" class="btn btn-default" title="Add new {$displayname}" />{/if}
            <input type="submit" name="btn_save" id="btn_save" accesskey="s" value="Save" class="btn btn-default" title="Save the changes to this {$displayname}" />
            {if $deletebutton}<input type="submit" name="btn_delete" id="btn_delete" value="Delete" onclick="return confirmdelete();" class="btn btn-default" title="Delete to this {$displayname} - this action cannot be undone" />{/if}
            {if $addsimilarbutton}<input type="submit" name="btn_addsimilar" id="btn_addsimilar" value="Copy" class="btn btn-default" title="Create another {$displayname} using selected {$displayname} as a starting point " />{/if}
            {if $addchildbutton}<input type="submit" name="btn_addchild" id="btn_addchild" value="Child" class="btn btn-default" title="Add a new {$displayname} underneath this one" />{/if}
        </div><!-- [end buttons] -->

        {if $numtabs > 1}
        <div id="tabs">
            <ul class="nav nav-tabs">
            {foreach from=$tabnames key=k item=t}<li{if $k==0} class="active"{/if}><a href="#tab-{if $t.tabname == ""}Fields{else}{$t.tabname|replace:" ":""|replace:".":""}{/if}" data-toggle="tab">{if $t.tabname == ""}Fields{else}{$t.tabname}{/if}</a></li>
            {/foreach}
            </ul>
        </div>
        {/if}
        <div id="fields" class="tab-content">
            {foreach from=$tabnames key=k item=tab}
            {if $numtabs > 1}
            <div class="tab-pane{if $k==0} active{/if}" id="tab-{if $tab.tabname == ""}Fields{else}{$tab.tabname|replace:" ":""|replace:".":""}{/if}">
            {/if}
          
            {foreach from=$fields key=fieldname item=field}
            {if $field.tabname == $tab.tabname}
                <div id="row_{$fieldname}" class="form-group{if $field.error} error{/if}{if $field.type=='hidden' || $field.type=='privacy'} hidden{/if}">
                
                    {if $field.showlabel=='no'}{elseif $field.type=='texteditor' ||  $field.type=='wysiwygeditor' || $field.type=='bbeditor' || $field.type=='permissions'}{$field.name}:{else}<label for="fm_{$fieldname}" class="col-md-2">{$field.name}:</label>{/if}
                    <div title="{$field.help|replace:"\"":''}" id="wrap_{$fieldname}"{if !($field.type=='texteditor' ||  $field.type=='wysiwygeditor' || $field.type=='bbeditor' || $field.showlabel=='no')} class="col-md-10"{/if}>
                        {$field.html}
                        {if $field.error}<img src="images/cms/icons/error.png" border="0" alt="Error: {$field.error}"  title="Error: {$field.error}" />{/if}
                        {if $field.required=="yes"}<i class="input-append icon-exclamation-sign"></i>{/if}
                        {if $field.flags.PRIVACY}&nbsp;&nbsp;<input type="hidden" name="hasprivacy[{$fieldname}]" value="1" /><label class="checkbox inline note"><input type="checkbox" name="privacy[{$fieldname}]" id="privacy_{$fieldname}" value="Y"{if $field.privacy=='y' || $field.privacy=='Y'} checked="checked"{/if} />Private</label>{/if}
                    </div>

                </div>
            {/if}
            {/foreach}

            {if $numtabs > 1}
            </div>
            {/if}
            {/foreach}
        </div>
    </form>
</div>
</div>
{jojoHook hook="admin_edit_bottom"}

<script type="text/javascript">{literal}/* <![CDATA[ */

$('#btn_save').click(function(){$('.jTagEditor-editor:visible').change();});
/* ]]> */{/literal}</script>

{include file="admin/footer.tpl"}