{block name="got_my_own_block_now"}
    <h2>{s name="geschenke_loader_title"}Gratis zu Ihrer Bestellung{/s}</h2>
    <div class="mv-product-detail-with-right-boxes-items-container">
        <div class="mv-product-detail-with-right-boxes-item-full">
        		{* <!--
            		<a href="{$mv_special_for_you_cheapest_premium.sArticle.linkDetails}" title="{$mv_special_for_you_cheapest_premium.sArticle.articleName|escape}" class="product--image">
            --> *}
            <a href="{url controller="checkout"}" title="{$mv_special_for_you_cheapest_premium.sArticle.articleName|escape}" class="product--image">
                {if $mv_special_for_you_cheapest_premium.sArticle.image.thumbnails}
                    <img srcset="{$mv_special_for_you_cheapest_premium.sArticle.image.thumbnails[0].sourceSet}"
                         alt="{$mv_special_for_you_cheapest_premium.sArticle.articleName|escape}" class="img-responsive" style="margin: 0 auto;" />
                {else}
                    <img src="{link file='frontend/_public/src/img/no-picture.jpg'}"
                         alt="{"{s name="PremiumInfoNoPicture"}{/s}"|escape}" class="img-responsive" style="margin: 0 auto;">
                {/if}
                
                <div class="mv-product-detail-with-right-boxes-item-title">{$mv_special_for_you_cheapest_premium.sArticle.articleName|escape}</div>
                
                
                {if $mv_special_for_you_cheapest_premium.available == 1}
                    <div class="mv-product-detail-with-right-boxes-item-price">{s name="geschenke_loader_info_text"}Jetzt als kostenlose Prämie mitnehmen!**{/s}</div>
                {else}		
                    <div class="mv-product-detail-with-right-boxes-item-price">{s name="geschenke_loader_for_free"}kostenlos{/s}<br />{s name="PremiumsInfoAtAmount" namespace="frontend/checkout/premiums"}{/s} {$mv_special_for_you_cheapest_premium.startprice|currency} {s name="PremiumInfoBasketValue" namespace="frontend/checkout/premiums"}{/s}</div>
                    <div class="mv-product-detail-with-right-boxes-item-price">{s name="geschenke_loader_not_free_till_before"}Noch {/s}{$mv_special_for_you_cheapest_premium.sDifference|currency}{s name="geschenke_loader_not_free_till_after"} benötigt{/s}</div>
                {/if}
            </a>
            
            {if $mv_special_for_you_cheapest_premium.available == 1}
                <form action="{url controller='checkout' action='addPremium' sTargetAction='cart'}" method="POST" class="is--align-center mv-add-premium-detail-form">
                    <input type="hidden" name="sAddPremium" value="{$mv_special_for_you_cheapest_premium.premium_ordernumber}" />
                    <button type="submit" class="btn is--primary is--align-center">{s name="geschenke_load_add_to_cart"}In den Warenkorb{/s}</button>
                </form>
            {/if}
        </div>
    </div>
    {if $mv_special_for_you_second_cheapest_premium != false}
        <div class="mv-product-detail-with-right-boxes-items-container">
            <div class="mv-product-detail-with-right-boxes-item-full">
                {* <!--
                <a href="{$mv_special_for_you_second_cheapest_premium.sArticle.linkDetails}" title="{$mv_special_for_you_second_cheapest_premium.sArticle.articleName|escape}" class="product--image">
                --> *}
                <a href="{url controller="checkout"}" title="{$mv_special_for_you_second_cheapest_premium.sArticle.articleName|escape}" class="product--image">
                    {if $mv_special_for_you_second_cheapest_premium.sArticle.image.thumbnails}
                        <img srcset="{$mv_special_for_you_second_cheapest_premium.sArticle.image.thumbnails[0].sourceSet}"
                             alt="{$mv_special_for_you_second_cheapest_premium.sArticle.articleName|escape}" class="img-responsive" style="margin: 0 auto;" />
                    {else}
                        <img src="{link file='frontend/_public/src/img/no-picture.jpg'}"
                             alt="{"{s name="PremiumInfoNoPicture"}{/s}"|escape}" class="img-responsive" style="margin: 0 auto;">
                    {/if}
                    
                    <div class="mv-product-detail-with-right-boxes-item-title">{$mv_special_for_you_second_cheapest_premium.sArticle.articleName|escape}</div>
                    
                    {if $mv_special_for_you_second_cheapest_premium.available == 1}
                        <div class="mv-product-detail-with-right-boxes-item-price">{s name="geschenke_loader_info_text"}Jetzt als kostenlose Prämie mitnehmen!**{/s}</div>
                    {else}		
                        <div class="mv-product-detail-with-right-boxes-item-price">{s name="geschenke_loader_for_free"}kostenlos{/s}<br />{s name="PremiumsInfoAtAmount" namespace="frontend/checkout/premiums"}{/s} {$mv_special_for_you_second_cheapest_premium.startprice|currency} {s name="PremiumInfoBasketValue" namespace="frontend/checkout/premiums"}{/s}</div>
                        <div class="mv-product-detail-with-right-boxes-item-price">{s name="geschenke_loader_not_free_till_before"}Noch {/s}{$mv_special_for_you_second_cheapest_premium.sDifference|currency}{s name="geschenke_loader_not_free_till_after"} benötigt{/s}</div>
                    {/if}
                </a>
                
                
                {if $mv_special_for_you_second_cheapest_premium.available == 1}
                    <form action="{url controller='checkout' action='addPremium' sTargetAction='cart'}" method="POST" class="is--align-center mv-add-premium-detail-form">
                        <input type="hidden" name="sAddPremium" value="{$mv_special_for_you_second_cheapest_premium.premium_ordernumber}" />
                        <button type="submit" class="btn is--primary is--align-center">{s name="geschenke_load_add_to_cart"}In den Warenkorb{/s}</button>
                    </form>
                {/if}
                
            </div>
        </div>
        
        {if $mv_special_for_you_premiums_count > 2}
            <div class="mv-products-special-for-you-show-more-container">
                <button type="button" id="mv-products-special-for-you-show-more-container" data-attr-id1="{$mv_special_for_you_cheapest_premium.sArticle.articleID}" data-attr-id2="{$mv_special_for_you_second_cheapest_premium.sArticle.articleID}" data-attr-url="{url controller="GeschenkeLoader" action="index"}">Weitere anzeigen</button>
            </div>
        {/if}
        
        <div class="mv-premium-product-info is--align-center">{s name="geschenke_load_disclaimer_only_one"}** Nur ein Prämien-Artikel je Bestellung{/s}</div>
    {/if}
{/block}