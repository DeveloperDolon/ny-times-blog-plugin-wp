jQuery(document).ready(function($){

	$.post(ajaxurl, {

		action: 'pgnyt_articles_refresh_results'

	}, function( response ) {

		console.log( 'AJAX complete' );

	});

});
