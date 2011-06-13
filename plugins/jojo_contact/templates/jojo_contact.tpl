{if $content}{$content} 
{/if}{if $message}<div class="message">{$message}</div> 
{/if}<div{if $hideonsuccess && $message && $sent} style="display:none;"{/if}>
<form name="contactform" method="post" action="{$posturl}" onsubmit="return checkme();" class="contact-form">
<div>
{if $toaddresses}<div class="form-fieldset">
        <label for="form_sendto">Send Enquiry To:</label>
        <select name="form_sendto" id="form_sendto">
            <option value="">Select</option>
            {foreach item=to from=$toaddresses}<option value="{$to.email}">{$to.name}</option>
            {/foreach}
        </select>&nbsp;<span class="required">*</span>
    </div>
{/if}{if !$option_new_database_method}<p class="note">Required fields are marked <span class="required">*</span></p>
{/if}   
{foreach from=$fields key=k item=f }{assign var=x value=`$k-1`}{if $f.fieldset!='' && $f.fieldset!=$fields[$x].fieldset}<fieldset><legend>{$f.fieldset}</legend>
    {/if}{if !in_array($f.type,array('heading','note'))}<div class="form-fieldset {$f.type}">
        {if $f.showlabel}<label for="form_{$f.field}">{if $f.display}{$f.display}:{/if}</label>{/if}
    {/if}{if $f.type == 'textarea'}<textarea class="input textarea" rows="{$f.rows|default:'10'}" cols="{$f.cols|default:'29'}" name="form_{$f.field}" id="form_{$f.field}">{$f.value}</textarea>{if $f.required}<span class="required">*</span>{/if}
    </div>
    {elseif $f.type == 'checkboxes'}<div class="form-field">
            {foreach from=$f.options item=fo }<div class="checkbox"><input type="checkbox" class="checkbox" name="form_{$f.field}[{$fo}]" id="form_{$f.field}_{$fo|replace:' ':'_'|replace:'$':''}" value="{$fo}"{foreach from=$f.valuearr item=fa}{if $fa==$fo} checked="checked"{/if}{/foreach} /><label for="form_{$f.field}_{$fo}"> {$fo}</label></div>
            {/foreach}{if $f.required}<span class="required">*</span>{/if}
        </div>
    </div>
    {elseif $f.type == 'radio'}<div class="form-field">
            {foreach from=$f.options item=button }<input type="radio" class="radio" name="form_{$f.field}" id="form_{$f.field}_{$button|replace:' ':'_'|replace:'$':''}" value="{$button}" {if $f.value == $button} checked="checked"{/if} /><label for="form_{$f.field}_{$fo}"> {$button}</label>
            {/foreach}{if $f.required}<span class="required">*</span>{/if}
        </div>
    </div>
    {elseif $f.type=='select'}<select name="form_{$f.field}" id="form_{$f.field}">
              <option value="">Select</option>
                {foreach from=$f.options item=so}<option value="{$so}"{if $f.value == $so} selected="selected"{/if}>{$so}</option>
                {/foreach}
        </select>{if $f.required}<span class="required">*</span>{/if}
    </div>
    {elseif $f.type=='list'}<select name="form_{$f.field}[]" id="form_{$f.field}" multiple="multiple" style="width:{$f.size}px">
            {foreach from=$f.options item=so}
            <option value="{$so|escape:'htmlall'}"{if $f.value == $so} selected="selected"{/if}>{$so}</option>
            {/foreach}
        </select>{if $f.required}<span class="required">*</span>{/if}
    </div>    
    {elseif $f.type =='heading'}{if in_array($f.size, array(1,2,3,4,5,6))}<div class="form-heading">
            <h{$f.size}>{$f.value}</h{$f.size}>
        </div>
      {else}<div class="form-heading">
        <h1>{$f.value}</h1>
      </div>
      {/if}
    {elseif $f.type=='note'}<div class="form-note">
            {if $f.value}<p>{$f.value}</p>{/if}
        </div>
    {else}<input type="{$f.type}" class="input {$f.type}" size="{$f.size}" name="form_{$f.field}" id="form_{$f.field}" value="{$f.value}" />{if $f.required}<span class="required">*</span>{/if}
    </div>
    {/if}{if $f.description}<div class="form-field-description">{$f.description}</div>
    {/if}{assign var=x value=`$k+1`}{if $f.fieldset!='' && $f.fieldset!=$fields[$x].fieldset}</fieldset>{/if}
{/foreach}
{if $option_new_database_method}{if $option_form_captcha}<div class="form-fieldset">
        <label for="CAPTCHA">Spam prevention:</label>
        <input type="text" class="input text" size="8" name="CAPTCHA" id="CAPTCHA" value="" autocomplete="off" /> <span class="required">*</span>
    </div>
    <div class="form-fieldset captcha">
        <p class="note">Please enter the {$OPTIONS.captcha_num_chars|default:3} letter code below. This helps us prevent spam. <em>Code is not case-sensitive</em></p>
        <p><img src="external/php-captcha/visual-captcha.php" width="200" height="60" alt="Visual CAPTCHA" /></p>
    </div>
    {/if}{else}{if $OPTIONS.contactcaptcha == 'yes'}<div class="form-fieldset">
        <label for="CAPTCHA">Spam prevention:</label>
        <input type="text" class="input text" size="8" name="CAPTCHA" id="CAPTCHA" value="" autocomplete="off" /> <span class="required">*</span>
    </div>
    <div class="form-fieldset captcha">
        <p class="note">Please enter the {$OPTIONS.captcha_num_chars|default:3} letter code below. This helps us prevent spam. <em>Code is not case-sensitive</em></p>
        <p><img src="external/php-captcha/visual-captcha.php" width="200" height="60" alt="Visual CAPTCHA" /></p>
    </div>
    {/if}
{/if}<div  class="form-fieldset submit">
        <label>&nbsp;</label><input type="submit" name="submit" value="Submit" class="button" onmouseover="this.className='button buttonrollover';" onmouseout="this.className='button'" /><br />
   </div>
</div>
</form>
</div>