{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
		<div class="x-panel">
				<div class="x-grid-body">
						{if $tabs|count == 0}
								<p>Keine Daten</p>
						{else}
								<table class="x-grid-table x-grid-table-resizer mv-article-table">
										<thead>
												<tr class="x-grid-header-row">
														<th>Titel</th>
														<th>Freitextfeld</th>
                            <th>Sortierung</th>
                            <th>Tab-Seite (rechts, links)</th>
												</tr>
										</thead>
										<tbody>
												{foreach from=$tabs item=tab}
														<tr class="x-grid-row mv-article-item" data-attr-article-id="{$tab.id|htmlspecialchars}">
																<td class="x-grid-cell"><span>{$tab.title|htmlspecialchars}</span></td>
                                <td class="x-grid-cell"><span>{foreach from=$fieldname_values item=fieldname_val}{if $tab.fieldname == $fieldname_val.internal_fieldname}{$fieldname_val.field|htmlspecialchars}{/if}{/foreach}</span></td>
                                <td class="x-grid-cell"><span">{$tab.sortorder|htmlspecialchars}</span></td>
                                <td class="x-grid-cell"><span">{$tab.side|htmlspecialchars}</span></td>
														</tr>
												{/foreach}
										</tbody>
								</table>
						{/if}
            
            <div class="addButton">
            		<button type="button" id="mvAddItem" style="margin-top: 12px;">Tab hinzufügen</button>
            </div>
				</div>
		</div>
		
		<style>
				.container { width: 100%; }
				body { padding-top: 10px!important; }
				.navbar { display: none; }
				table { width: 100%; }
				thead th { border-bottom: 1px solid rgb(209, 224, 236); background-color: #CCC; }
				tr { border-bottom: 1px solid rgb(209, 224, 236); }
				tr:nth-child(even) { background-color: #e6f2f8 !important; }
				table td { cursor: pointer; }
				table td:first-child { width: 60%; }
				table th:first-child { width: 60%; }
				table tbody tr:hover { background-color: #006; color: #FFF; }
		</style>
{/block}

{block name="content/javascript"}
		{literal}
		<script>
		$(function() {
				$('.mv-article-item').off('click');
				$('.mv-article-item').on('click', function() {
						var tab_id = $(this).attr('data-attr-article-id');
						
						console.log(tab_id);
						
						var values = {
								width: 800,
								height: 500,
								component: 'customSubWindow',
								url: 'MvArticleDetail/create_sub_window/?tab_id=' + tab_id,
								title: 'Tab-Einstellungen'
						};
						
						postMessageApi.createSubWindow(values);	
				});
				
				$('#mvAddItem').off('click');
				$('#mvAddItem').on('click', function() {
						var tab_id = 0;
						
						var values = {
								width: 800,
								height: 500,
								component: 'customSubWindow',
								url: 'MvArticleDetail/create_sub_window/?tab_id=' + tab_id,
								title: 'Tab erstellen'
						};
						
						postMessageApi.createSubWindow(values);	
				});
		});
		</script>
		{/literal}
{/block}