{foreach from=$MV_MORE_PREMIUMS item=premium}
		<div class="mv-product-detail-with-right-boxes-items-container">
        <div class="mv-product-detail-with-right-boxes-item-full">
        		{* <!--
            <a href="{$premium.sArticle.linkDetails}" title="{$premium.sArticle.articleName|escape}" class="product--image">
            --> *}
            <a href="{url controller="checkout"}" title="{$premium.sArticle.articleName|escape}" class="product--image">
                {if $premium.sArticle.image.thumbnails}
                    <img srcset="{$premium.sArticle.image.thumbnails[0].sourceSet}"
                         alt="{$premium.sArticle.articleName|escape}" class="img-responsive" style="margin: 0 auto;" />
                {else}
                    <img src="{link file='frontend/_public/src/img/no-picture.jpg'}"
                         alt="{"{s name="PremiumInfoNoPicture"}{/s}"|escape}" class="img-responsive" style="margin: 0 auto;">
                {/if}
                
                <div class="mv-product-detail-with-right-boxes-item-title">{$premium.sArticle.articleName|escape}</div>
                
                {if $premium.available == 1}
                		<div class="mv-product-detail-with-right-boxes-item-price">{s name="geschenke_loader_info_text"}Jetzt als kostenlose Prämie mitnehmen!**{/s}</div>
                {else}		
                		<div class="mv-product-detail-with-right-boxes-item-price">{s name="geschenke_loader_for_free"}kostenlos{/s}<br />{s name="PremiumsInfoAtAmount" namespace="frontend/checkout/premiums"}{/s} {$premium.startprice|currency} {s name="PremiumInfoBasketValue" namespace="frontend/checkout/premiums"}{/s}</div>
            				<div class="mv-product-detail-with-right-boxes-item-price">{s name="geschenke_loader_not_free_till_before"}Noch {/s}{$premium.sDifference|currency}{s name="geschenke_loader_not_free_till_after"} benötigt{/s}</div>
                {/if}
            </a>
            
						{if $premium.available == 1}
                <form action="{url controller='checkout' action='addPremium' sTargetAction='cart'}" method="POST" class="is--align-center mv-add-premium-detail-form">
                    <input type="hidden" name="sAddPremium" value="{$premium.premium_ordernumber}" />
                    <button type="submit" class="btn is--primary is--align-center">{s name="geschenke_load_add_to_cart"}In den Warenkorb{/s}</button>
                </form>
            {/if}
        </div>
    </div>
{/foreach}