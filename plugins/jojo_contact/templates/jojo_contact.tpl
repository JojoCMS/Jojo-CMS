{if $content}{$content}
{/if}<div{if $form.form_hideonsuccess && $message && $sent} style="display:none;"{/if}>
<form name="{$form.form_name|escape:'htmlall'}" id="form{$form.form_id}" method="post" action="{$posturl}" enctype="multipart/form-data" class="contact-form{if $form.form_multipage} multipage{/if}{if $form.form_horizontal} form-horizontal{/if}{if $form.form_class} {$form.form_class}{/if}" role="form">
<input type="hidden" name="form_id" id="form_id" value="{$form.form_id}" />
<input type="hidden" name="form_redirect" id="form_redirect" value="{$form.form_redirect_url}" />
<input type="hidden" name="MAX_FILE_SIZE" value="{if $maxuploadvalue}{$maxuploadvalue}{else}5000000{/if}" />

<div>
{if $toaddresses}<div class="form-fieldset form-group">
        <label for="form_sendto" class="control-label">##Send Enquiry To##<span class="required">*</span></label>
        <select name="form_sendto" id="form_sendto" class="form-control required"{if $form_choice_multiple} multiple="multiple"{/if}>
            {foreach item=to from=$toaddresses key=k}<option value="{$to.email}"{if $k==0} selected="selected"{/if}>{$to.name}</option>
            {/foreach}
        </select>
    </div>
{/if}{assign var=tabnum value=1}
{foreach from=$fields key=k item=f }{assign var=x value=`$k-1`}
    {if ($f.fieldset && $f.fieldset!=$fields[$x].fieldset) || $k==0}{if $k>0}</fieldset>
    {/if}<fieldset{if $f.fieldsetid} id="{$f.fieldsetid}"{/if} title="{$tabnum}. {$f.fieldset}"{$tabnum = $tabnum+1}>{if $f.fieldset && $form.form_fieldsets}<legend>{$f.fieldset}</legend>{/if}
    {/if}{if $f.type!='hidden'}<div class="form-group {if $f.type == 'emailwithconfirmation'}text{elseif !in_array($f.type,array('radio','checkbox'))}{$f.type}{/if}{if $f.class} {$f.class}{/if}">
    {if !($f.type=='heading' || $f.type=='note')}<label for="{$f.field}" class="control-label{if !$f.showlabel} sr-only{/if}">{$f.display}{if $f.required}<span class="required text-danger" title="##required field##">*</span>{/if}</label>{/if}
    {/if}{if $f.type == 'hidden'}<input type="hidden" name="{$f.field}" id="{$f.field}" value="{$f.value}" />
    {elseif $f.type == 'textarea'}<textarea class="form-control input textarea{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" rows="{$f.rows|default:'10'}" cols="{$f.cols|default:'29'}" {if $f.maxlength}maxlength="{$f.maxlength}" {/if}name="{$f.field}" id="{$f.field}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if}>{$f.value}</textarea>
    {elseif $f.type == 'checkboxes'}{foreach from=$f.options key=ck item=fo}<div class="checkbox{if $f.class} {$f.class}{/if}"><label for="{$f.field}_{$ck}" title="{$f.display}"><input type="checkbox"{if $f.required} class="required"{/if} name="{$f.field}[]" id="{$f.field}_{$ck}" value="{$fo}"{if $f.valuearr}{foreach from=$f.valuearr item=fa}{if $fa==$fo} checked="checked"{/if}{/foreach}{elseif $f.value == 'checked'} checked="checked"{/if} />{$fo}</label></div>
            {/foreach}
    {elseif $f.type == 'radio'}{foreach from=$f.options key=rk item=button}<div class="radio{if $f.class} {$f.class}{/if}"><label for="{$f.field}_{$rk}" title="{$f.display}"><input type="radio"{if $f.required} class="required"{/if} name="{$f.field}" id="{$f.field}_{$rk}" value="{$button}" {if $f.value == $button} checked="checked"{/if} />{$button}</label></div>
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
    {elseif $f.type=='upload' || $f.type=='privateupload' || $f.type=='attachment'}
    <div class="input-group">
                <span class="input-group-btn">
                    <span class="btn btn-primary btn-file">
                        Browse&hellip; <input type="file" class="fileupload {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" name="FILE_{$f.field}[]" id="FILE_{$f.field}""{if $f.size} size="{$f.size}"{/if} multiple>
                    </span>
                </span>
                <input type="text" class="form-control" title="Files" readonly>
    </div>
    {elseif $f.type=='emailwithconfirmation'}<input type="email" class="form-control input text{if $f.class} {$f.class}{/if}{if $f.required} required{/if}" size="{$f.size}" name="{$f.field}" id="{$f.field}" value=""{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />
    {elseif $f.type=='date'}{if $anytime}<div class="input-group">{/if}
        <input type="{if $anytime}text{else}date{/if}" class="form-control input {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}{if $anytime} anytime{/if}{if $f.validation && $f.validation!=$f.type} {$f.validation}{/if}" {if $f.size}size="{$f.size}" {/if}name="{$f.field}" id="{$f.field}" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if}{if $anytime && $f.options} data-format="{$f.options[0]}"{/if} />
        {if $anytime}<span class="input-group-btn"><a class="btn btn-default" title="clear field" onclick="$('#{$f.field}').val('');return false;">x</a></span>
    </div>{/if}
    {else}{if $f.prependvalue || $f.appendvalue}
    <div class="input-group">
        {/if}{if $f.prependvalue}<span class="input-group-addon">{$f.prependvalue}</span>{/if}<input type="{if $f.validation=='email'}email{elseif $f.validation=='url'}url{elseif $f.validation=='integer'}number{else}text{/if}" class="form-control input {$f.type}{if $f.class} {$f.class}{/if}{if $f.required} required{/if}{if $f.validation && $f.validation!=$f.type} {$f.validation}{/if}" {if $f.size}size="{$f.size}" {/if}{if $f.maxlength}maxlength="{$f.maxlength}" {/if}name="{$f.field}" id="{$f.field}" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />{if $f.appendvalue}<span class="input-group-addon">{$f.appendvalue}</span>{/if}{if $f.prependvalue || $f.appendvalue}
    </div>{/if}
    {/if}
    {if $f.description}<span class="help-block">{$f.description|nl2br}</span>
    {/if}{if $f.type!='hidden'}</div>
    {/if}{if $f.type=='emailwithconfirmation'}
    <div class="form-fieldset text form-group">
        <label for="form_{$f.field}_confirmation" class="control-label">##Confirm Email:##</label>
        <input type="email" class="form-control input text{if $f.class} {$f.class}{/if}" {if $f.size}size="{$f.size}" {/if}name="{$f.field}_confirmation" id="{$f.field}_confirmation" value="{$f.value}"{if $f.placeholder} placeholder="{$f.placeholder}"{/if} />
    </div>
    {/if}{assign var=x value=`$k+1`}{if !$fields[$x]}</fieldset>
    {/if}
{/foreach}
    <div class="form-submit{if $form.form_submit_end} endonly{/if}">
    {if $form.form_captcha}{if $OPTIONS.captcha_recaptcha=="yes" && $OPTIONS.captcha_sitekey}<div class="form-group captcha">
        <div class="g-recaptcha" data-sitekey="{$OPTIONS.captcha_sitekey}"></div>
    </div>
    {else}
        <div class="form-group captcha">
            <p class="note">Please enter the {$OPTIONS.captcha_num_chars|default:3} letter code below. This helps us prevent spam. <em>Code is not case-sensitive</em></p>
            <p><img src="external/php-captcha/visual-captcha.php" width="200" height="60" alt="Visual CAPTCHA" /></p>
        </div>
        <div class="form-group">
            <label for="CAPTCHA" class="control-label">Spam prevention<span class="required">*</span></label>
            <input type="text" class="form-control input text required" size="8" name="CAPTCHA" id="CAPTCHA" value="" autocomplete="off" />
        </div>
    {/if}{/if}
        <div class="form-group submit">
            {if $form.form_submit_label}<label class="control-label"></label>{/if}<button type="submit" name="contactsubmit" id="contactsubmit" value="{$form.form_submit}" class="btn btn-primary" data-normalval="{$form.form_submit}" >{$form.form_submit}</button>
       </div>
        <div class="progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="display: none;">
            <div class="progress-bar">0%</div >
        </div>
    </div>
</div>

</form>
</div>
<div id="form{$form.form_id}message" class="message alert alert-info" {if !$message}style="display:none;"{/if}>{$message}</div>
{if $sent && $form.form_tracking_code}{$form.form_tracking_code}
{/if}