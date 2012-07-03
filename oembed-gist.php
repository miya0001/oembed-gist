<?php
/*
Plugin Name: oEmbed Gist
Plugin URI: http://firegoby.jp/wp/oembed-gist
Description: Embed source from gist.github.
Author: Takayuki Miyauchi
Version: 1.3.0
Author URI: http://firegoby.jp/
*/

/*
Copyright (c) 2010 Takayuki Miyauchi (THETA NETWORKS Co,.Ltd).

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

Modified August 18, 2011 by Alex King (alexking.org) to add NOSCRIPT and i18n support

*/

new gist();

class gist {

private $noscript;
private $html_gist = '<script src="https://gist.github.com/%s.js%s"></script><noscript>%s</noscript>';

private $repoid = 0;
private $html_github = '<div id="%s"></div><noscript>%s</noscript>';
private $js_github = array();

function __construct()
{
    add_action('plugins_loaded', array(&$this, 'plugins_loaded'));
}

public function plugins_loaded()
{
    load_plugin_textdomain(
        'oembed-gist',
        false,
        dirname(plugin_basename(__FILE__)).'/languages'
    );

    wp_embed_register_handler(
        'gist',
        '#https://gist.github.com/([a-zA-Z0-9]+)(\#file_(.+))?$#i',
        array(&$this, 'handler_gist')
    );
    add_shortcode('gist', array(&$this, 'shortcode_gist'));

    add_action( 'wp_print_scripts', array(&$this,'add_scripts') );
    wp_embed_register_handler(
        'github',
        '#https://github.com/([a-zA-Z0-9]+)/([^/]*)?$#i',
        array(&$this, 'handler_github')
    );
    add_shortcode('github', array(&$this, 'shortcode_github'));
}

public function handler_gist($m, $attr, $url, $rattr)
{
    if (!isset($m[2]) || !isset($m[3]) || !$m[3]) {
        $m[3] = null;
    }
    return '[gist id="'.$m[1].'" file="'.$m[3].'"]';
}

public function shortcode_gist($p)
{
    if (preg_match("/^[a-zA-Z0-9]+$/", $p['id'])) {
        $noscript = sprintf(
            __('<p>View the code on <a href="https://gist.github.com/%s">Gist</a>.</p>', 'oembed-gist'),
            $p['id']
        );
        if ($p['file']) {
            return sprintf($this->html_gist, $p['id'], '?file='.$p['file'], $noscript);
        } else {
            return sprintf($this->html_gist, $p['id'], '', $noscript);
        }
    }
}

public function handler_github($m, $attr, $url, $rattr)
{
    if (!isset($m[2]) || !isset($m[3]) || !$m[3]) {
        $m[3] = null;
    }
    return '[github id="'.$m[1].'" repo="'.$m[2].'"]';
}

public function shortcode_github($p)
{
    if (preg_match("/^[a-zA-Z0-9]+$/", $p['id'])) {
        $noscript = sprintf(
            __('<p>View the repo on <a href="https://github.com/%s">Gist</a>.</p>', 'oembed-gist'),
            $p['id']
        );
        $js = '$("#%s").repo({ user: "%s", name: "%s" });';

        $repoid = sprintf('github-repo-%d', $this->repoid);
        $html = sprintf($this->html_github, $repoid, $noscript);
        $this->js_github[] = sprintf($js, $repoid, $p['id'], (isset($p['repo']) ? $p['repo'] : ''));
        $this->repoid++;

		add_action('wp_footer', array(&$this, 'footer_github'));

        return $html;
    }
}

public function add_scripts() {
    wp_enqueue_script( 'jquery' );
}

public function footer_github(){
    if (count($this->js_github) > 0) {
        printf ('<script type="text/javascript" src="%s/repo.min.js"></script>', plugins_url('js', __FILE__));
        echo "<script>\n";
        echo "jQuery(function($){\n";
        echo implode("\n",$this->js_github);
        echo "\n});\n";
        echo "</script>\n";
    }
}

}


// EOF
