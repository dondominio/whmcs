<div class="modal" id="message" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5  class="modal-title" >{$LANG.log_message}</h5>
            </div>
            <div class="modal-body">
                <p data-log-message></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.close}</button>
            </div>
        </div>
    </div>
</div>

<form action='' method='get'>
    <input type="hidden" name="module" value="{$module_name}">
    <input type="hidden" name="__c__" value="{$__c__}">
    <input type="hidden" name="__a__" value="{$actions.view_history}">
    <table width='100%' border='0' cellpadding='3' cellspacing='0'>
        <tbody>
            <tr>
                <td width='50%' align='left'>
                    {$pagination.total} {$LANG.pagination_results_found}, {$LANG.pagination_page} {$pagination.page} {$LANG.pagination_of} {$pagination.total_pages}
                </td>

                <td width='50%' align='right'>
                    {$LANG.pagination_go_to}
                    <select name='page' onchange='submit()'>
                        {html_options options=$pagination_select selected=$pagination.page}
                    </select>

                    <input type='submit' value='{$LANG.pagination_go}' class='btn-small'>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<table style="table-layout: fixed;" class='datatable' width='100%' border='0' cellspacing='1' cellpadding='3'>
    <thead>
        <tr>
            <th>
                {$LANG.log_date}
            </th>

            <th>
                {$LANG.log_ip}
            </th>

            <th>
                {$LANG.log_user}
            </th>

            <th>
                {$LANG.log_title}
            </th>

            <th>
                {$LANG.log_message}
            </th>
        </tr>
    </thead>
    <tbody>

        {if count($history) gt 0}
        {foreach $history item=log}
        <tr>
            <td>
                {$log.ts}
            </td>

            <td>
                {$log.ip}
            </td>

            <td>
                {$log.user}
            </td>

            <td>
                {$log.title}
            </td>

            <td style="width: 100%;"> 
                <span data-message style="width: 90%;" class="text-ellipsis">{$log.message}</span>
                <a class="log-message-icon" href="#" data-more-info data-toggle="modal" data-target="#message" style="display: none;"><i class="fa fa-plus-circle"></i></a>
            </td>
        </tr>
        {/foreach}
        {else}
        <tr>
            <td colspan='5'>
                {$LANG.info_no_results}
            </td>
        </tr>
        {/if}

    </tbody>
    <tfoot>
        <tr>
            <th>
                {$LANG.log_date}
            </th>

            <th>
                {$LANG.log_ip}
            </th>

            <th>
                {$LANG.log_user}
            </th>

            <th>
                {$LANG.log_title}
            </th>

            <th>
                {$LANG.log_message}
            </th>
        </tr>
    </tfoot>
</table>

<p align='center'>
    {if $pagination.page gt 1}
        <a href='{$links.prev_page}'>« {$LANG.pagination_previous}</a>
    {else}
        « {$LANG.pagination_previous}
    {/if}

    &nbsp;
    {if $pagination.page lt $pagination.total_pages}
        <a href='{$links.next_page}'>{$LANG.pagination_next} »</a>
    {else}
        {$LANG.pagination_next} »
    {/if}
</p>

{literal}
<script type='text/javascript'>

function hideMessageMoreInfo(){
    $('[data-message]').each(function (key, val) {

        if ($(val)[0].scrollWidth >  $(val).innerWidth()) {
            $(val).siblings('[data-more-info]').show();
            return;
        }

        $(val).siblings('[data-more-info]').hide();
    })
}

$(window).resize(hideMessageMoreInfo);

$(document).ready(function() {
    hideMessageMoreInfo();

    $('[data-more-info]').on('click', function(event) {
        event.preventDefault();

        let message = $(this).siblings('[data-message]').text();
        $('[data-log-message]').text(message);
    });
});

</script>
{/literal}