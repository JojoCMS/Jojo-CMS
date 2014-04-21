{if $content}{$content}
{/if}<div{if $form.form_hideonsuccess && $message && $sent} style="display:none;"{/if}>
<form name="{$form.form_name|escape:'htmlall'}" id="form{$form.form_id}" method="post" action="{$posturl}" enctype="multipart/form-data" class="contact-form{if $form.form_multipage} multipage{/if}{if $form.form_horizontal} form-horizontal{/if}{if $form.form_class} {$form.form_class}{/if}" role="form">
<input type="hidden" name="form_id" id="form_id" value="{$form.form_id}" />
<input type="hidden" name="form_redirect" id="form_redirect" value="{$form.form_redirect_url}" />
<input type="hidden" name="MAX_FILE_SIZE" value="{if $maxuploadvalue}{$maxuploadvalue}{else}5000000{/if}" />
<script type="text/javascript">
		<!--
		function triggerAnalyticsEventTracking(){literal}{{/literal}
			_gaq.push(['_trackEvent', '{$form.form_name} {$form.form_id}', 'submit', '{$pg_url}']);
		{literal}}{/literal}
		
		function fieldsettriggeranalyticstracking($fieldsettitle){literal}{{/literal}
			_gaq.push(['_trackEvent', '{$form.form_name} {$form.form_id}', $fieldsettitle, '{$pg_url}']);
		{literal}}{/literal} 
		-->
	</script>
<div>
{if $toaddresses}<div class="form-fieldset form-group">
        <label for="form_sendto" class="control-label">##Send Enquiry To##<span class="required">*</span></label>
        <div class="form-field controls"><select name="form_sendto" id="form_sendto" class="required"{if $form_choice_multiple} multiple="multiple"{/if}>
            {foreach item=to from=$toaddresses key=k}<option value="{$to.email}"{if $k==0} selected="selected"{/if}>{$to.name}</option>
            {/foreach}
        </select></div>
    </div>
{/if}
{foreach from=$fields key=k item=f }{assign var=x value=`$k-1`}
    {if ($f.fieldset && $f.fieldset!=$fields[$x].fieldset) || $k==0}{if $k>0}</fieldset>
    {/if}<fieldset{if $f.fieldsetid} id="{$f.fieldsetid}"{/if}>{if $f.fieldset && $form.form_fieldsets}<legend>{$f.fieldset}</legend>{/if}
    {/if}{if $f.type!='hidden'}<div class="form-group {if $f.type == 'emailwithconfirmation'}text{elseif !in_array($f.type,array('radio','checkbox'))}{$f.type}{/if}{if $f.class} {$f.class}{/if}">
        {if $f.showlabel || $f.padlabel}<label for="{$f.field}" class="control-label">{if $f.display && $f.showlabel && $f.type!='heading'}{$f.display}{/if}{if $f.required}<span class="required">*</span>{/if}</label>
        {/if}
    {/if}{if $f.type == 'hidden'}<input type="hidden" name="{$f.field}" id="{$f.field}" value="{$f.value}" />
    {elseif $f.type == 'textarea'}<textarea class="form-control input textarea{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" rows="{$f.rows|default:'10'}" cols="{$f.cols|default:'29'}" name="{$f.field}" id="{$f.field}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if}>{$f.value}</textarea>
    {elseif $f.type == 'checkboxes'}{foreach from=$f.options key=ck item=fo}<div class="checkbox{if $f.class} {$f.class}{/if}"><label for="{$f.field}_{$ck}"><input type="checkbox"{if $f.required} class="required"{/if} name="{$f.field}[]" id="{$f.field}_{$ck}" value="{$fo}"{if $f.valuearr}{foreach from=$f.valuearr item=fa}{if $fa==$fo} checked="checked"{/if}{/foreach}{elseif $f.value == 'checked'} checked="checked"{/if} />{$fo}</label></div>
            {/foreach}
    {elseif $f.type == 'radio'}{foreach from=$f.options key=rk item=button}<div class="radio{if $f.class} {$f.class}{/if}"><label for="{$f.field}_{$rk}"><input type="radio"{if $f.required} class="required"{/if} name="{$f.field}" id="{$f.field}_{$rk}" value="{$button}" {if $f.value == $button} checked="checked"{/if} />{$button}</label></div>
            {/foreach}
    {elseif $f.type=='select'}<select name="{$f.field}" id="{$f.field}" class="form-control{if $f.required} required{/if}">
              <option value="">Select</option>
                {foreach from=$f.options item=so}<option value="{$so}"{if $f.value == $so} selected="selected"{/if}>{$so}</option>
                {/foreach}
        </select>
    {elseif $f.type=='list'}<select name="{$f.field}[]" id="{$f.field}" multiple="multiple" class="form-control{if $f.required} required{/if}">
            {foreach from=$f.options item=so}
            <option value="{$so|escape:'htmlall'}"{if $f.value == $so} selected="selected"{/if}>{$so}</option>
            {/foreach}
        </select>
    {elseif $f.type =='heading'}<h{if in_array($f.size, array(1,2,3,4,5,6))}{$f.size}{else}3{/if}{if $f.class} class="{$f.class}"{/if}>{if $f.value}{$f.value}{else}{$f.display}{/if}</h{if in_array($f.size, array(1,2,3,4,5,6))}{$f.size}{else}3{/if}>
    {elseif $f.type=='note'}<p class="{if $f.class}{$f.class}{/if}">{if $f.value}{$f.value}{else}{$f.display}{/if}</p>
    {elseif $f.type=='upload' || $f.type=='privateupload'}<input type="file" class="form-control input fileupload {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" name="FILE_{$f.field}" id="FILE_{$f.field}"  size="{$f.size}" value="" />
    {elseif $f.type=='emailwithconfirmation'}<input type="text" class="form-control input text{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value=""{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />
    {elseif $f.type=='date'}<input type="{if $anytime}text{else}date{/if}" class="form-control input {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}{if $f.validation && $f.validation!=$f.type} {$f.validation}{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} /><a class="cleardata" title="clear field" onclick="$('#{$f.field}').val('');return false;">x</a>
    {else}{if $f.prependvalue || $f.appendvalue}
    <div class="input-group">
        {/if}{if $f.prependvalue}<span class="input-group-addon">{$f.prependvalue}</span>{/if}<input type="{$f.type}" class="form-control input {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}{if $f.validation && $f.validation!=$f.type} {$f.validation}{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />{if $f.appendvalue}<span class="input-group-addon">{$f.appendvalue}</span>{/if}{if $f.prependvalue || $f.appendvalue}
    </div>{/if}
    {/if}
    {if $f.description}<span class="help-block">{$f.description|nl2br}</span>
    {/if}{if $f.type!='hidden'}</div>
    {/if}{if $f.type=='emailwithconfirmation'}
    <div class="form-fieldset text form-group">
        <label for="form_{$f.field}_confirmation" class="control-label">##Confirm Email:##</label>
        <input type="text" class="form-control input text{if $f.class} {$f.class}{/if}" size="{$f.size}" name="{$f.field}_confirmation" id="{$f.field}_confirmation" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />
    </div>
    {/if}{assign var=x value=`$k+1`}{if !$fields[$x]}</fieldset>
    {/if}
{/foreach}
    <div class="form-submit{if $form.form_submit_end} endonly{/if}">
    {if $form.form_captcha}
        <div class="form-group captcha">
            <p class="note">Please enter the {$OPTIONS.captcha_num_chars|default:3} letter code below. This helps us prevent spam. <em>Code is not case-sensitive</em></p>
            <p><img src="external/php-captcha/visual-captcha.php" width="200" height="60" alt="Visual CAPTCHA" /></p>
        </div>
        <div class="form-group">
            <label for="CAPTCHA" class="control-label">Spam prevention<span class="required">*</span></label>
            <input type="text" class="form-control input text required" size="8" name="CAPTCHA" id="CAPTCHA" value="" autocomplete="off" />
        </div>
    {/if}
        <div class="form-group submit">
            {if $form.form_submit_label}<label class="control-label"></label>{/if}<input type="submit" name="contactsubmit" id="contactsubmit" value="{$form.form_submit}" class="btn btn-primary" data-normalval="{$form.form_submit}" /><br />
       </div>
        <div class="progress" style="display: none;">
            <div class="bar"></div >
            <div class="percent">0%</div >
        </div>
    </div>
</div>
</form>
</div>
<div id="form{$form.form_id}message" class="message alert alert-{if $sent}success{else}error{/if}" {if !$message}style="display:none;"{/if}>{$message}</div>
{if $sent && $form.form_tracking_code}{$form.form_tracking_code}
{/if}
{if $anytime}<script type="text/javascript">
$(document).ready(function(){ldelim}
{foreach from=$fields key=k item=f }{if $f.type=='date'}
    $('#{$f.field}').AnyTime_noPicker();
    $('#{$f.field}').AnyTime_picker({ldelim}format: "%M %d, %Y"{rdelim} );
{/if}{/foreach}
{rdelim});
</script>
{/if}
