{include file="admin/header.tpl"}
<div id="option-categories">
  <h3>Categories</h3>
    <ul>
    {section name=c loop=$categories}
      <li><a href="">{$categories[c].op_category}</a></li>
    {/section}
    </ul>
</div>

<div id="option-items">
  <div class="category">
    <h3>Options</h3>
    <p>Config options change the behaviour of the site, and any plugins that are installed. To edit options, please select a category from the list.<br />
    All options are saved automatically after they are changed.</p>
  </div>

{foreach from=$categories item=cat}
    <div id="category-{$cat.op_category|replace:' ':'-'}" class="category hidden">
        <h3>{$cat.op_category} Options</h3>
        <p>All options are saved automatically</p>

    {foreach from=$options[$cat.op_category] item=opt}
        {if $opt.op_type != 'hidden'}
        <div>
            <div class="options-title">
              <div id="savemsg_{$opt.op_name|replace:".":"_"}"></div>
              <h4>{$opt.op_displayname}</h4>
            </div>
        {if $opt.op_type == 'radio'}
          {foreach from=$opt.options item=radioOption}
            <input type="radio" name="option-{$opt.op_name}" onclick="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}','{$radioOption}');"{if $radioOption == $opt.op_value} checked="checked"{/if}/> {$radioOption}
          {/foreach}
        {elseif $opt.op_type == 'select'}
          <select id="option-{$opt.op_name}" name="option-{$opt.op_name}" onchange="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}', $('#option-{$opt.op_name} :selected').val());">
          {foreach from=$opt.options item=option}
            <option{if $option == $opt.op_value} selected="selected"{/if}>{$option}</option>
          {/foreach}
          </select>
        {elseif $opt.op_type == 'checkbox'}
            <span id="{$opt.op_name}">
          {foreach from=$opt.options item=option}
            <input type="checkbox" name="temp" value="{$option}" {if in_array($option, $opt.values)} checked="checked"{/if} onclick="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}', $('#{$opt.op_name} input:checked').serialize().replace(/temp=/g, '').replace(/&/g, ',') );" /> {$option}
          {/foreach}
          </span>
        {elseif $opt.op_type == 'text' || $opt.op_type == 'integer'}
            <input type="text" size="60" name="option-{$opt.op_name}" value="{$opt.op_value|escape:'html'}" onchange="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}', this.value);" />
        {else}
            <textarea rows="8" cols="50" name="option-{$opt.op_name}" onchange="$('#savemsg_{$opt.op_name}').hide().html('Saving...').show();  frajax('admin-set-options','{$opt.op_name}', this.value)">{$opt.op_value|escape:'html'}</textarea>
        {/if}

            <p>{$opt.op_description}</p>
        {if $opt.op_default}
            <p>Default Value: {$opt.op_default}</p>
        {/if}
        </div>
        {/if}
    {/foreach}
    </div>
{/foreach}
</div>

{include file="admin/footer.tpl"}