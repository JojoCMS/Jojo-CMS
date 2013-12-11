{include file="admin/header.tpl"}

<div id="message"></div>
<div class="themes row">
{foreach from=$themes key=k item=t}{if $k>0 && $k%4==0}</div>
<div class="themes row">
{/if}
    <div class="col-md-3">
        <div class="thumbnail{if $t.status=='active'} installed{/if}" title="{$t.description|escape:'htmlall':$charset}">
            <img src="images/160x100/themes/{$t.name}.jpg" alt="{$t.name}" /><br />
            <div class="caption">
                <h3>{$t.name}</h3>
                <button class="btn {if $t.status=='active'}btn-primary{else}btn-default{/if}" rel="{$t.name}">{if $t.status== 'active'}<i class="glyphicon glyphicon-refresh"></i> re-install{else}<i class="glyphicon glyphicon-upload"></i> install{/if}</button>
            </div>
        </div>
    </div>
    {/foreach}
</div>
<script type="text/javascript">
{literal}
$('.themes button').click(
    function(){
        $(this).parent().addClass('installing');
        $(this).html('installing...');
        $('.themes button').fadeOut('slow');
        $('.themes').removeClass('installed');
        $.get(siteurl + '/json/admin-install-theme.php', {theme: $(this).attr('rel')}, function(data) {
            if (data.result) {
                $('div.installing').parent().addClass('installed');
                $('div.installing').html(data.message);
            } else {
                alert('Error Installing theme: ' + data.message);
            }
        }, 'json');
        return false;
    }
);
{/literal}
</script>

{include file="admin/footer.tpl"}