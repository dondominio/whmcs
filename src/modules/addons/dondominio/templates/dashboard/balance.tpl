<div class="panel panel-default">

    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8">
                <h3 class="panel-title domain-title">{$LANG.balance_title}</h3>
            </div>
            <div class="col-xs-4">
                <a data-update href="{$links.update}" class="btn btn-default pull-right">Actualizar</a>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="widget-content-padded">
            <table class="datatable" style="width: 100%;">
                <tbody data-info>
                    <tr>
                        <td style="width: 250px">{$LANG.balance_client_name}</td>
                        <td data-client>{$info.clientName}</td>
                    </tr>
                    <tr>
                        <td style="width: 250px">{$LANG.balance_title}</td>
                        <td data-balane>{$info.balance}</td>
                    </tr>
                    <tr>
                        <td style="width: 250px">{$LANG.balance_threshold}</td>
                        <td data-threshold>{$info.threshold}</td>
                    </tr>
                    <tr>
                        <td style="width: 250px">{$LANG.balance_currency}</td>
                        <td data-currency>{$info.currency}</td>
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

            $.get(link, function (response) {
                $('[data-client]').text(response.clientName)
                $('[data-balane]').text(response.balance_title)
                $('[data-threshold]').text(response.balance_threshold)
                $('[data-currency]').text(response.balance_currency)

                info.show();
            });
        });

    });
</script>
{/literal}