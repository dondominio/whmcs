<div class="panel panel-default">

    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-8">
                <h3 class="panel-title domain-title">{$contact.contactID}</h3>
            </div>
            <div class="col-xs-4">
                {if $contact.verificationstatus eq 'inprocess'}
                <div class="dropdown pull-right">
                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        {$LANG.domain_actions_view}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="{$links.contact_resend}">{$LANG.contact_resend}</a></li>
                    </ul>
                </div>
                {/if}
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="widget-content-padded">
            <table class="datatable domain-table" style="width: 100%;">
                <tbody>
                    <tr>
                        <td>{$LANG.contact_name}</td>
                        <td>{$contact.firstName} {$contact.lastName}</td>
                    </tr>
                    {if $contact.contactType eq 'organization'}
                    <tr>
                        <td>Tipo de organizacion</td>
                        <td>{$contact.orgType}</td>
                    </tr>
                    {/if}
                    <tr>
                        <td>{$LANG.contact_type}</td>
                        <td>
                            {if $contact.contactType eq 'organization'}
                            {$LANG.contact_type_organization}
                            {/if}
                            {if $contact.contactType eq 'individual'}
                            {$LANG.contact_type_individual}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_email}</td>
                        <td>{$contact.email}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_type_phone}</td>
                        <td>{$contact.phone}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_type_fax}</td>
                        <td>{$contact.fax}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_type_address}</td>
                        <td>{$contact.address}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_type_postal_code}</td>
                        <td>{$contact.postalCode}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_type_city}</td>
                        <td>{$contact.city}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_type_state}</td>
                        <td>{$contact.state}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_type_country}</td>
                        <td>{$contact.country}</td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_daaccepted}</td>
                        <td>
                            {if $contact.daaccepted eq true}
                            {$LANG.contact_da_accepted}
                            {else}
                            {$LANG.contact_da_no_accepted}
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td>{$LANG.contact_verification}</td>
                        <td>
                            {if $contact.verificationstatus eq 'verified'}
                            {$LANG.contact_ver_verified}
                            {/if}
                            {if $contact.verificationstatus eq 'inprocess'}
                            {$LANG.contact_ver_inprocess}
                            {/if}
                            {if $contact.verificationstatus eq 'failed'}
                            {$LANG.contact_ver_failed}
                            {/if}
                            {if $contact.verificationstatus eq 'notapplicable'}
                            {$LANG.contact_ver_notapplicable}
                            {/if}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>