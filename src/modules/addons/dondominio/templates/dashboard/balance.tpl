<div class="panel panel-default">

    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8">
                <h3 class="panel-title domain-title">{$LANG.balance_title}</h3>
            </div>
            <div class="col-xs-4">
                <a data-update href="{$links.update}" class="btn btn-default pull-right">{$LANG.update}</a>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="widget-content-padded">
            <table class="datatable" style="width: 100%;">
                <tbody data-error="error" style="display: none;">
                    <tr class="text-danger">
                        <td style="width: 250px">{$LANG.error}</td>
                        <td data-error="message"></td>
                    </tr>
                </tbody>
                <tbody data-info>
                    <tr>
                        <td style="width: 250px">{$LANG.balance_client_name}</td>
                        <td data-client>{$info.clientName}</td>
                    </tr>
                    <tr>
                        <td style="width: 250px">{$LANG.balance_title}</td>
                        <td><span data-balane>{$info.balance}</span> <span data-currency>{$info.currency}</span></td>
                    </tr>
                    <tr >
                        <td style="width: 250px">{$LANG.balance_threshold}</td>
                        <td ><span data-threshold>{$info.threshold}</span> <span data-currency>{$info.currency}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{literal}
<script>
    $(document).ready(function() {
        
        $('[data-update]').on('click', function(event) {
            event.preventDefault();
            let link = $(this).attr('href');
            let info = $('[data-info]')

            info.hide();
            $('[data-error="error"]').hide()

            $.get(link, function (response) {

                if (typeof response.error !== 'undefined') {
                    $('[data-error="error"]').show()
                    $('[data-error="message"').text(response.error)
                    return;
                }

                $('[data-client]').text(response.clientName)
                $('[data-balane]').text(response.balance)
                $('[data-threshold]').text(response.threshold)
                $('[data-currency]').text(response.currency)

                info.show();
            });
        });

    });
</script>
{/literal}