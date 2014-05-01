{include file="admin/header.tpl"}

<div class="admin-home row">

<div class="col-md-6">

    {if !($browser == 'mozilla' || $browser == 'webkit' ) }
    <div class="admin-home-item-left">

        <h3>Browser Warning!</h3>
        <p>Jojo CMS Admin has only been tested in the <a href="http://www.getfirefox.com" target="_BLANK">Firefox</a>, <a href="http://www.google.com/chrome" target="_BLANK">Chrome</a> and <a href="www.apple.com/safari/" target="_BLANK">Safari</a> browsers. There are several known issues with Internet Explorer in particular which will prevent some features of the admin area from working.</p>

        <p>You are currently using the <strong>{$browser}</strong> browser</p>

        <p>We recommend you download the latest version of <a href="http://www.getfirefox.com">Firefox</a>, <a href="http://www.google.com/chrome" target="_BLANK">Chrome</a> or <a href="www.apple.com/safari/" target="_BLANK">Safari</a>, all free, all good.</p>

    </div>
    {/if}

    <div class="admin-home-item-left">

        <h2>Welcome</h2>
        Welcome to the admin section of your website.{if $jojoversion} Currently running Jojo CMS {$jojoversion}{/if}.

    </div>

    <div class="admin-home-item-left">

        <h3>Help!</h3>
        <p>Documentation for Jojo CMS is available <a href="http://www.jojocms.org/docs/" target="_BLANK">online</a>.
        Support is also available for Jojo CMS in several forms...</p>
        <ul>
        <li>Contact your {if $OPTIONS.webmasteraddress}<a href="mailto:{$OPTIONS.webmasteraddress}">web developer</a>{else}web developer{/if} for direct support</li>
        <li>Read the <a href="http://www.jojocms.org/docs/" target="_BLANK">Jojo documentation</a></li>
        <li>Browse or search the <a href="http://www.jojocms.org/forums/" target="_BLANK">Forums</a></li>
        <li>Submit a <a href="https://github.com/JojoCMS/Jojo-CMS/issues?state=open" target="_BLANK">bug report</a></li>
        <li>Contact the Jojo team directly - <a href="http://www.jojocms.org/contact/">www.jojocms.org/contact/</a> (if posting in the <a href="http://www.jojocms.org/forums/" target="_BLANK">forums</a> is not appropriate)</li>
        </ul>

    </div>

</div>

<div class="col-md-5 offset1">

    <div class="admin-home-item-right">
        <h3>Edit Site Options</h3>
        <p>These options are for managing your website. Unless you know what these do, they are best left unchanged</p>

        <form action="" method="post" class="form-horizontal">

            <!-- [Content Cache] -->
            <label class="control-label">Content Cache: </label>
            <div class="controls">
                <label for="content-cache-on" class="radio inline"><input type="radio" name="content-cache" id="content-cache-on" onclick="$('#savemsg_contentcache').hide().html('Saving...').show(); frajax('admin-set-options','contentcache','yes');"{if $OPTIONS.contentcache == 'yes'} checked="checked"{/if} />On</label>
                <label for="content-cache-off" class="radio inline"><input type="radio" name="content-cache" id="content-cache-off" onclick="$('#savemsg_contentcache').hide().html('Saving...').show(); frajax('admin-set-options','contentcache','no');"{if $OPTIONS.contentcache == 'no'} checked="checked"{/if} />Off</label>
                <span id="savemsg_contentcache" style="color: red;" class="inline-help"></span>
            </div>
            <br />
            Caching your website's content makes it run a lot faster, and reduces load on the server. It should usually be left on, unless new features are being tested
            <br /><br />

            <!-- [Empty Content Cache] -->
            <label class="control-label">Empty Cache: </label>
            <div class="controls">
            <input type="submit" name="empty-cache" id="empty-cache" value="Empty" class="btn"
                   onclick="{literal}$(this).attr('value', 'Emptying cache'); $.get(siteurl + '/json/admin-empty-cache.php', null, function(data) {$('#empty-cache').attr('value', 'Cache Emptied');}); return false;{/literal}"
                   />
                </div>
            <br />
            Every page has a maximum cache time of 8 hours, however emptying the cache will ensure everyone will see fresh content immediately.
            <br /><br />

            <!-- [Enable GZip] -->
            <label class="control-label">GZip: </label>
            <div class="controls">
                <label for="enable-gzip-on" class="radio inline"><input type="radio" name="enable-gzip" id="enable-gzip-on" onclick="$('#savemsg_enablegzip').hide().html('Saving...').show(); frajax('admin-set-options','enablegzip','1');"{if $OPTIONS.enablegzip == '1'} checked="checked"{/if} />Enabled</label>
                <label for="enable-gzip-off" class="radio inline"><input type="radio" name="enable-gzip" id="enable-gzip-off" onclick="$('#savemsg_enablegzip').hide().html('Saving...').show(); frajax('admin-set-options','enablegzip','0');"{if $OPTIONS.enablegzip == '0'} checked="checked"{/if} />Disabled</label>
                <span id="savemsg_enablegzip" style="color: red;" class="inline-help"></span>
            </div>
            <br />
            GZipping your content reduces the amount of data that needs to be downloaded, and this can make a big difference to speed. Does not work correctly on some webhosts.
            <br /><br />
            All options are available from the <a href="{$ADMIN}/options/">options page</a>.
        </form>
    </div>
</div>

</div>

{include file="admin/footer.tpl"}