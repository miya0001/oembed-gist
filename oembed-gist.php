<?php
/*
Plugin Name: oEmbed Gist
Plugin URI: http://firegoby.jp/wp/oembed-gist
Description: Embed source from gist.github.
Author: Takayuki Miyauchi
Version: 1.4.0
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
private $html = '<script src="https://gist.github.com/%s.js%s"></script><noscript>%s</noscript>';

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
        '#https://gist.github.com/([^\/]+\/)?([a-zA-Z0-9]+)(\#file(\-|_)(.+))?$#i',
        array(&$this, 'handler')
    );
    add_shortcode('gist', array(&$this, 'shortcode'));
}

public function handler($m, $attr, $url, $rattr)
{
    if (!isset($m[3]) || !isset($m[5]) || !$m[5]) {
        $m[5] = null;
    }
    return '[gist id="'.$m[2].'" file="'.$m[5].'"]';
}

public function shortcode($p)
{
    if (preg_match("/^[a-zA-Z0-9]+$/", $p['id'])) {
        $noscript = sprintf(
            __('<p>View the code on <a href="https://gist.github.com/%s">Gist</a>.</p>', 'oembed-gist'),
            $p['id']
        );
        if ($p['file']) {
            $file = preg_replace('/[\-\.]([a-z]+)$/', '.\1', $p['file']);
            return sprintf($this->html, $p['id'], '?file='.$file, $noscript);
        } else {
            return sprintf($this->html, $p['id'], '', $noscript);
        }
    }
}

}


// EOF
