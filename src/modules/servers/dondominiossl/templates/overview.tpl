<h3>{$LANG.clientareaproductdetails}</h3>

<hr>

<div class="row">
    <div class="col-sm-4">
        {$LANG.clientareahostingregdate}
    </div>
    <div class="col-sm-8">
        {$regdate}
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        {$LANG.orderproduct}
    </div>
    <div class="col-sm-8">
        {$groupname} - {$product}
    </div>
</div>

{if $dd_product_name}
    <div class="row">
        <div class="col-sm-4">
            DonDominio Product
        </div>
        <div class="col-sm-8">
            {$dd_product_name}
        </div>
    </div>
{/if}

{if $type eq "server"}
    {if $domain}
        <div class="row">
            <div class="col-sm-4">
                {$LANG.serverhostname}
            </div>
            <div class="col-sm-8">
                {$domain}
            </div>
        </div>
    {/if}
    {if $dedicatedip}
        <div class="row">
            <div class="col-sm-4">
                {$LANG.primaryIP}
            </div>
            <div class="col-sm-8">
                {$dedicatedip}
            </div>
        </div>
    {/if}
    {if $assignedips}
        <div class="row">
            <div class="col-sm-4">
                {$LANG.assignedIPs}
            </div>
            <div class="col-sm-8">
                {$assignedips|nl2br}
            </div>
        </div>
    {/if}
    {if $ns1 || $ns2}
        <div class="row">
            <div class="col-sm-4">
                {$LANG.domainnameservers}
            </div>
            <div class="col-sm-8">
                {$ns1}<br />{$ns2}
            </div>
        </div>
    {/if}
{else}
    {if $domain}
        <div class="row">
            <div class="col-sm-4">
                {$LANG.orderdomain}
            </div>
            <div class="col-sm-8">
                {$domain}
                <a href="http://{$domain}" target="_blank" class="btn btn-default btn-xs">{$LANG.visitwebsite}</a>
            </div>
        </div>
    {/if}
    {if $username}
        <div class="row">
            <div class="col-sm-4">
                {$LANG.serverusername}
            </div>
            <div class="col-sm-8">
                {$username}
            </div>
        </div>
    {/if}
    {if $serverdata}
        <div class="row">
            <div class="col-sm-4">
                {$LANG.servername}
            </div>
            <div class="col-sm-8">
                {$serverdata.hostname}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                {$LANG.domainregisternsip}
            </div>
            <div class="col-sm-8">
                {$serverdata.ipaddress}
            </div>
        </div>
        {if $serverdata.nameserver1 || $serverdata.nameserver2 || $serverdata.nameserver3 || $serverdata.nameserver4 || $serverdata.nameserver5}
            <div class="row">
                <div class="col-sm-4">
                    {$LANG.domainnameservers}
                </div>
                <div class="col-sm-8">
                    {if $serverdata.nameserver1}{$serverdata.nameserver1} ({$serverdata.nameserver1ip})<br />{/if}
                    {if $serverdata.nameserver2}{$serverdata.nameserver2} ({$serverdata.nameserver2ip})<br />{/if}
                    {if $serverdata.nameserver3}{$serverdata.nameserver3} ({$serverdata.nameserver3ip})<br />{/if}
                    {if $serverdata.nameserver4}{$serverdata.nameserver4} ({$serverdata.nameserver4ip})<br />{/if}
                    {if $serverdata.nameserver5}{$serverdata.nameserver5} ({$serverdata.nameserver5ip})<br />{/if}
                </div>
            </div>
        {/if}
    {/if}
{/if}

{if $dedicatedip}
    <div class="row">
        <div class="col-sm-4">
            {$LANG.domainregisternsip}
        </div>
        <div class="col-sm-8">
            {$dedicatedip}
        </div>
    </div>
{/if}

{foreach from=$configurableoptions item=configoption}
    <div class="row">
        <div class="col-sm-4">
            {$configoption.optionname}
        </div>
        <div class="col-sm-8">
            {if $configoption.optiontype eq 3}
                {if $configoption.selectedqty}
                    {$LANG.yes}
                {else}
                    {$LANG.no}
                {/if}
            {elseif $configoption.optiontype eq 4}
                {$configoption.selectedqty} x {$configoption.selectedoption}
            {else}
                {$configoption.selectedoption}
            {/if}
        </div>
    </div>
{/foreach}

{foreach from=$productcustomfields item=customfield}
    <div class="row">
        <div class="col-sm-4">
            {$customfield.name}
        </div>
        <div class="col-sm-8">
            {$customfield.value}
        </div>
    </div>
{/foreach}

{if $lastupdate}
    <div class="row">
        <div class="col-sm-4">
            {$LANG.clientareadiskusage}
        </div>
        <div class="col-sm-8">
            {$diskusage}MB / {$disklimit}MB ({$diskpercent})
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            {$LANG.clientareabwusage}
        </div>
        <div class="col-sm-8">
            {$bwusage}MB / {$bwlimit}MB ({$bwpercent})
        </div>
    </div>
{/if}

<div class="row">
    <div class="col-sm-4">
        {$LANG.orderpaymentmethod}
    </div>
    <div class="col-sm-8">
        {$paymentmethod}
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        {$LANG.firstpaymentamount}
    </div>
    <div class="col-sm-8">
        {$firstpaymentamount}
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        {$LANG.recurringamount}
    </div>
    <div class="col-sm-8">
        {$recurringamount}
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        {$LANG.clientareahostingnextduedate}
    </div>
    <div class="col-sm-8">
        {$nextduedate}
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        {$LANG.orderbillingcycle}
    </div>
    <div class="col-sm-8">
        {$billingcycle}
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        {$LANG.clientareastatus}
    </div>
    <div class="col-sm-8">
        {$status}
    </div>
</div>

{if $suspendreason}
    <div class="row">
        <div class="col-sm-4">
            {$LANG.suspendreason}
        </div>
        <div class="col-sm-8">
            {$suspendreason}
        </div>
    </div>
{/if}

{if $api_response.sslCert}
    <div class="row">
        <div class="col-sm-4">
            CSR Data
        </div>
        <div class="col-sm-8">
            <pre>{$api_response.sslCert}</pre>
        </div>
    </div>
{/if}

{if $api_response.sslKey}
    <div class="row">
        <div class="col-sm-4">
            CSR Key
        </div>
        <div class="col-sm-8">
            <pre>{$api_response.sslKey}</pre>
        </div>
    </div>
{/if}


<hr>

<div class="row">

    <div class="col-sm-4 pull-right">
        <a href="clientarea.php?action=cancel&amp;id={$id}" class="btn btn-danger btn-block{if $pendingcancellation}disabled{/if}">
            {if $pendingcancellation}
                {$LANG.cancellationrequested}
            {else}
                {$LANG.cancel}
            {/if}
        </a>
    </div>
</div>
