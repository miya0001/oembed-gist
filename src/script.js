( function() {

	var load_css = function() {

		var me = '/oembed-gist/js/script.min.js';
		var css = '/oembed-gist/css/style.min.css';

		var scripts = document.querySelectorAll( 'script' );
		for ( var i = 0; i < scripts.length; i++ ) {
			var item = scripts[ i ];
			var src = item.getAttribute( 'src' );
			if ( src ) {
				if ( 0 < src.indexOf( me ) ) {
					css = src.replace( me, css );
				}
			}
		}

		var link = document.createElement( 'link' );
		link.setAttribute( 'rel', 'stylesheet' );
		link.setAttribute( 'type', 'text/css' );
		link.setAttribute( 'media', 'all' );
		link.setAttribute( 'href', css );
		document.head.appendChild( link );
	}

	if ( document.querySelector( '.gist' ) ) {
		load_css();
	}

} )();
