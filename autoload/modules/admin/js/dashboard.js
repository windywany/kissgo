define('admin/js/dashboard', function(require, exports) {
	var lang = require('admin/i18n/{locale}');
	if (!lang) {
		lang = {
			start : 'Start'
		};
	}
	exports.main = function() {
		var width = $('#workbench').width(), max_cols = parseInt(width / 130,
				10);
		var disableclick = function() {
			return false;
		};
		window.ksgGridster = $(".gridster ul").gridster({
			widget_margins : [ 10, 10 ],
			max_cols : max_cols,
			widget_base_dimensions : [ 120, 120 ],
			resize : {
				enabled : true
			},
			serialize_params : function($w, wgd) {
				return {
					id : $w.attr('id'),
					col : wgd.col,
					row : wgd.row,
					size_x : wgd.size_x,
					size_y : wgd.size_y
				};
			},
		}).data('gridster').resizable();
		ksgGridster.disable();
		ksgGridster.disable_resize();
		$('#cancel-edit-start').on(
				'click',
				function() {
					$('#cancel-edit-start').addClass('hide');
					$('#edit-start-screen').removeClass('editing').find('i')
							.removeClass('fg-green').removeClass('icon-floppy')
							.addClass('icon-grid').addClass('fg-blue');
					ksgGridster.disable();
					ksgGridster.disable_resize();
					$('#start-screen').find('a').unbind('click', disableclick);
					return false;
				});
		$('#edit-start-screen').click(
				function() {
					if ($(this).hasClass('editing')) {
						$('#cancel-edit-start').addClass('hide');
						$(this).find('i').removeClass('fg-green').removeClass(
								'icon-floppy').addClass('icon-grid').addClass(
								'fg-blue');
						ksgGridster.disable();
						ksgGridster.disable_resize();
						$('#start-screen').find('a').unbind('click',
								disableclick);
						var serialData = ksgGridster.serialize();
					} else {
						$(this).find('i').removeClass('icon-grid').removeClass(
								'fg-blue').addClass('fg-green').addClass(
								'icon-floppy');
						ksgGridster.enable();
						ksgGridster.enable_resize();
						$('#start-screen').find('a').on('click', disableclick);
						$('#cancel-edit-start').removeClass('hide');
					}
					$(this).toggleClass('editing');
					return false;
				});
	};
});
