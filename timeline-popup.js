var dlg = '';

/* Creates a popup out of a dialog with given id */
function create_popup_with_id(id, width) {
	if (!width) {
		width = 'auto';
	}
	
	dlg = $("#" + id).dialog({
						autoOpen: false,
						width: width, 
						height: 'auto',
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
				console.log(dlg);
}

/* Loads a popup with given url */
function timeline_load_popup_by_id(id, load_url) {
	create_popup_with_id(id, 500);
	$("#" + id).dialog("open");
	$("#" + id).load(load_url);
}

/* Creates an image popup with given src */
function timeline_show_image_popup_by_id(id, src) {
	create_popup_with_id(id, 640);
	$("#" + id).dialog("open");
	$("#" + id).html("<img src='" + src + "' />");
}
