<link rel="stylesheet" type="text/css" href="{$css_path}style.css?v={$version}" />
<div class="dd">
    
    {if $print_title eq true}
    <h1>{$title}</h1>
    {/if}

    {if $print_nav eq true}
    {include file='nav.tpl'}
    {/if}

    <!-- {include file='breadcrumb.tpl'} -->
    

    {if count($RESPONSE->errors) gt 0 or $RESPONSE->force_errors}
    <div class='errorbox'>
        <span class='title'>
            {if strlen($errors_title) gt 0}
            {$errors_title}
            {else}
            {$LANG.errors_title}
            {/if}
        </span>
        <br />
        {if count($RESPONSE->errors) gt 0}
        {foreach from=$RESPONSE->errors item=error}
        {$error}
        <br>
        {/foreach}
        {else}
        {$LANG.unknown_error}
        {/if}
    </div>
    {/if}

    {if count($RESPONSE->success) gt 0 or $RESPONSE->force_success}
    <div class='successbox'>
        <span class='title'>
            {if strlen($success_title) gt 0}
            {$success_title}
            {else}
            {$LANG.succcess_title}
            {/if}
        </span>
        <br>
        {foreach from=$RESPONSE->success item=msg}
        {$msg}
        <br>
        {/foreach}
    </div>
    {/if}

    {if count($RESPONSE->info) gt 0 or $RESPONSE->force_info}
    <div class='infobox'>
        <span class='title'>
            {if strlen($info_title) gt 0}
            {$info_title}
            {else}
            {$LANG.info_title}
            {/if}
        </span>
        <br>
        {foreach from=$RESPONSE->info item=msg}
        {$msg}
        <br>
        {/foreach}
    </div>
    {/if}

    {include file=$CONTENT_FILE}

</div>