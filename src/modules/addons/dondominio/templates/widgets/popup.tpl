<link rel="stylesheet" type="text/css" href="{$css_path}style.css?v={$version}" />

<div data-dondominio="modal" class="modal" tabindex="-1" role="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header dd-popup-header">
                <h5 class="modal-title">
                    {if not $is_last_version}
                        {$LANG.new_version}
                    {elseif not $ssl_provisioning_module}
                        {$LANG.incomplet_install}
                    {/if}
                </h5>
            </div>
            <div class="modal-body  dd-popup-body">
                <h1>
                    {if not $is_last_version}
                        {$LANG.new_version_body}
                    {elseif not $ssl_provisioning_module}
                        La instalación de los módulos de DonDominio está incompleta, ahora puedes instalar el módulo de
                        Certificados SSL.
                    {/if}
                </h1>
                <a href="{$links.admin}" class="btn btn-lg dd-update-btn">
                    {if not $is_last_version}
                        {$LANG.update}
                    {elseif not $ssl_provisioning_module}
                        {$LANG.end_install}
                    {/if}
                </a>
            </div>
            <div class="modal-footer">
                <span class="pull-left"><input type="checkbox" data-dondominio="no-show"
                        data-dondominio-version="{$new_version}" class="dd-no-show-checkbox"> {$LANG.no_show} </span>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.close}</button>
            </div>
        </div>
    </div>
</div>

{literal}
    <script>
        const cookieName = 'dondominio_update_popup';

        function createNoShowCookie(version) {
            let date = new Date();
            date.setTime(date.getTime() + (1 * 24 * 60 * 60 * 30));

            document.cookie = cookieName + version + '=1; Expires=' + date.toGMTString() + '; Path=/';
        }

        function deleteNoShowCookie(version) {
            document.cookie = cookieName + version + '=; Expires=Thu, 01 Jan 1970 00:00:01 GMT; Path=/';
        }

        $(document).ready(function() {
            const version = $('[data-dondominio-version]').data('dondominio-version');

            if (parseInt(document.cookie.indexOf(cookieName + version)) === -1) {
                $('[data-dondominio="modal"]').modal("show");
            }

            $('[data-dondominio="no-show"]').on('change', function() {
                const checked = $(this).is(':checked');

                if (checked) {
                    createNoShowCookie(version);
                    return;
                }

                deleteNoShowCookie(version);
            });
        });
    </script>
{/literal}