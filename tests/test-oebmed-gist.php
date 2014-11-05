<?php

class oEmbedGist_Test extends WP_UnitTestCase {

	function test_oEmbed() {
		$contents = apply_filters('the_content', "https://gist.github.com/miya0001/cabf03ef768ba7f9ba7d");
		$this->assertEquals(
			'<script src="https://gist.github.com/cabf03ef768ba7f9ba7d.js?file="></script><noscript><p>View the code on <a href="https://gist.github.com/cabf03ef768ba7f9ba7d">Gist</a>.</p></noscript>',
			trim($contents)
		);
	}
}
