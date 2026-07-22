(function ($) {
	'use strict';

	window.ABC_PT_CopyShortcode = function (postId) {
		var copyText = document.getElementById('abc-pricing-shortcode-' + postId);
		if (copyText) {
			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(copyText.value).then(function () {
					$('#copy-msg-' + postId).fadeIn(1000, 'linear').fadeOut(2500, 'swing');
				}).catch(function () {
					copyText.select();
					document.execCommand('copy');
					$('#copy-msg-' + postId).fadeIn(1000, 'linear').fadeOut(2500, 'swing');
				});
			} else {
				copyText.select();
				document.execCommand('copy');
				$('#copy-msg-' + postId).fadeIn(1000, 'linear').fadeOut(2500, 'swing');
			}
		}
		return false;
	};
})(jQuery);
