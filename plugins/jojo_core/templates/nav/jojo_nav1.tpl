<ul id="nav">
{foreach from=$mainnav item=n}
    <li {if in_array($n.pageid, $selectedpages)} class="selected"{/if}><a href="{$n.url}" title="{$n.title|escape:"html"}"{if $n.pg_followto=='no'} rel="nofollow"{/if}>{$n.label}</a></li>
{if $n.subnav && (in_array($n.pageid, $selectedpages))}
    <li class="subnav">
        <ul class="subnav">
{foreach from=$n.subnav item=s}
            <li{if in_array($s.pageid, $selectedpages)} class="selected"{/if}><a href="{$s.url}" title="{$s.title|escape:"html"}"{if $s.pg_followto=='no'} rel="nofollow"{/if}>{$s.label}</a></li>
{if $s.subnav && (in_array($s.pageid, $selectedpages))}
            <li class="subnav">
                <ul class="subnav">
{foreach from=$s.subnav item=t}
                    <li{if in_array($t.pageid, $selectedpages)} class="selected"{/if}><a href="{$t.url}" title="{$t.title|escape:"html"}"{if $t.pg_followto=='no'} rel="nofollow"{/if}>{if $t.label}</a></li>
{if $t.subnav && (in_array($t.pageid, $selectedpages))}
                    <li class="subnav">
                        <ul class="subnav">
{foreach from=$t.subnav item=u}
                            <li{if in_array($u.pageid, $selectedpages)} class="selected"{/if}><a href="{$u.url}" title="{$u.label|escape:"html"}"{if $u.pg_followto=='no'} rel="nofollow"{/if}>{if $u.label}</a></li>
{/foreach}
                        </ul>
                    </li>
{/if}
{/foreach}
                </ul>
            </li>
{/if}
{/foreach}
        </ul>
    </li>
{/if}
{/foreach}
</ul>