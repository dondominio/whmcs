<div class="domain-step-options" id="stepResults">
    <div class="domain-checker-result-headline">
        <p class="domain-checker-available">
            {$LANG.tpl_you_may_also_like}
        </p>
    </div>

    <div class="domainresults" id="primarySearchResults">
        <div id="btnCheckout" class="domain-checkout-area">
            <a href="cart.php?a=view" class="btn btn-default">{$LANG.tpl_go_to_checkout}</a>
        </div>

        <div>
            {$LANG.tpl_search_results}
        </div>

        <table class="table table-curved table-hover" id="searchResults">
        <tbody>
            {literal}{{#suggestions}}{/literal}
            <tr>
                <td><strong>{literal}{{domain}}{/literal}</strong></td>
                
                <td class="text-center">
                    <span class="label label-success">{$LANG.tpl_available}</span>
                </td>

                <td class="text-center">{literal}{{price.currency_prefix}}{{price.1Y}} {{price.currency_suffix}}{/literal}</td>

                <td class="text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-sm" onclick="addToCart(this, false, 'register', 1)">
                            <b class="glyphicon glyphicon-shopping-cart"></b>
                            
                            {$LANG.tpl_add_to_cart}
                        </button>
                    </div>
                </td>
            </tr>
            {literal}{{/suggestions}}{/literal}
        </tbody>
        </table>
    </div>
</div>