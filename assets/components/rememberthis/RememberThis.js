function rememberAdd(id) {
	jQuery.ajax({
		type: 'GET',
		url: 'assets/components/rememberthis/connectors/connector.php',
		data: {
			'action': 'remember',
			'add': id
		},
		success: function(data) {
			jQuery('.rememberload').remove();
			if (jQuery('.rememberthis .rememberempty').length) {
				jQuery('.rememberthis').fadeOut('fast', function() {
					jQuery(this).html(data).slideDown('slow');
				});
			} else {
				if (data.length) {
					var newDoc = jQuery(data).attr('style', 'display: none');
					jQuery('.rememberthis').append(newDoc);
					newDoc.slideDown('slow');
				}
			}
		}
	});
}

function rememberDelete(id) {
	jQuery.ajax({
		type: 'GET',
		url: 'assets/components/rememberthis/connectors/connector.php',
		data: {
			'action': 'remember',
			'delete': id
		},
		success: function(data) {
			jQuery('.rememberload').remove();
			if (isNaN(data)) {
				jQuery('.rememberdoc').slideUp('slow', function() {
					jQuery(this).html(jQuery.trim(data)).fadeIn('fast');
				});
				return true;
			}
		}
	});
}

jQuery(document).ready( function() {
	jQuery('.rememberadd').click( function() {
		jQuery(this).append('<img class="rememberload" src="assets/components/rememberthis/ajax-loader.gif" />');
		var queryString = {};
		jQuery(this).attr('href').replace(
			new RegExp("([^?=&]+)(=([^&]*))?", "g"), function($0, $1, $2, $3) {
				queryString[$1] = $3;
			}
			);
		var id = queryString['add'];
		rememberAdd(id);
		return false;
	});
	jQuery('.rememberdelete').live('click', function() {
		jQuery(this).append('<img class="rememberload" src="assets/components/rememberthis/ajax-loader.gif" />');
		var queryString = {};
		jQuery(this).attr('href').replace(
			new RegExp("([^?=&]+)(=([^&]*))?", "g"), function($0, $1, $2, $3) {
				queryString[$1] = $3;
			}
			);
		var id = queryString['delete'];
		if (!rememberDelete(id)) {
			jQuery(this).parent().slideUp('slow', function() {
				jQuery(this).remove();
			});
		}
		return false;
	});
});