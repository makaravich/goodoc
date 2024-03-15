<?php

namespace GOODOC_Classes;
/**
 * Class to initialize the plugin
 */
class GOODOC_Init {
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_assets' ] );
	}

	public function load_admin_assets(): void {
		wp_enqueue_style( 'goodoc', GOODOC_PLUGIN_URL . 'assets/styles/goodoc-admin.css', [], GOODOC_VERSION );
		wp_enqueue_script( 'goodoc', GOODOC_PLUGIN_URL . 'assets/js/goodoc-admin.js', [ 'jquery' ], GOODOC_VERSION, true );
	}
}