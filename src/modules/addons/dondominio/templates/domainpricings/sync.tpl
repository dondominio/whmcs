<div class="panel panel-default">
    <form data-sync-form action='' method='post'>
        <input type="hidden" name="module" value="{$module_name}">
        <input type="hidden" name="__c__" value="{$__c__}">
        <input type="hidden" name="__a__" value="{$actions.sync}">
        <div class="panel-body">
            <p>{$LANG.sync_message}</p>

            <label><input name="update_prices" id="update_prices" type="checkbox" {$update_prices}> {$LANG.sync_alert}</label>
        </div>
        <div class="panel-footer">
            <input data-sync-submit class='btn btn-primary' type="submit" value="{$LANG.sync_tlds}" />
            <span data-sync-message style="display: none;">{$LANG.sync_wait}</span>
        </div>
    </form>
</div>

{literal}
<script>
    $(document).ready(function () {

        $('[data-sync-form]').submit(function (event) {
            $('[data-sync-message]').show();
            $('[data-sync-submit]').attr('disabled', true);
        });

    });
</script>
{/literal}