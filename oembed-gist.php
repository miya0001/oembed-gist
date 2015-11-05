<?php
/*
Plugin Name: oEmbed Gist
Plugin URI: https://github.com/miya0001/oembed-gist
Description: Embed source from gist.github.
Author: Takayuki Miyauchi
Version: 2.0.3
Author URI: http://firegoby.jp/
*/

$oe_gist = new gist();
$oe_gist->register();

class gist {

	private $shotcode_tag = 'gist';
	private $noscript;
	private $regex = '#(https://gist.github.com/([^\/]+\/)?([a-zA-Z0-9]+)(\/[a-zA-Z0-9]+)?)(\#file(\-|_)(.+))?$#i';

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
			$this->get_gist_regex(),
			array( $this, 'handler' )
		 );

		add_shortcode( $this->get_shortcode_tag(), array( $this, 'shortcode' ) );

		add_filter(
			'jetpack_shortcodes_to_include',
			array( $this, 'jetpack_shortcodes_to_include' )
		 );

		add_filter(
			'oembed_providers',
			array( $this, 'oembed_providers' )
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

	function oembed_providers( $providers )
	{
		//Support to Press This.
		global $pagenow;
		if ( 'press-this.php' == $pagenow && ! array_key_exists( $this->get_gist_regex(), $providers ) ) {
			$providers[ $this->get_gist_regex() ] = array(
				'https://gist.github.com/{id}.{format}', //dummy value
				true
			);
		}
		return $providers;
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
			font-size: 12px !important;
			height: 18px !important;
			line-height: 18px !important;
		}
		.gist .line
		{
			white-space: pre !important;
			width: auto !important;
			word-wrap: normal !important;
		}
		.gist .line span
		{
			word-wrap: normal !important;
		}
		</style>
		<?php
	}

	public function handler( $m, $attr, $url, $rattr )
	{
		if ( !isset( $m[7] ) || !$m[7] ) {
			$m[7] = null;
		}

		return $this->shortcode( array(
			'url'  => $m[1],
			'id'   => $m[3],
			'file' => $m[7],
		) );
	}

	public function shortcode( $p )
	{
		if ( isset( $p['url'] ) && $p['url'] ) {
			$url = $p['url'];
		} elseif ( preg_match( "/^[a-zA-Z0-9]+$/", $p['id'] ) ) {
			$url = 'https://gist.github.com/' . $p['id'];
		}

		$noscript = sprintf(
			__( 'View the code on <a href="%s">Gist</a>.', 'oembed-gist' ),
			esc_url( $url )
		);

		$url = $url . '.js';

		if ( isset( $p['file'] ) && $p['file'] ) { //RRD: Fixed line 79 error by adding isset()
			$file = preg_replace( '/[\-\.]([a-z]+)$/', '.\1', $p['file'] );
			$url = $url . '?file=' . $file;
		}

		if( is_feed() ){
			return $noscript;
		}else{
			return sprintf(
				'<div class="oembed-gist"><script src="%s"></script><noscript>%s</noscript></div>',
				$url,
				$noscript
			);
		}
	}

	public function get_gist_regex()
	{
		return $this->regex;
	}

	private function get_shortcode_tag()
	{
		return apply_filters( 'oembed_gist_shortcode_tag', $this->shotcode_tag );
	}

}

// EOF
