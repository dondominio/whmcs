<div class="panel panel-default">

    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8">
                <h3 class="panel-title domain-title">{$LANG.domains_domain}: {$domain.domain}</h3>
            </div>
            <div class="col-xs-4">
                <div class="dropdown pull-right">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        {$LANG.domain_actions_view}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="{$links.sync}">{$LANG.domain_sync_view}</a></li>
                        <li><a href="#" data-get-info="action"
                                data-link="{$links.get_info}">{$LANG.domain_check_view}</a></li>
                        <li><a href="{$links.history}">{$LANG.domain_history_view}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="widget-content-padded">
            <table class="datatable domain-table" style="width: 100%;">
                <tbody>
                    <tr>
                        <td>{$LANG.domain_name_view}</td>
                        <td>{$domain.domain}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.domain_register_view}</td>
                        <td>{$domain.registrar}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.domain_status_view}</td>
                        <td>
                            {assign var='status_class' value=''}

                            {if $domain.status eq 'Active'}
                            {assign var='status_class' value="active"}
                            {/if}
                            {if $domain.status eq 'Pending'}
                            {assign var='status_class' value="pending"}
                            {/if}
                            {if $domain.status eq 'Pending Transfer'}
                            {assign var='status_class' value="pending"}
                            {/if}
                            {if $domain.status eq 'Expired'}
                            {assign var='status_class' value="expired"}
                            {/if}
                            {if $domain.status eq 'Cancelled'}
                            {assign var='status_class' value="cancelled"}
                            {/if}
                            {if $domain.status eq 'Fraud'}
                            {assign var='status_class' value="fraud"}
                            {/if}
                            {if $domain.status eq 'Transferred Away'}
                            {assign var='status_class' value="transferred-away"}
                            {/if}
                            {if $domain.status eq 'Grace'}
                            {assign var='status_class' value="grace"}
                            {/if}
                            {if $domain.status eq 'Redemption'}
                            {assign var='status_class' value="redemption"}
                            {/if}

                            {if strlen($status_class) gt 0}
                            <div style='text-align: center;' class='label {$status_class}'>{$domain.status}
                            </div>
                            {else}
                            <div style='text-align: center;'>{$domain.status}</div>
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td>{$LANG.domain_expire_view}</td>
                        <td>{$expire_date}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

{if $domain.registrar eq $module_name}
<div class="panel panel-default" data-get-info="table" style="display: none;">

    <div class="panel-heading">
        <h3 class="panel-title">{$LANG.domain_api_check_view}</h3>
    </div>

    <div class="panel-body">
        <div class="widget-content-padded">
            <table class="datatable domain-table" style="width: 100%;">
                <tbody data-get-info="success" style="display: none;">
                    <tr>
                        <td>{$LANG.domain_name_view}</td>
                        <td data-get-info="name"></td>
                    </tr>
                    <tr>
                        <td>{$LANG.domain_tld_view}</td>
                        <td data-get-info="tld"></td>
                    </tr>
                    <tr>
                        <td>{$LANG.domain_status_view}</td>
                        <td data-get-info="status"></td>
                    </tr>
                    <tr>
                        <td>{$LANG.domain_expire_view}</td>
                        <td data-get-info="ts-expire"></td>
                    </tr>
                    <tr>
                        <td>{$LANG.domain_create_view}</td>
                        <td data-get-info="ts-create"></td>
                    </tr>
                </tbody>
                <tbody data-get-info="error" style="display: none;">
                    <tr class="text-danger">
                        <td>{$LANG.error}</td>
                        <td data-get-info="error-message"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
{/if}


{literal}
<script type="text/javascript">
    $(document).ready(function () {

        $('[data-get-info="action"]').on('click', function (event) {
            event.preventDefault();
            let link = $(this).data('link');

            $('[data-get-info="table"]').show()
            $('[data-get-info="error"]').hide()
            $('[data-get-info="success"]').hide()

            $.get(link, function (response) {
                if (typeof response.error !== 'undefined') {
                    $('[data-get-info="error"]').show()
                    $('[data-get-info="error-message"]').text(response.error)
                    return;
                }

                $('[data-get-info="success"]').show()

                $('[data-get-info="name"]').text(response.name)
                $('[data-get-info="tld"]').text(response.tld)
                $('[data-get-info="status"]').text(response.status)
                $('[data-get-info="ts-expire"]').text(response.tsExpire)
                $('[data-get-info="ts-create"]').text(response.tsCreate)
            })

        });

    });

</script>
{/literal}