{include file="admin/header.tpl"}
<div class="row">
    <div class="span4">
        <div>
            <h3>Available plugins</h3>
            <ul class="plugins">
        {foreach from=$plugins item=p}{if $p.status!='active'}
                <li class="{$p.type}" title="{$p.description|escape:'htmlall':'utf-8'}">
                    <span>{$p.name|replace:'_':' '|ucwords}</span>
                    <button class="btn" rel="{$p.name}"><i class="icon-upload"></i></button>
                </li>
        {/if}{/foreach}
            </ul>
        </div>
        <div class="box-300">
            <h3>Plugin information</h3>
            <p class="plugin-info">Hover over an available plugin for a description, click the icon to install</p>
        </div>

    </div>

    <div class="span8">
        <h2>Installed plugins</h2>
        {foreach from=$plugins item=p}
        {if $p.status=='active'}<div>
          <h3>{$p.name|replace:'_':' '|ucwords}</h3>
          {if $p.description}<p>{$p.description}</p>{/if}

          <div class="options well" id="options-{$p.name}">
          {assign var='hasoptions' value=false}
          {foreach from=$options key=k item=opt}
            {if $opt.op_plugin == $p.name}
            {assign var='hasoptions' value=true}
            <div class="control-group">
                <div class="options-title">
                    <div id="savemsg_{$opt.op_name|replace:".":"_"}"></div>
                    <h4{if $k==0} style="margin-top:0;"{/if}>{$opt.op_displayname}</h4>
                </div>
                <div class="controls">
    {if $opt.op_type == 'radio'}
                {foreach from=$opt.options item=radioOption}<label class="radio inline"><input type="radio" name="option-{$opt.op_name}" onclick="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}','{$radioOption}');"{if $radioOption == $opt.op_value} checked="checked"{/if}/>{$radioOption}</label>
                {/foreach}
    {elseif $opt.op_type == 'select'}
                  <select id="option-{$opt.op_name}" name="option-{$opt.op_name}" onchange="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}', $('#option-{$opt.op_name} :selected').val());">
                  {foreach from=$opt.options item=option}<option {if $option == $opt.op_value} selected="selected"{/if}>{$option}</option>
                  {/foreach}
                  </select>
    {elseif $opt.op_type == 'checkbox'}
                <span id="{$opt.op_name}">
                    {foreach from=$opt.options item=option}<input type="checkbox" name="temp" value="{$option}" {if in_array($option, $opt.values)} checked="checked"{/if} onclick="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}', $('#{$opt.op_name} input:checked').serialize().replace(/temp=/g, '').replace(/&/g, ',') );" /> {$option}
                    {/foreach}
                </span>
    {elseif $opt.op_type == 'text' || $opt.op_type == 'integer'}
                <input type="text" size="60" name="option-{$opt.op_name}" value="{$opt.op_value|escape:'html'}" onchange="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}', this.value);" />
    {else}
                <textarea rows="8" cols="50" name="option-{$opt.op_name}" onchange="$('#savemsg_{$opt.op_name}').hide().html('Saving...').show();  frajax('admin-set-options','{$opt.op_name}', this.value)">{$opt.op_value|escape:'html'}</textarea>
    {/if}
                 </div>
               {if $opt.op_description}<p>{$opt.op_description}</p>{/if}
                {if $opt.op_default}<p>Default Value: {$opt.op_default}</p>{/if}
            </div>
            {/if}
        {/foreach}
        </div>

        {if $p.readme}
        <div class="well" id="readme-{$p.name}">
            <h4 style="margin-top:0;">readme.txt</h4>
            {$p.readme}
        </div>
        {/if}

        <div id="buttons" class="btn-group">
            <button class='btn reinstall' rel='{$p.name}'><i class="icon-refresh"></i> Reinstall</button>
            {if $p.status != 'not installed' && $p.name!='jojo_core'}<button class="btn uninstall" rel="{$p.name}"><i class="icon-remove"></i> Uninstall</button>{/if}
            {if $hasoptions}<button class="btn" id="show-options-{$p.name}"><i class="icon-list"></i> Options</button>{/if}
            {if $p.readme}<button class="btn" id="show-readme-{$p.name}"><i class="icon-book"></i> Readme</button>{/if}
        </div>
        <script type="text/javascript">
            $('#show-options-{$p.name}').click(function(){ldelim}$('#options-{$p.name}').toggle();{rdelim});
            $('#show-readme-{$p.name}').click(function(){ldelim}$('#readme-{$p.name}').toggle();{rdelim});
        </script>
        
        </div>
        {/if}
        {/foreach}
    </div>
</div>
    <script type="text/javascript">
    {literal}
    $('ul.plugins li').hover(
    function(){$('p.plugin-info').html($(this).attr('title'))}, function(){});

    $('ul.plugins li button').click(function() {
        $(this).parent().addClass('installing');
        $(this).replaceWith('<p style="float:right">installing...</p>');
        $('button.reinstall').attr('disabled', 'disabled');
        $('button.uninstall').attr('disabled', 'disabled');
        $('ul.plugins li img').fadeOut();
        $.get(siteurl + '/json/admin-install-plugin.php', {plugin: $(this).attr('rel')}, function(data) {
                if (data.result) {
                    $('.installing p').html('installed');
                    location.reload();
                } else {
                    alert('Error Installing plguin: ' + data.message);
                }
        }, 'json');
        return false;
    });

    $('button.reinstall').click(function() {
        $(this).html('Re-installing...').addClass('installing');
        $('button.reinstall').attr('disabled', 'disabled');
        $('button.uninstall').attr('disabled', 'disabled');
        $('ul.plugins li img').fadeOut();
        $.get(siteurl + '/json/admin-install-plugin.php', {plugin: $(this).attr('rel')}, function(data) {
                if (data.result) {
                    $('button.installing').html('Re-installed');
                    location.reload();
                } else {
                    alert('Error Installing plguin: ' + data.message);
                }
        }, 'json');
        return false;
    });

    $('button.uninstall').click(function() {
        $(this).html('uninstalling...').addClass('unstalling');
        $('button.reinstall').attr('disabled', 'disabled');
        $('button.uninstall').attr('disabled', 'disabled');
        $('ul.plugins li img').fadeOut();
        $.get(siteurl + '/json/admin-uninstall-plugin.php', {plugin: $(this).attr('rel')}, function(data) {
                if (data.result) {
                    $('button.installing').html('Uninstalled');
                    location.reload();
                } else {
                    alert('Error Unstalling plguin: ' + data.message);
                }
        }, 'json');
        return false;
    });

    {/literal}
    </script>

{include file="admin/footer.tpl"}
