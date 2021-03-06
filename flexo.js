jQuery( document ).ready(
	function () {
		jQuery( 'ul.flexo-list' ).hide();	// hide year lists
		jQuery( 'a.flexo-link' ).click(
			function() {
				var nextEl = jQuery( this ).next();
				if ( nextEl.is( ':hidden' ) ) {
					nextEl.show();
				} else {
					nextEl.hide();
				}
				return false;
			}
		);
		jQuery( 'a.flexo-decade-link' ).click(
			function() {
				var nextEl = jQuery( this ).nextAll();
				if ( nextEl.is( ':hidden' ) ) {
					nextEl.show();
				} else {
					nextEl.hide();
				}
				return false;
			}
		);
	}
);
