<?php
/*
Plugin Name: oEmbed Gist
Plugin URI: https://github.com/miya0001/oembed-gist
Description: Embed source from gist.github.
Author: Takayuki Miyauchi
Version: 1.7.1
Author URI: http://firegoby.jp/
*/

$oe_gist = new gist();
$oe_gist->register();

class gist {

	private $shotcode_tag = 'gist';
	private $noscript;
	private $html = '<div class="oembed-gist"><script src="https://gist.github.com/%s.js%s"></script><noscript>%s</noscript></div>';

	function register()
	{
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	public function plugins_loaded()
	{
		add_action( 'wp_head', array( $this, 'wp_head' ) );

		load_plugin_textdomain(
			'oembed-gist',
			false,
			dirname( plugin_basename( __FILE__ ) ).'/languages'
		 );

		wp_embed_register_handler(
			'oe-gist',
			'#https://gist.github.com/([^\/]+\/)?([a-zA-Z0-9]+)(\#file(\-|_)(.+))?$#i',
			array( $this, 'handler' )
		 );

		add_shortcode( $this->get_shortcode_tag(), array( $this, 'shortcode' ) );

		add_filter(
			'jetpack_shortcodes_to_include',
			array( $this, 'jetpack_shortcodes_to_include' )
		 );
	}

	public function jetpack_shortcodes_to_include( $incs )
	{
		$includes = array();
		foreach ( $incs as $inc ) {
			if ( !preg_match( "/gist\.php\z/", $inc ) ) {
				$includes[] = $inc;
			}
		}
		return $includes;
	}

	public function wp_head()
	{
		?>
		<style>
		.gist table {
			margin-bottom: 0 !important;
		}
		.gist .line-numbers
		{
			width: 4em !important;
		}
		.gist .line,
		.gist .line-number
		{
			height: 1.5em !important;
			line-height: 1.5em !important;
			white-space: pre !important;
			overflow: hidden !important;
			box-sizing: border-box !important;
		}
		</style>
		<?php
	}

	public function handler( $m, $attr, $url, $rattr )
	{
		if ( !isset( $m[3] ) || !isset( $m[5] ) || !$m[5] ) {
			$m[5] = null;
		}

		return $this->shortcode( array(
			'id'   => esc_attr( $m[2] ),
			'file' => esc_attr( $m[5] ),
		) );
	}

	public function shortcode( $p )
	{
		if ( preg_match( "/^[a-zA-Z0-9]+$/", $p['id'] ) ) {
			$noscript = sprintf(
				__( 'View the code on <a href="https://gist.github.com/%s">Gist</a>.', 'oembed-gist' ),
				$p['id']
			 );
			if ( isset( $p['file'] ) ) { //RRD: Fixed line 79 error by adding isset()
				$file = preg_replace( '/[\-\.]([a-z]+)$/', '.\1', $p['file'] );
				return sprintf( $this->html, $p['id'], '?file='.$file, $noscript );
			} else {
				return sprintf( $this->html, $p['id'], '', $noscript );
			}
		}
	}

	private function get_shortcode_tag()
	{
		return apply_filters( 'oembed_gist_shortcode_tag', $this->shotcode_tag );
	}

}

// EOF
