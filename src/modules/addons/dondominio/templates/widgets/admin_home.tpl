<div class="widget-content-padded widget-billing">
    <div class="item text-center">
        {if $last_version eq true}
        <div class="data color-green mb-2" style="padding-top: 0;">{$LANG.actual_version}</div>
        <a class="btn btn-success btn-block" href="{$links.admin}">{$LANG.admin}</a>
        {else}
        <div class="data color-green mb-2" style="padding-top: 0;">{$LANG.new_version}</div>
        <a class="btn btn-success btn-block" href="{$links.update_modules}">{$LANG.update}</a>
        <div class="note"><a target="_blank"
                href="https://github.com/dondominio/whmcs/blob/main/CHANGELOG-es.md">{$LANG.check_changelog}</a></div>
        {/if}
    </div>
</div>