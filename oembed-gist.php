<?php
/*
Plugin Name: oEmbed Gist
Plugin URI: http://firegoby.jp/wp/oembed-gist
Description: Embed source from gist.github.
Author: Takayuki Miyauchi
Version: 1.6.0
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

$oe_gist = new gist();
$oe_gist->register();

class gist {

private $shotcode_tag = 'gist';
private $noscript;
private $html = '<script src="https://gist.github.com/%s.js%s"></script><noscript>%s</noscript>';

function register()
{
    add_action('plugins_loaded', array(&$this, 'plugins_loaded'));
}

public function plugins_loaded()
{
    add_action('wp_head', array($this, 'wp_head'));

    load_plugin_textdomain(
        'oembed-gist',
        false,
        dirname(plugin_basename(__FILE__)).'/languages'
    );

    wp_embed_register_handler(
        'oe-gist',
        '#https://gist.github.com/([^\/]+\/)?([a-zA-Z0-9]+)(\#file(\-|_)(.+))?$#i',
        array(&$this, 'handler')
    );

    add_shortcode($this->get_shortcode_tag(), array($this, 'shortcode'));

    add_filter(
        'jetpack_shortcodes_to_include',
        array($this, 'jetpack_shortcodes_to_include')
    );
}

public function jetpack_shortcodes_to_include($incs)
{
    $includes = array();
    foreach ($incs as $inc) {
        if (!preg_match("/gist\.php\z/", $inc)) {
            $includes[] = $inc;
        }
    }
    return $includes;
}

public function wp_head()
{
    echo '<style>.gist table { margin-bottom: 0; }</style>';
}

public function handler($m, $attr, $url, $rattr)
{
    if (!isset($m[3]) || !isset($m[5]) || !$m[5]) {
        $m[5] = null;
    }
    return sprintf(
        '[%s id="%s" file="%s"]',
        $this->get_shortcode_tag(),
        esc_attr($m[2]),
        esc_attr($m[5])
    );
}

public function shortcode($p)
{
    if (preg_match("/^[a-zA-Z0-9]+$/", $p['id'])) {
        $noscript = sprintf(
            __('<p>View the code on <a href="https://gist.github.com/%s">Gist</a>.</p>', 'oembed-gist'),
            $p['id']
        );
        if (isset($p['file'])) { //RRD: Fixed line 79 error by adding isset()
            $file = preg_replace('/[\-\.]([a-z]+)$/', '.\1', $p['file']);
            return sprintf($this->html, $p['id'], '?file='.$file, $noscript);
        } else {
            return sprintf($this->html, $p['id'], '', $noscript);
        }
    }
}

private function get_shortcode_tag()
{
    return apply_filters('oembed_gist_shortcode_tag', $this->shotcode_tag);
}

}

// EOF
