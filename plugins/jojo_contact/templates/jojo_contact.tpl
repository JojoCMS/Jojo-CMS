{if $content}{$content}
{/if}<div{if $form.form_hideonsuccess && $message && $sent} style="display:none;"{/if}>
<form name="{$form.form_name|escape:'htmlall'}" id="form{$form.form_id}" method="post" action="{$posturl}" enctype="multipart/form-data" class="contact-form{if $form.form_multipage} multipage{/if}{if $form.form_class} {$form.form_class}{/if}">
<input type="hidden" name="form_id" id="form_id" value="{$form.form_id}" />
<input type="hidden" name="form_redirect" id="form_redirect" value="{$form.form_redirect_url}" />
<input type="hidden" name="MAX_FILE_SIZE" value="{if $maxuploadvalue}{$maxuploadvalue}{else}5000000{/if}" />
<div>
{if $toaddresses}<div class="form-fieldset">
        <label for="form_sendto">Send Enquiry To:</label>
        <select name="form_sendto" id="form_sendto">
            {foreach item=to from=$toaddresses}<option value="{$to.email}">{$to.name}</option>
            {/foreach}
        </select>&nbsp;<span class="required">*</span>
    </div>
{/if}
{foreach from=$fields key=k item=f }{assign var=x value=`$k-1`}
    {if ($f.fieldset && $f.fieldset!=$fields[$x].fieldset) || $k==0}{if $k>0}</fieldset>
    {/if}<fieldset{if $f.fieldsetid} id="{$f.fieldsetid}"{/if}>{if $f.fieldset && $form.form_fieldsets}<legend>{$f.fieldset}</legend>{/if}
    {/if}{if !in_array($f.type,array('heading','note')) && $f.type!='hidden'}<div class="form-fieldset {if $f.type == 'emailwithconfirmation'}text{else}{$f.type}{/if}{if $f.prependvalue} input-prepend{/if}{if $f.appendvalue} input-append{/if}{if $f.class} {$f.class}{/if}">
        {if $f.showlabel || $f.padlabel}<label for="{$f.field}">{if $f.display && $f.showlabel}{$f.display}{/if}{if $f.required}<span class="required">*</span>{/if}</label>
        {/if}
    {/if}{if $f.prependvalue}<span class="add-on">{$f.prependvalue}</span>{/if}{if $f.type == 'hidden'}<input type="hidden" name="{$f.field}" id="{$f.field}" value="{$f.value}" />
    {elseif $f.type == 'textarea'}<textarea class="input textarea{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" rows="{$f.rows|default:'10'}" cols="{$f.cols|default:'29'}" name="{$f.field}" id="{$f.field}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if}>{$f.value}</textarea>
    {elseif $f.type == 'checkboxes'}<div class="form-field">
            {foreach from=$f.options key=ck item=fo}<div class="checkbox"><label for="{$f.field}_{$ck}" class="checkbox"><input type="checkbox" class="checkbox{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" name="{$f.field}[]" id="{$f.field}_{$ck}" value="{$fo}"{if $f.valuearr}{foreach from=$f.valuearr item=fa}{if $fa==$fo} checked="checked"{/if}{/foreach}{elseif $f.value == 'checked'} checked="checked"{/if} />{$fo}</label></div>
            {/foreach}
            </div>
    {elseif $f.type == 'radio'}<div class="form-field">
            {foreach from=$f.options key=rk item=button}<span class="radio"><label for="{$f.field}_{$rk}" class="radio"><input type="radio" class="radio{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" name="{$f.field}" id="{$f.field}_{$rk}" value="{$button}" {if $f.value == $button} checked="checked"{/if} />{$button}</label></span>
            {/foreach}
        </div>
    {elseif $f.type=='select'}<select name="{$f.field}" id="{$f.field}"{if $f.required} class="required"{/if}>
              <option value="">Select</option>
                {foreach from=$f.options item=so}<option value="{$so}"{if $f.value == $so} selected="selected"{/if}>{$so}</option>
                {/foreach}
        </select>
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
    {elseif $f.type=='upload' || $f.type=='privateupload'}<input type="file" class="input fileupload {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" name="FILE_{$f.field}" id="FILE_{$f.field}"  size="{$f.size}" value="" />
    {elseif $f.type=='emailwithconfirmation'}<input type="text" class="input text{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value=""{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />
    {elseif $f.type=='date'}<input type="{if $anytime}text{else}date{/if}" class="input {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}{if $f.validation && $f.validation!=$f.type} {$f.validation}{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} /><a class="cleardata" title="clear field" onclick="$('#{$f.field}').val('');return false;">x</a>
    {else}<input type="{$f.type}" class="input {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}{if $f.validation && $f.validation!=$f.type} {$f.validation}{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />
    {/if}{if $f.appendvalue}<span class="add-on">{$f.appendvalue}</span>{/if}
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
        <div class="form-fieldset">
            <label for="CAPTCHA">Spam prevention:</label>
            <input type="text" class="input text required" size="8" name="CAPTCHA" id="CAPTCHA" value="" autocomplete="off" /><span class="required">*</span>
        </div>
    {/if}
        <div  class="form-fieldset submit">
            {if $form.form_submit_label}<label>&nbsp;</label>{/if}<input type="submit" name="contactsubmit" id="contactsubmit" value="{$form.form_submit}" class="button btn" data-normalval="{$form.form_submit}" onmouseover="this.className='button btn buttonrollover';" onmouseout="this.className='button btn'" /><br />
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
