<div class="panel-heading">
    <h3 class="panel-title" style="touch-action: none;">{$LANG.balance_title}</h3>
</div>
<div class="panel-body">
    <div class="widget-content-padded">
        <table class="datatable" style="width: 100%;">
            <tbody>
                <tr>
                    <td style="width: 250px">{$LANG.balance_client_name}</td>
                    <td>{$info.clientName}</td>
                </tr>
                <tr>
                    <td style="width: 250px">{$LANG.balance_title}</td>
                    <td>{$info.balance}</td>
                </tr>
                <tr>
                    <td style="width: 250px">{$LANG.balance_threshold}</td>
                    <td>{$info.threshold}</td>
                </tr>
                <tr>
                    <td style="width: 250px">{$LANG.balance_currency}</td>
                    <td>{$info.currency}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>