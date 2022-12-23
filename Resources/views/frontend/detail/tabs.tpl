{extends file="parent:frontend/detail/tabs.tpl"}
 
{block name="frontend_detail_tabs"}
    <div class="content--description custom-tabs-outer-container">
    	{if $mvTabsRightSide > 0}
        		<div class="custom-tabs-left">
        {else}
        		<div class="custom-tabs-left-only">
        {/if}
        		<div class="custom-tabs">
                {* <!-- Die "normalen" Tabs auf der linken Seite --> *}
                {block name="mv_description_tabs"}
                    {counter name="tabButtons" start=0 print=FALSE}
                    
                    {foreach from=$mvTabConfiguration item=mvTab}
                        {if $mvTab.side != 'right'}
                            {include file='frontend/detail/tabbuttons.tpl' dataAttributeName='data-activate-tab'}
                        {/if}
                    {/foreach}
                {/block}
                
                {* <!-- Die weiteren Tabs auf der linken Seite,
                die nur sichtbar sind, wenn wir in der mobilen Ansicht sind --> *}
                {block name="mv_description_tabs_left_mobile"}
                    {counter name="tabButtons" start=0 print=FALSE}
                    
                    {foreach from=$mvTabConfiguration item=mvTab}
                        {if $mvTab.side == 'right'}
                            {include file='frontend/detail/tabbuttons.tpl' dataAttributeName='data-activate-tab-mobile'}
                        {/if}
                    {/foreach}
                {/block}
        		</div>
        
        		{* <!-- Ab hier beginnt der Inhalt der Tabs! --> *}
            <div class="custom-tabs--container">
                {counter name="tabContent" start=0 print=FALSE}
                
                {foreach from=$mvTabConfiguration item=mvTab}
            				{if $mvTab.side != 'right'}
		                    {include file='frontend/detail/tabcontent.tpl' dataAttributeName='data-active-tab'}
                    {/if}
                {/foreach}
            </div>
            
        
        {if $mvTabsRightSide > 0}
            </div>
            <div class="custom-tabs-right">
            		<div class="custom-tabs">
                    {counter name="tabButtons" start=0 print=FALSE}
                    
                    {block name="mv_description_tabs"}
                        {foreach from=$mvTabConfiguration item=mvTab}
                            {if $mvTab.side == 'right'}
                                {include file='frontend/detail/tabbuttons.tpl' dataAttributeName='data-activate-tab-right'}
                            {/if}
                        {/foreach}
                    {/block}
                </div>
                
                {* <!-- Ab hier beginnt der Inhalt der Tabs auf der rechten Seite! --> *}
                <div class="custom-tabs--container">
                    {counter name="tabContent" start=0 print=FALSE}
                    
                    {foreach from=$mvTabConfiguration item=mvTab}
                        {if $mvTab.side == 'right'}
                            {include file='frontend/detail/tabcontent.tpl' dataAttributeName='data-active-tab-right'}
                        {/if}
                    {/foreach}
                </div>
            </div>
        {else}
        		</div><!-- END custom-tabs-left-only -->
        {/if}
    </div>
{/block}