<div class="panel panel-default">
    <form data-sync-form action='{$links.sync}' method='post'>
        <div class="panel-body">
            <p>{$LANG.ssl_sync_message}</p>
            
            <label><input name="update_prices" id="update_prices" type="checkbox" {$update_prices}> {$LANG.sync_alert}</label>
        </div>
        <div class="panel-footer">
            <input data-sync-submit class='btn btn-primary' type="submit" value="{$LANG.ssl_sync}" />
            <span data-sync-message style="display: none;">{$LANG.ssl_sync_wait}</span>
        </div>
    </form>
</div>

{literal}
<script>
    $(document).ready(function () {

        $('[data-sync-submit]').on('click', function (event) {
            event.preventDefault();
            $('[data-sync-message]').show();
            $('[data-sync-form]').find('input').attr('disabled', true);
            $('[data-sync-form]').submit();
        });
    });
</script>
{/literal}