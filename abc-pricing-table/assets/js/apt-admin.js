(function ($) {
	'use strict';

	// Namespace for admin functions
	window.ABC_PT_Admin = {
		copy_shortcode: function () {
			var copyText = document.getElementById("abc-pricing-shortcode");
			if (copyText) {
				if (navigator.clipboard && window.isSecureContext) {
					navigator.clipboard.writeText(copyText.value).then(function () {
						$('#copy-msg').fadeIn(1000).fadeOut(2500);
					}).catch(function () {
						copyText.select();
						document.execCommand("copy");
						$('#copy-msg').fadeIn(1000).fadeOut(2500);
					});
				} else {
					copyText.select();
					document.execCommand("copy");
					$('#copy-msg').fadeIn(1000).fadeOut(2500);
				}
			}
		},
		add_new_column: function () {
			var i18n = window.apt_admin_i18n || {
				yes: 'Yes',
				no: 'No',
				name: 'Name',
				pricing: 'Pricing',
				pricing_plan: 'Pricing Plan',
				pricing_features: 'Pricing Features',
				button_text: 'Button Text',
				button_url: 'Button Url',
				confirm_delete: 'Are sure to delete this columns from table?'
			};
			var total_cols = $('#apt_column_count').length ? $('#apt_column_count').val() : $('#total_cols').val();
			var columns_class_name = "column_" + total_cols;
			var iconpick_class_name = "iconpick_" + total_cols;
			var new_col_html = '' +
				'<div id="columns[]" class="pri_main_div col-md-4 ' + columns_class_name + '">' +
				'<div class="pri_head">' +
				'<div class="switch-field em_size_field col-md-6" data-toggle="tooltip" data-placement="top" title="This Table Will Be Featured" aria-hidden="true">' +
				'<input type="radio" name="featured_button[' + total_cols + ']" id="featured_button1[' + total_cols + ']" value="true" >' +
				'<label for="featured_button1[' + total_cols + ']">' + i18n.yes + '</label>' +
				'<input type="radio" name="featured_button[' + total_cols + ']" id="featured_button2[' + total_cols + ']" value="false" checked >' +
				'<label for="featured_button2[' + total_cols + ']">' + i18n.no + '</label>' +
				'</div>' +
				'<a data-toggle="tooltip" data-placement="top" title="Delete table" class="time" id="pri_delete" onclick=ABC_PT_Admin.delete_column("' + columns_class_name + '");><span class="dashicons dashicons-trash"></span></a>' +
				'<input type="text" id="pricing_name_' + total_cols + '" class="text" name="pricing_name[]" placeholder="' + i18n.name + '" value="">' +
				'</div>' +
				'<ul>' +
				'<li>' +
				'<input type="text" id="pricing_price_' + total_cols + '" name="pricing_price[]" class="text" placeholder="' + i18n.pricing + '" value="">' +
				'</li>' +
				'<li>' +
				'<input type="text" id="pricing_plan_' + total_cols + '" name="pricing_plan[]" class="text" placeholder="' + i18n.pricing_plan + '" value="">' +
				'</li>' +
				'<li class="features">' +
				'<textarea id="pricing_features_' + total_cols + '" name="pricing_features[]" class="text" placeholder="' + i18n.pricing_features + '" rows="7"></textarea>' +
				'</li>' +
				'<li>' +
				'<input type="text" id="pricing_btn_text_' + total_cols + '" name="pricing_btn_text[]" class="text" placeholder="' + i18n.button_text + '" value="">' +
				'</li>' +
				'<li>' +
				'<input type="text" id="pricing_btn_url_' + total_cols + '" name="pricing_btn_url[]" class="text" placeholder="' + i18n.button_url + '" value="">' +
				'</li>' +
				'</ul>' +
				'</div>';

			var new_icon_picker = '<button type="button" value="" id="pricing_icon_pick[]" name="pricing_icon_pick[]" class="' + iconpick_class_name + ' ' + columns_class_name + ' target_picker btn btn-default" data-iconset="fontawesome" data-icon="fa-wifi" role="iconpicker"></button>';

			$('#iconpicker-container').append(new_icon_picker);
			$('#pricing-container').append(new_col_html);

			var $counter = $('#apt_column_count').length ? $('#apt_column_count') : $('#total_cols');
			var current_total = parseInt($counter.val(), 10);
			$counter.val(current_total + 1);
			$('[data-toggle="tooltip"]').tooltip();
			$('.target_picker').iconpicker();
		},
		delete_column: function (col_id) {
			var i18n = window.apt_admin_i18n || { confirm_delete: 'Are sure to delete this columns from table?' };
			if (confirm(i18n.confirm_delete)) {
				$("." + col_id).fadeOut(1000, function () {
					$("." + col_id).remove();
				});
			}
		},
		display_range_value: function (id, value) {
			var slider = document.getElementById(id);
			var output = document.getElementById(id + "-value");
			if (slider && output) {
				output.innerHTML = slider.value;
				slider.oninput = function () {
					output.innerHTML = this.value;
				};
			}
		}
	};

	$(document).ready(function () {
		// Tooltip setup
		if ($.fn.tooltip) {
			$('[data-toggle="tooltip"]').tooltip();
		}

		// Iconpicker visibility based on template
		function updateIconPickerVisibility() {
			var design = $('input[name="pricing_table_design"]:checked').val();
			if (design === "template1") {
				$(".pricing_iconpicker").show();
			} else {
				$(".pricing_iconpicker").hide();
			}
		}
		updateIconPickerVisibility();

		$('input[name="pricing_table_design"]').change(function () {
			updateIconPickerVisibility();
		});

		// Color Pickers
		var colorPickers = '#heading_text_color, #heading_background_color, #background_hover_color, #button_color, #button_heading_color, #button_hover_color, #feature_heading_text_color, #feature_heading_background_color, #feature_background_hover_color, #feature_button_color, #feature_button_heading_color, #feature_button_hover_color';
		if ($.fn.wpColorPicker) {
			$(colorPickers).wpColorPicker();
			$(document).ajaxComplete(function () {
				$(colorPickers).wpColorPicker();
			});
		}

		// Tab menu navigation
		$("div.bhoechie-tab-menu>div.list-group>a").click(function (e) {
			e.preventDefault();
			$(this).siblings('a.active').removeClass("active");
			$(this).addClass("active");
			var index = $(this).index();
			$("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
			$("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
		});

		// Layout selection class toggling and iris preset updates
		function updateLayoutClasses(layout) {
			$('.pricing_layout_one, .pricing_layout_two, .pricing_layout_three, .pricing_table_four').removeClass('team_layout');
			if (layout === 'template1') $('.pricing_layout_one').addClass('team_layout');
			if (layout === 'template2') $('.pricing_layout_two').addClass('team_layout');
			if (layout === 'template3') $('.pricing_layout_three').addClass('team_layout');
			if (layout === 'template4') $('.pricing_table_four').addClass('team_layout');
		}

		var initialLayout = $('[name=pricing_table_design]:checked').val();
		updateLayoutClasses(initialLayout);

		$('input[type=radio][name=pricing_table_design]').change(function () {
			var selectedLayout = $('[name=pricing_table_design]:checked').val();
			updateLayoutClasses(selectedLayout);

			if ($.fn.iris) {
				if (selectedLayout === 'template1') {
					$('#heading_text_color').iris('color', '#ffffff');
					$('#heading_background_color').iris('color', '#962744');
					$('#background_hover_color').iris('color', '#ff4266');
					$('#button_color').iris('color', '#962744');
					$('#button_heading_color').iris('color', '#ffffff');
					$('#button_hover_color').iris('color', '#ff4266');
				} else if (selectedLayout === 'template2') {
					$('#heading_text_color').iris('color', '#ffffff');
					$('#heading_background_color').iris('color', '#F26547');
					$('#background_hover_color').iris('color', '#F28168');
					$('#button_color').iris('color', '#F26547');
					$('#button_heading_color').iris('color', '#ffffff');
					$('#button_hover_color').iris('color', '#F28168');
				} else if (selectedLayout === 'template3') {
					$('#heading_text_color').iris('color', '#ffffff');
					$('#heading_background_color').iris('color', '#1E73BE');
					$('#background_hover_color').iris('color', '#4D4D4D');
					$('#button_color').iris('color', '#1E73BE');
					$('#button_heading_color').iris('color', '#ffffff');
					$('#button_hover_color').iris('color', '#4D4D4D');
				} else if (selectedLayout === 'template4') {
					$('#heading_text_color').iris('color', '#ffffff');
					$('#heading_background_color').iris('color', '#1ABC9C');
					$('#background_hover_color').iris('color', '#1ABC9C');
					$('#button_color').iris('color', '#1ABC9C');
					$('#button_heading_color').iris('color', '#ffffff');
					$('#button_hover_color').iris('color', '#1ABC9C');
				}
			}
		});
	});
})(jQuery);
