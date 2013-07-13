{include file="admin/header.tpl"}

<div id="message"></div>

{foreach from=$themes item=t}
    <div class="theme-box{if $t.status=='active'} well{/if}" title="{$t.description|escape:'htmlall':$charset}">
        <h3>{$t.name}</h3>
        <img src="images/160x100/themes/{$t.name}.jpg" alt="{$t.name}" /><br />
        <button class="btn" rel="{$t.name}">{if $t.status== 'active'}<i class="icon-refresh"></i> reinstall{else}<i class="icon-upload"></i> install{/if}</button>
    </div>
{/foreach}

<script type="text/javascript">
{literal}
//Add a mouseover effect to themes
$('.theme-box').hover(
    function(){
        $(this).addClass('over')
    },
    function(){
        $(this).removeClass('over')
    }
);

$('.theme-box button').click(
    function(){
        $(this).parent().addClass('installing');
        $(this).replaceWith('<p>installing...</p>');
        $('div.theme-box button').fadeOut();
        $('div.theme-box').removeClass('installed');
        $.get(siteurl + '/json/admin-install-theme.php', {theme: $(this).attr('rel')}, function(data) {

                if (data.result) {
                    $('div.installing').addClass('installed');
                    $('div.installing img').hide();
                    $('div.installing p').html(data.message);
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