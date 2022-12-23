{if $mvTab.fieldname == 'default_tab:description'}
    {if ($sArticle.description_long || $sArticle.sProperties || $sArticle.sDownloads)}
        <button class="custom-tabs--btn" {$dataAttributeName}="{counter name="tabButtons" print=TRUE}">
            {$mvTab.title}
        </button>
    {/if}
{else if $mvTab.fieldname == 'default_tab:rating'}
    {if !{config name=VoteDisable}}
        <button class="custom-tabs--btn" {$dataAttributeName}="{counter name="tabButtons" print=TRUE}">
            {$mvTab.title}
            {block name="frontend_detail_tabs_navigation_rating_count"}
                <span class="product--rating-count">{$sArticle.sVoteAverage.count}</span>
            {/block}
        </button>
    {/if}
{else if $mvTab.fieldname == 'extension_tab:mvspecialforyou'}
    <button class="custom-tabs--btn" {$dataAttributeName}="{counter name="tabButtons" print=TRUE}">
        {$mvTab.title}
    </button>
{else if $mvTab.fieldname == 'extension_tab:mvcrossselling'}
    {if $mv_cross_selling_products|@count > 0}
        <button class="custom-tabs--btn" {$dataAttributeName}="{counter name="tabButtons" print=TRUE}">
            {$mvTab.title}
        </button>
    {/if}
{else if $mvTab.fieldname == 'extension_tab:mvupselling'}
    {if $mv_up_selling_products|@count > 0}
        <button class="custom-tabs--btn" {$dataAttributeName}="{counter name="tabButtons" print=TRUE}">
            {$mvTab.title}
        </button>
    {/if}
{else if $mvTab.fieldname == 'extension_tab:youtube'}
    {if $sArticle.wbp_video_1 || $sArticle.wbp_video_2 || $sArticle.wbp_video_3 || $sArticle.wbp_video_4 || $sArticle.wbp_video_5}
        <button class="custom-tabs--btn youtube" {$dataAttributeName}="{counter name="tabButtons" print=TRUE}">
            {$mvTab.title}
        </button>
    {/if}
{else if $mvTab.fieldname|strpos: 'field:' === 0}
    {* <!-- Prüfen, ob Daten in dem Custom-Field stecken. Nur dann zeigen wir den Wert wirklich an! --> *}
    {if $sArticle.attributes.core->get( $mvTab.fieldname|substr:6 )|trim != ""}
        <button class="custom-tabs--btn" {$dataAttributeName}="{counter name="tabButtons" print=TRUE}">
            {$mvTab.title}
        </button>
    {/if}
{/if}
