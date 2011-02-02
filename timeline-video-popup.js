var dlg = '';

function create_popup_with_id(id) {
	dlg = $("#popup_dialog_" + id).dialog({
						autoOpen: false,
						width: 500, 
						modal: true,
						open: function(event, ui) { 
							console.log('opening');
							$(".ui-dialog-titlebar-close").hide(); 	
						},
						buttons: {
							"X": function() { 
								$(this).dialog("close"); 
							} 
	}});
}

function simplekaltura_show_popup_by_id(id, load_url) {
	create_popup_with_id(id);
	$("#popup_dialog_" + id).dialog("open");
	$("#popup_dialog_" + id).load(load_url + '&entryid=' + id);
}
