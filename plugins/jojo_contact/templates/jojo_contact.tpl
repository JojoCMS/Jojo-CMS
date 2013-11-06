{if $content}{$content}
{/if}<div{if $form.form_hideonsuccess && $message && $sent} style="display:none;"{/if}>
<form name="{$form.form_name|escape:'htmlall'}" id="form{$form.form_id}" method="post" action="{$posturl}" enctype="multipart/form-data" class="contact-form{if $form.form_multipage} multipage{/if}{if $form.form_horizontal} form-horizontal{/if}{if $form.form_class} {$form.form_class}{/if}">
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
{if $toaddresses}<div class="form-fieldset control-group">
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
    {/if}{if !in_array($f.type,array('heading','note')) && $f.type!='hidden'}<div class="form-fieldset control-group {if $f.type == 'emailwithconfirmation'}text{else}{$f.type}{/if}{if $f.prependvalue} input-prepend{/if}{if $f.appendvalue} input-append{/if}{if $f.class} {$f.class}{/if}">
        {if $f.showlabel || $f.padlabel}<label for="{$f.field}" class="control-label">{if $f.display && $f.showlabel}{$f.display}{/if}{if $f.required}<span class="required">*</span>{/if}</label>
        {/if}
    {/if}{if $f.type == 'hidden'}<input type="hidden" name="{$f.field}" id="{$f.field}" value="{$f.value}" />
    {elseif $f.type == 'textarea'}<div class="form-field controls"><textarea class="input textarea{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" rows="{$f.rows|default:'10'}" cols="{$f.cols|default:'29'}" name="{$f.field}" id="{$f.field}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if}>{$f.value}</textarea></div>
    {elseif $f.type == 'checkboxes'}<div class="form-field controls">
            {foreach from=$f.options key=ck item=fo}<label for="{$f.field}_{$ck}" class="checkbox{if $f.class} {$f.class}{/if}"><input type="checkbox"{if $f.required} class="required"{/if} name="{$f.field}[]" id="{$f.field}_{$ck}" value="{$fo}"{if $f.valuearr}{foreach from=$f.valuearr item=fa}{if $fa==$fo} checked="checked"{/if}{/foreach}{elseif $f.value == 'checked'} checked="checked"{/if} />{$fo}</label>
            {/foreach}
            </div>
    {elseif $f.type == 'radio'}<div class="form-field controls">
            {foreach from=$f.options key=rk item=button}<label for="{$f.field}_{$rk}" class="radio{if $f.class} {$f.class}{/if}"><input type="radio"{if $f.required} class="required"{/if} name="{$f.field}" id="{$f.field}_{$rk}" value="{$button}" {if $f.value == $button} checked="checked"{/if} />{$button}</label>
            {/foreach}
        </div>
    {elseif $f.type=='select'}<div class="form-field controls"><select name="{$f.field}" id="{$f.field}"{if $f.required} class="required"{/if}>
              <option value="">Select</option>
                {foreach from=$f.options item=so}<option value="{$so}"{if $f.value == $so} selected="selected"{/if}>{$so}</option>
                {/foreach}
        </select></div>
    {elseif $f.type=='list'}<select name="{$f.field}[]" id="{$f.field}" multiple="multiple" style="width:{$f.size}px"{if $f.required} class="required"{/if}>
            {foreach from=$f.options item=so}
            <option value="{$so|escape:'htmlall'}"{if $f.value == $so} selected="selected"{/if}>{$so}</option>
            {/foreach}
        </select>
    {elseif $f.type =='heading'}<div class="form-heading{if $f.class} {$f.class}{/if}">
        {if in_array($f.size, array(1,2,3,4,5,6))}<h{$f.size}>{if $f.value}{$f.value}{else}{$f.display}{/if}</h{$f.size}>
        {else}<h3>{if $f.value}{$f.value}{else}{$f.display}{/if}</h3>
        {/if}
    </div>
    {elseif $f.type=='note'}<div class="form-note{if $f.class} {$f.class}{/if}">
            <p>{if $f.value}{$f.value}{else}{$f.display}{/if}</p>
        </div>
    {elseif $f.type=='upload' || $f.type=='privateupload'}<div class="form-field controls"><input type="file" class="input fileupload {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" name="FILE_{$f.field}" id="FILE_{$f.field}"  size="{$f.size}" value="" /></div>
    {elseif $f.type=='emailwithconfirmation'}<div class="form-field controls"><input type="text" class="input text{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value=""{if $f.placeholder} placeholder="{$f.placeholder}"{/if} /></div>
    {elseif $f.type=='date'}<div class="form-field controls"><input type="{if $anytime}text{else}date{/if}" class="input {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}{if $f.validation && $f.validation!=$f.type} {$f.validation}{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} /><a class="cleardata" title="clear field" onclick="$('#{$f.field}').val('');return false;">x</a></div>
    {else}<div class="form-field controls">{if $f.prependvalue}<span class="add-on">{$f.prependvalue}</span>{/if}<input type="{$f.type}" class="input {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}{if $f.validation && $f.validation!=$f.type} {$f.validation}{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />{if $f.appendvalue}<span class="add-on">{$f.appendvalue}</span>{/if}</div>
    {/if}
    {if $f.description}<div class="form-field-description">{$f.description|nl2br}</div>
    {/if}{if !in_array($f.type,array('heading','note')) && $f.type!='hidden'}</div>
    {/if}{if $f.type=='emailwithconfirmation'}
    <div class="form-fieldset text">
        <label for="form_{$f.field}_confirmation">##Confirm Email:##</label>
        <input type="text" class="input text{if $f.class} {$f.class}{/if}" size="{$f.size}" name="{$f.field}_confirmation" id="{$f.field}_confirmation" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />
    </div>
    {/if}{assign var=x value=`$k+1`}{if !$fields[$x]}</fieldset>
    {/if}
{/foreach}
    <div class="form-submit{if $form.form_submit_end} endonly{/if}">
    {if $form.form_captcha}
        <div class="form-fieldset captcha">
            <p class="note">Please enter the {$OPTIONS.captcha_num_chars|default:3} letter code below. This helps us prevent spam. <em>Code is not case-sensitive</em></p>
            <p><img src="external/php-captcha/visual-captcha.php" width="200" height="60" alt="Visual CAPTCHA" /></p>
        </div>
        <div class="form-fieldset control-group">
            <label for="CAPTCHA" class="control-label">Spam prevention<span class="required">*</span></label>
            <input type="text" class="input text required" size="8" name="CAPTCHA" id="CAPTCHA" value="" autocomplete="off" />
        </div>
    {/if}
        <div class="form-fieldset submit">
            {if $form.form_submit_label}<label class="control-label">&nbsp;</label>{/if}<input type="submit" name="contactsubmit" id="contactsubmit" value="{$form.form_submit}" class="button btn" data-normalval="{$form.form_submit}" onmouseover="this.className='button btn buttonrollover';" onmouseout="this.className='button btn'" /><br />
       </div>
        <div class="progress" style="display: none;">
            <div class="bar"></div >
            <div class="percent">0%</div >
        </div>
    </div>
</div>
</form>
</div>
<div id="form{$form.form_id}message" class="message" {if !$message}style="display:none;"{/if}>{$message}</div>
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
