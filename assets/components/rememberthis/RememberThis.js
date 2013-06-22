(function($) {
	// default settings
	var defaults = {
		ajaxLoaderImg: 'assets/components/rememberthis/ajax-loader.gif',
		onBeforeAdd: function(elem, id) {
		},
		onAfterAdd: function(elem, id) {
		},
		onBeforeDelete: function(elem, id) {
		},
		onAfterDelete: function(elem, id) {
		}
	};
	// globals
	var settings;
	var loadImage;
	var rememberThis = $('.rememberthis');

	// methods
	var methods = {
		init: function(options) {
			settings = $.extend({}, defaults, options);
			if (settings.ajaxLoaderImg !== '') {
				loadImage = $('<img>').addClass('rememberload').attr('src', settings.ajaxLoaderImg);
			}
			else {
				loadImage = $('<span>').addClass('rememberload');
			}
			methods.add();
			methods.delete(rememberThis);
		},
		add: function() {
			$('.rememberadd').click(function(e) {
				e.preventDefault();
				$(this).append(loadImage.clone());
				methods.rememberAdd(this, methods.queryString($(this).attr('href'), 'add'));
			});
		},
		delete: function(elem) {
			$('.rememberdelete', elem).click(function(e) {
				e.preventDefault();
				$(this).hide().after(loadImage.clone());
				methods.rememberDelete(this, methods.queryString($(this).attr('href'), 'delete'));
			});
		},
		rememberAdd: function(elem, id) {
			settings.onBeforeAdd.call(elem, id);
			$.ajax({
				type: 'GET',
				url: 'assets/components/rememberthis/connectors/connector.php',
				data: {
					'action': 'remember',
					'add': id
				},
				success: function(data) {
					$('.rememberload').remove();
					if ($('.rememberempty', rememberThis).length) {
						rememberThis.slideUp('fast', function() {
							$(this).html(data).slideDown('slow');
							methods.delete($(this));
						});
					} else {
						if (data.length) {
							var newDoc = $(data).attr('style', 'display: none');
							rememberThis.append(newDoc);
							newDoc.slideDown('slow');
							methods.delete(newDoc);
						}
					}
					settings.onAfterAdd.call(elem, id);
				}
			});
		},
		rememberDelete: function(elem, id) {
			settings.onBeforeDelete.call(elem, id);
			$.ajax({
				type: 'GET',
				url: 'assets/components/rememberthis/connectors/connector.php',
				data: {
					'action': 'remember',
					'delete': id
				},
				success: function(data) {
					if (isNaN(data)) {
						rememberThis.slideUp('slow', function() {
							$(this).html($.trim(data)).slideDown('fast');
						});
					} else {
						$(elem).parent().slideUp('slow', function() {
							$(this).remove();
						});
					}
					settings.onAfterDelete.call(elem, id);
				}
			});
		},
		queryString: function(query, param) {
			var queryString = {};
			query.replace(
					new RegExp('([^?=&]+)(=([^&]*))?', 'g'), function($0, $1, $2, $3) {
				queryString[$1] = $3;
			}
			);
			return queryString[param];
		}
	};

	$.fn.rememberThis = function(methodOrOptions) {
		if (methods[methodOrOptions]) {
			// Apply method
			return methods[methodOrOptions].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof methodOrOptions === 'object' || !methodOrOptions) {
			// Default to "init"
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + methodOrOptions + ' does not exist on jQuery.rememberThis');
		}
	};
})(jQuery);
