{include file="admin/header.tpl"}

    <style type="text/css">
    {literal}

    ul.plugins li {
      border-top: 1px solid #ccc;

      list-style: none;
      margin: 0;
      padding: 5px;
      clear: both;
      line-height: 16px;
    }

    ul.plugins li img {
      float: right;
    }

    ul.plugins li span {
      /* float: left; */
    }

    ul.plugins {
      margin-right: 30px;
      padding: 0;
      border-bottom: 0px solid #ccc;
    }
    ul.plugins p {
        margin: 0;
    }

    div.options, div.readme {
      display: none;
    }

    li.core {
      color: #333;
    }

    {/literal}
    </style>
    <div style="float: left; width: 340px;">
    <div class="box-300">
    <h3>Available plugins</h3>
    <ul class="plugins">
{foreach from=$plugins item=p}{if $p.status!='active'}
        <li class="{$p.type}" title="{$p.description|escape:'htmlall':'utf-8'}">
            <span>{$p.name|replace:'_':' '|ucwords}</span>
            <img class="icon" src="images/cms/icons/brick_add.png" rel="{$p.name}" title="Install" alt="Install"/>
        </li>
{/if}{/foreach}
    </ul>
    </div>

    <div class="box-300">
    <h3>Plugin information</h3>
    <p class="plugin-info">Hover over an available plugin for a description, click the icon to install</p>
    </div>

    </div>



    <div style="float: left;">
    <h2>Installed plugins</h2>
    {section name=p loop=$plugins}
    {if $plugins[p].status=='active'}<div class="box-600">
      <h3>{$plugins[p].name|replace:'_':' '|ucwords}</h3>
      {if $plugins[p].description}<p>{$plugins[p].description}</p>{/if}

      <div class="options" id="options-{$plugins[p].name}">
      {assign var='hasoptions' value=false}
      {foreach from=$options item=opt}
        {if $opt.op_plugin == $plugins[p].name}
        {assign var='hasoptions' value=true}
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
            <option {if $option == $opt.op_value} selected="selected"{/if}>{$option}</option>
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

    {if $plugins[p].readme}
    <div class="readme" id="readme-{$plugins[p].name}">
    <h4>readme.txt</h4>
    {$plugins[p].readme|escape:'htmlall':'utf-8'|nl2br}
    </div>
    {/if}

      <button class='reinstall' rel='{$plugins[p].name}'><img class="icon" src="images/cms/icons/brick_add.png" id="plugin-{$plugins[p].name}-install" title="Reinstall" alt="Reinstall" /> Reinstall</button>
    {if $plugins[p].status != 'not installed' && $plugins[p].name!='jojo_core'}<button class="uninstall" rel="{$plugins[p].name}"><img class="icon" src="images/cms/icons/brick_delete.png" id="plugin-{$plugins[p].name}-uninstall" alt="Uninstall" title="Uninstall" /> Uninstall</button>{/if}
    {if $hasoptions}<button id="show-options-{$plugins[p].name}"><img class="icon" src="images/cms/icons/brick.png" alt="Options" title="Display Options" /> Options</button>{/if}
    {if $plugins[p].readme}<button id="show-readme-{$plugins[p].name}"><img class="icon" src="images/cms/icons/page.png" alt="Readme" title="Display Readme" /> Readme</button>{/if}
    <script type="text/javascript">
    $('#show-options-{$plugins[p].name}').toggle(function(){ldelim}$('#options-{$plugins[p].name}').show('fast');{rdelim},function(){ldelim}$('#options-{$plugins[p].name}').hide('fast');{rdelim});
    $('#show-readme-{$plugins[p].name}').toggle(function(){ldelim}$('#readme-{$plugins[p].name}').show('fast');{rdelim},function(){ldelim}$('#readme-{$plugins[p].name}').hide('fast');{rdelim});
    </script>

    </div>{/if}
    {/section}
    </div>

    <script type="text/javascript">
    {literal}
    $('ul.plugins li').hover(
    function(){$('p.plugin-info').html($(this).attr('title'))}, function(){});

    $('ul.plugins li img').click(function() {
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
