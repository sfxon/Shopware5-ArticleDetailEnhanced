{* <!-- Detail Tab Content --> *}
{if $mvTab.fieldname == 'default_tab:description'}
    {if ($sArticle.description_long || $sArticle.sProperties || $sArticle.sDownloads || $sArticle.wbp_video_1 || $sArticle.wbp_video_2 || $sArticle.wbp_video_3 || $sArticle.wbp_video_4 || $sArticle.wbp_video_5)}
        <div class="custom-tabs--body" {$dataAttributeName}="{counter name="tabContent" print=TRUE}">
            {* <!-- Artikel-Video über Artikelbeschreibung --> *}
            {if $sArticle.wbp_video_1 || $sArticle.wbp_video_2 || $sArticle.wbp_video_3 || $sArticle.wbp_video_4 || $sArticle.wbp_video_5}
                <div class="content--description">
                    {if ($sArticle.wbp_video_1)}
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_1}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                    {/if}
                    {if ($sArticle.wbp_video_2)}
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_2}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                    {/if}
                    {if ($sArticle.wbp_video_3)}
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_3}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                    {/if}
                    {if ($sArticle.wbp_video_4)}
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_4}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                    {/if}
                    {if ($sArticle.wbp_video_5)}
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_5}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                    {/if}
                </div>
            {/if} 
            {* <!-- Ende: Artikelvideo über Artikelbeschreibung --> *}
        
            {$sArticle.description_long}
            {if $sArticle.sProperties}
                <div class="product--properties panel has--border">
                    <table class="product--properties-table">
                        {foreach $sArticle.sProperties as $sProperty}
                            <tr class="product--properties-row">
                                {* Property label *}
                                {block name='frontend_detail_description_properties_label'}
                                    <td class="product--properties-label is--bold">{$sProperty.name|escape}:</td>
                                {/block}

                                {* Property content *}
                                {block name='frontend_detail_description_properties_content'}
                                    <td class="product--properties-value">{$sProperty.value|escape}</td>
                                {/block}
                            </tr>
                        {/foreach}
                    </table>
                </div>
            {/if}

            {if $sArticle.sDownloads}
                {* Downloads title *}
                {block name='frontend_detail_description_downloads_title'}
                    <div class="content--title">
                        {s name="DetailDescriptionHeaderDownloads"}{/s}
                    </div>
                {/block}
                {* Downloads list *}
                {block name='frontend_detail_description_downloads_content'}
                    <ul class="content--list list--unstyled">
                        {foreach $sArticle.sDownloads as $download}
                            {block name='frontend_detail_description_downloads_content_link'}
                                <li class="list--entry">
                                    <a href="{$download.filename}" target="_blank" class="content--link link--download" title="{"{s name="DetailDescriptionLinkDownload"}{/s}"|escape} {$download.description|escape}">
                                        <i class="icon--arrow-right"></i> {s name="DetailDescriptionLinkDownload"}{/s} {$download.description}
                                    </a>
                                </li>
                            {/block}
                        {/foreach}
                    </ul>
                {/block}
            {/if}
        </div>
    {/if}
 
 
{* <!-- Rating Tab Content --> *}
{else if $mvTab.fieldname == 'default_tab:rating'}
    {block name="frontend_detail_tabs_content_rating"}
        {if !{config name=VoteDisable}}
            <div class="custom-tabs--body" {$dataAttributeName}="{counter name="tabContent" print=TRUE}">
                {block name="frontend_detail_tabs_content_rating_inner"}
                    {* Rating title *}
                    {block name="frontend_detail_tabs_rating_title"}
                        <div class="tab--header">
                            {block name="frontend_detail_tabs_rating_title_inner"}
                                <a href="#" class="tab--title" title="{s name='DetailTabsRating'}{/s}">{s name='DetailTabsRating'}{/s}</a>
                                {block name="frontend_detail_tabs_rating_title_count"}
                                    <span class="product--rating-count">{$sArticle.sVoteAverage.count}</span>
                                {/block}
                            {/block}
                        </div>
                    {/block}
                    {* Rating preview *}
                    {block name="frontend_detail_tabs_rating_preview"}
                        <div class="tab--preview">
                            {block name="frontend_detail_tabs_rating_preview_inner"}
                                {s name="RatingPreviewText"}{/s}<a href="#" class="tab--link" title="{s name="PreviewTextMore"}{/s}">{s name="PreviewTextMore"}{/s}</a>
                            {/block}
                        </div>
                    {/block}
                    {* Rating content *}
                    {block name="frontend_detail_tabs_rating_content"}
                        <div id="tab--product-comment" class="tab--content">
                            {block name="frontend_detail_tabs_rating_content_inner"}
                                {include file="frontend/detail/tabs/comment.tpl"}
                            {/block}
                        </div>
                    {/block}

                {/block}
            </div>
        {/if}
    {/block}
  
{* <!-- MvSpecialForYou Tab --> *}
{else if $mvTab.fieldname == 'extension_tab:mvspecialforyou'}
    <div class="custom-tabs--body" {$dataAttributeName}="{counter name="tabContent" print=TRUE}">
        {action module='widgets' controller='GeschenkeLoader' action='index'}
    </div>
    
{* <!-- CrossSelling Tab --> *}
{else if $mvTab.fieldname == 'extension_tab:mvcrossselling'}
    {if $mv_cross_selling_products|@count > 0}
        <div class="custom-tabs--body" {$dataAttributeName}="{counter name="tabContent" print=TRUE}">
            <h2>{s name="mv_tab_empfehlung_title"}Das könnte Ihnen auch gefallen:{/s}</h2>
            <div class="mv-product-detail-with-right-boxes-items-container">
                {foreach from=$mv_cross_selling_products item=mv_product}
                    <div class="mv-product-detail-with-right-boxes-item">
                        <a href="{$mv_product.link}">
                            <img src="{$mv_product.image_url}" class="img-responsive" />
                            <div class="mv-product-detail-with-right-boxes-item-title">{$mv_product.products_title}</div>
                            {if $mv_product.formated_price > 0}
                                <div class="mv-product-detail-with-right-boxes-item-price">{$mv_product.formated_price|currency}</div>
                            {/if}
                        </a>
                    </div>
                {/foreach}
            </div>
        </div>
    {/if}
{* <!-- UpSelling Tab --> *}
{else if $mvTab.fieldname == 'extension_tab:mvupselling'}
    {if $mv_up_selling_products|@count > 0}
        <div class="custom-tabs--body" {$dataAttributeName}="{counter name="tabContent" print=TRUE}">
            <h2>{s name="mv-tab_crosselling_title"}Zu diesem Artikel empfehlen wir:{/s}</h2>
            <div class="mv-product-detail-with-right-boxes-items-container">
                {foreach from=$mv_up_selling_products item=mv_product}
                    <div class="mv-product-detail-with-right-boxes-item">
                        <a href="{$mv_product.link}">
                            <img src="{$mv_product.image_url}" class="img-responsive" />
                            <div class="mv-product-detail-with-right-boxes-item-title">{$mv_product.products_title}</div>
                            {if $mv_product.formated_price > 0}
                                <div class="mv-product-detail-with-right-boxes-item-price">{$mv_product.formated_price|currency}</div>
                            {/if}
                        </a>
                    </div>
                {/foreach}
            </div>
        </div>
    {/if}
{else if $mvTab.fieldname|strpos: 'field:' === 0}
    {* <!-- Prüfen, ob Daten in dem Custom-Field stecken. Nur dann zeigen wir den Wert wirklich an! --> *}
    {if $sArticle.attributes.core->get( $mvTab.fieldname|substr:6 )|trim != ""}
        <div class="custom-tabs--body" {$dataAttributeName}="{counter name="tabContent" print=TRUE}">
            {$sArticle.attributes.core->get( $mvTab.fieldname|substr:6 )}
        </div>
    {/if}
{else if $mvTab.fieldname|strpos: 'extension_tab:youtube' === 0}
    {if $sArticle.wbp_video_1 || $sArticle.wbp_video_2 || $sArticle.wbp_video_3 || $sArticle.wbp_video_4 || $sArticle.wbp_video_5}
        <div class="custom-tabs--body" {$dataAttributeName}="{counter name="tabContent" print=TRUE}">
            <div class="content--description">
                {if ($sArticle.wbp_video_1)}
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_1}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                {/if}
                {if ($sArticle.wbp_video_2)}
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_2}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                {/if}
                {if ($sArticle.wbp_video_3)}
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_3}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                {/if}
                {if ($sArticle.wbp_video_4)}
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_4}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                {/if}
                {if ($sArticle.wbp_video_5)}
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/{$sArticle.wbp_video_5}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="padding-bottom: 10px"></iframe>
                {/if}
            </div>
        </div>
    {/if}
{/if}
    