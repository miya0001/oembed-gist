<?php

class oEmbedGist_Test extends WP_UnitTestCase
{
	/**
	 * Add post and post to be set current.
	 *
	 * @param  array $args A hash array of the post object.
	 * @return none
	 */
	public function setup_postdata( $args )
	{
		global $post;
		global $wp_query;

		$wp_query->is_singular = true;

		$post_id = $this->factory->post->create( $args );
		$post = get_post( $post_id );
		setup_postdata( $post );
	}

	/**
	 * @test
	 */
	public function shortcode_test()
	{
		$this->assertSame(
			'<div class="oembed-gist"><script src="https://gist.github.com/2759039.js"></script><noscript>View the code on <a href="https://gist.github.com/2759039">Gist</a>.</noscript></div>',
			do_shortcode( '[gist id="2759039"]' )
		);

		$this->assertSame(
			'<div class="oembed-gist"><script src="https://gist.github.com/2759039.js?file=2.php"></script><noscript>View the code on <a href="https://gist.github.com/2759039">Gist</a>.</noscript></div>',
			do_shortcode( '[gist id="2759039" file="2.php"]' )
		);

		$this->assertSame(
			'<div class="oembed-gist"><script src="https://gist.github.com/cabf03ef768ba7f9ba7d.js"></script><noscript>View the code on <a href="https://gist.github.com/cabf03ef768ba7f9ba7d">Gist</a>.</noscript></div>',
			do_shortcode( '[gist id="cabf03ef768ba7f9ba7d"]' )
		);

		$this->assertSame(
			'<div class="oembed-gist"><script src="https://gist.github.com/cabf03ef768ba7f9ba7d.js?file=setuser.sh"></script><noscript>View the code on <a href="https://gist.github.com/cabf03ef768ba7f9ba7d">Gist</a>.</noscript></div>',
			do_shortcode( '[gist id="cabf03ef768ba7f9ba7d" file="setuser.sh"]' )
		);
	}

	/**
	 * General gist
	 *
	 * @test
	 */
	public function the_content_01()
	{
		$this->setup_postdata( array(
			'post_content' => 'https://gist.github.com/miya0001/cabf03ef768ba7f9ba7d',
		) );

		$this->expectOutputString('<div class="oembed-gist"><script src="https://gist.github.com/miya0001/cabf03ef768ba7f9ba7d.js"></script><noscript>View the code on <a href="https://gist.github.com/miya0001/cabf03ef768ba7f9ba7d">Gist</a>.</noscript></div>'."\n");

		the_content();
	}

	/**
	 * General gist with file name
	 *
	 * @test
	 */
	public function the_content_02()
	{
		$this->setup_postdata( array(
			'post_content' => 'https://gist.github.com/miya0001/cabf03ef768ba7f9ba7d#file-setuser-sh',
		) );

		$this->expectOutputString('<div class="oembed-gist"><script src="https://gist.github.com/miya0001/cabf03ef768ba7f9ba7d.js?file=setuser.sh"></script><noscript>View the code on <a href="https://gist.github.com/miya0001/cabf03ef768ba7f9ba7d">Gist</a>.</noscript></div>'."\n");

		the_content();
	}

	/**
	 * Old api of gist
	 *
	 * @test
	 */
	public function the_content_03()
	{
		$this->setup_postdata( array(
			'post_content' => 'https://gist.github.com/2759039',
		) );

		$this->expectOutputString('<div class="oembed-gist"><script src="https://gist.github.com/2759039.js"></script><noscript>View the code on <a href="https://gist.github.com/2759039">Gist</a>.</noscript></div>'."\n");

		the_content();
	}

	/**
	 * Old api of gist with file name
	 *
	 * @test
	 */
	public function the_content_04()
	{
		$this->setup_postdata( array(
			'post_content' => 'https://gist.github.com/2759039#file_2.php',
		) );

		$this->expectOutputString('<div class="oembed-gist"><script src="https://gist.github.com/2759039.js?file=2.php"></script><noscript>View the code on <a href="https://gist.github.com/2759039">Gist</a>.</noscript></div>'."\n");

		the_content();
	}

	/**
	 * Old gist and new api of gist
	 *
	 * @test
	 */
	public function the_content_05()
	{
		$this->setup_postdata( array(
			'post_content' => 'https://gist.github.com/miya0001/2759039',
		) );

		$this->expectOutputString('<div class="oembed-gist"><script src="https://gist.github.com/miya0001/2759039.js"></script><noscript>View the code on <a href="https://gist.github.com/miya0001/2759039">Gist</a>.</noscript></div>'."\n");

		the_content();
	}

	/**
	 * Old gist and new api of gist with file name
	 *
	 * @test
	 */
	public function the_content_06()
	{
		$this->setup_postdata( array(
			'post_content' => 'https://gist.github.com/miya0001/2759039#file-2-php',
		) );

		$this->expectOutputString('<div class="oembed-gist"><script src="https://gist.github.com/miya0001/2759039.js?file=2.php"></script><noscript>View the code on <a href="https://gist.github.com/miya0001/2759039">Gist</a>.</noscript></div>'."\n");

		the_content();
	}

	/**
	 * Gist with revison
	 *
	 * @test
	 */
	public function the_content_07()
	{
		$this->setup_postdata( array(
			'post_content' => 'https://gist.github.com/miya0001/0583c8105592a4a1d9f4/7fbe9b0dfd0e4db493fa43630fd7345e49484a81',
		) );

		$this->expectOutputString('<div class="oembed-gist"><script src="https://gist.github.com/miya0001/0583c8105592a4a1d9f4/7fbe9b0dfd0e4db493fa43630fd7345e49484a81.js"></script><noscript>View the code on <a href="https://gist.github.com/miya0001/0583c8105592a4a1d9f4/7fbe9b0dfd0e4db493fa43630fd7345e49484a81">Gist</a>.</noscript></div>'."\n");

		the_content();
	}

	/**
	 * Gist with revison & file
	 *
	 * @test
	 */
	public function the_content_08()
	{
		$this->setup_postdata( array(
			'post_content' => 'https://gist.github.com/miya0001/0583c8105592a4a1d9f4/7fbe9b0dfd0e4db493fa43630fd7345e49484a81#file-gistfile1-sh',
		) );

		$this->expectOutputString('<div class="oembed-gist"><script src="https://gist.github.com/miya0001/0583c8105592a4a1d9f4/7fbe9b0dfd0e4db493fa43630fd7345e49484a81.js?file=gistfile1.sh"></script><noscript>View the code on <a href="https://gist.github.com/miya0001/0583c8105592a4a1d9f4/7fbe9b0dfd0e4db493fa43630fd7345e49484a81">Gist</a>.</noscript></div>'."\n");

		the_content();
	}
}
