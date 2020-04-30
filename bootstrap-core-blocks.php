<?php
/*
Plugin Name: Bootstrap Core Blocks
Plugin URI: https://github.com/BramEsposito/bootstrap-core-blocks
Description: Make existing Gutenberg blocks compatible with Bootstrap
Version: 1.0
Author: Bram Esposito
Author URI: https://www.bramesposito.com
License: GPL-3.0+
Text Domain: bcb
Domain Path: /languages
*/

/**
 * Enqueue the plugin javascript 
 * @return [type] [description]
 */
function bcb_enqueue() {
  wp_register_script(
    'bcb-script',
    plugins_url( 'bootstrap-core-blocks.js', __FILE__ ),
    array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
    filemtime( plugin_dir_path( __FILE__ ) . '/bootstrap-core-blocks.js' )
  );
  wp_localize_script(
    'bcb-script',
    'bcb',
    get_option("bootstrap-core-blocks", ['posttypes' => []])
  );
  wp_enqueue_script( 'bcb-script' );
}

add_action( 'enqueue_block_editor_assets', 'bcb_enqueue' );

/**
 * Render a Bootstrap container, row and column around the block content
 */
add_filter( 'render_block', function( $block_content, $block ) {
  // Wrap block in bootstrap container, row and column classes
  if(isset($block['attrs']['bootstrapContainer']) && $block['attrs']['bootstrapContainer'] == "enabled") {
    // TODO: add a filter to this for customisation
    $block_content = "<div class='container'><div class='row'><div class='col-12'>".$block_content."</div></div></div>";
  }
  return $block_content;
}, 10, 2 );

require_once ("admin.php");
