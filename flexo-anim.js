jQuery( document ).ready(
	function () {
		jQuery( 'ul.flexo-list' ).hide();	// hide year lists
		jQuery( 'a.flexo-link' ).click(
			function() {
				jQuery( this ).next().slideToggle( 'fast' );
				return false;
			}
		);
		jQuery( 'a.flexo-decade-link' ).click(
			function() {
				jQuery( this ).nextAll().slideToggle( 'fast' );
				return false;
			}
		);
	}
);
