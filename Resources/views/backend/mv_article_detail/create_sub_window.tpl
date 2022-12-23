<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{link file="backend/_resources/css/bootstrap.min.css"}">
    
    {block name="content/header_tags"}{/block}
		
		{literal}
				<style>
						table { width: 100%; margin-bottom: 28px; }
						h1 { font-size: 16px; color: #1C8FDD; }
						body { padding-left: 10px; padding-right: 10px; }
				</style>
		{/literal}
</head>
<body role="document">
		<div class="article_data">
				<h1>Tab: {$tab_data.title}</h1>
		</div>

		<table class="x-grid-table x-grid-table-resizer">
				<tbody>
						<tr>
								<th>Titel</th>
								<th>Feldname</th>
								<th>Sortierung</th>
								<th>Seite</th>
						</tr>
            <tr>
								<td>
                    <input type="text" value="{$tab_data.title}" name="title" id="title" />
                </td>
                <td>
                		<select name="fieldname" id="fieldname">
                    		<option value="">-- Bitte wählen --</option>
                        
                        {foreach from=$fieldname_values item=fieldname_val}
                        		<option value="{$fieldname_val.internal_fieldname|htmlspecialchars}"{if $tab_data.fieldname == $fieldname_val.internal_fieldname} selected="selected"{/if}>{$fieldname_val.field|htmlspecialchars}</option>
                        {/foreach}
                    </select>
                </td>
                <td>
                    <input type="text" value="{$tab_data.sortorder}" name="sortorder" id="sortorder" />
                </td>
                <td>
                		<select name="side" id="side">
                    		<option value="left"{if $tab_data.side == 'left'} selected="selected"{/if}>Linke Seite</option>
                        <option value="right"{if $tab_data.side == 'right'} selected="selected"{/if}>Rechte Seite</option>
                    </select>
                </td>
            </tr>
				</tbody>
		</table>
		
    <table class="x-grid-table x-grid-table-resizer">
    		<tbody>
        		<tr>
            		<td>
                		<button type="button" class="btn btn-primary" id="mv_save_changes" data-attr-article-id="{$tab_data.id}">Änderungen speichern</button>
                </td>
              	<td style="text-align: right;">
                		<button type="button" class="btn btn-danger" id="mv_delete_changes" data-attr-article-id="{$tab_data.id}">Eintrag löschen</button>
                </td>
            </tr>
        </tbody>
    </table>
		
<script type="text/javascript" src="{link file="backend/base/frame/postmessage-api.js"}"></script>
<script type="text/javascript" src="{link file="backend/_resources/js/jquery-2.1.4.min.js"}"></script>
<script type="text/javascript" src="{link file="backend/_resources/js/bootstrap.min.js"}"></script>

		{block name="content/javascript"}
				{literal}
				<script>
				$(function() {
						$('#mv_save_changes').off('click');
						$('#mv_save_changes').on('click', function() {
								var title = $('#title').val();
								var fieldname = $('#fieldname').val();
								var sortorder = $('#sortorder').val();
								var side = $('#side').val();
								
								var id = $(this).attr('data-attr-article-id');
								
								//Wir könnten auch so an controller url herankommen:
								//var url = "{url controller="ExampleModulePlainHtml" action="getEmotion" __csrf_token=$csrfToken}",
								
								var url = '/backend/MvArticleDetail/save/?id=' + id;
								
								if (document.location.protocol != 'https:') {
										url = 'http://' + document.location.host + url;
								} else {
										url = 'https://' + document.location.host + url;
								}
								
								var post_data = {
										id: id,
										title: title,
										fieldname: fieldname,
										sortorder: sortorder,
										side: side
								};
								
								$.ajax({
										method: "POST",
										url: url,
										data: post_data
								}).done(function( msg ) {
										if(msg == 'done') {
												postMessageApi.createGrowlMessage('Daten gespeichert.', 'Die Daten wurden erfolgreich gespeichert.');
										} else {
												postMessageApi.createGrowlMessage('Es ist ein Fehler aufgetreten.', 'Details befinden sich in der Javascript Console ihres Browsers.');
												console.log(msg);
										}
								});
						});
						
						// Handler für Klick auf den Button "Eintrag löschen"
						$('#mv_delete_changes').off('click');
						$('#mv_delete_changes').on('click', function() {
								if(!window.confirm("Wollen Sie diesen Eintrag wirklich löschen?")) {
										return;
								}
								
								
								var id = $(this).attr('data-attr-article-id');
								
								var url = '/backend/MvArticleDetail/delete/?id=' + id;
								
								if (document.location.protocol != 'https:') {
										url = 'http://' + document.location.host + url;
								} else {
										url = 'https://' + document.location.host + url;
								}
								
								var post_data = {
										id: id
								};
								
								$.ajax({
										method: "POST",
										url: url,
										data: post_data
								}).done(function( msg ) {
										if(msg == 'done') {
												postMessageApi.createGrowlMessage('Eintrag gelöscht.', 'Der Datensatz wurde gelöscht.');
												postMessageApi.window.destroy();
										} else {
												postMessageApi.createGrowlMessage('Es ist ein Fehler aufgetreten.', 'Details befinden sich in der Javascript Console ihres Browsers.');
												console.log(msg);
										}
								});
						});
						
						
						
						/*
						$('.mv-article-item').off('click');
						$('.mv-article-item').on('click', function() {
								var article_id = $(this).attr('data-attr-article-id');
								
								console.log(article_id);
							
								var values = {
										width: 500,
										height: 500,
										component: 'customSubWindow',
										url: 'mvFeaturesList/create_sub_window/?article_id=' + article_id,
										title: 'Feature Details'
								};
								
								postMessageApi.createSubWindow(values);
								
								
								
						});
						*/
						
						/*		
						$('.btn-subwindow').on('click', function() {
								var values = {
										width: 500,
										height: 500,
										component: 'customSubWindow',
										url: 'ExampleModulePlainHtml/create_sub_window',
										title: 'Plugin Konfiguration'
								};
								postMessageApi.createSubWindow(values);
								window.setTimeout(function() {
										postMessageApi.sendMessageToSubWindow({
												component: values.component,
												params: {
														msg: 'A message from another galaxy beyond the sky.',
														foo: [ 'bar', 'batz', 'foobar' ]
												}
										});
								}, 3000);
						});
						*/
				});
				</script>
				{/literal}
		{/block}
</body>
</html>
