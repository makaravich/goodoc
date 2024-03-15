<?php

namespace GOODOC_Classes;

class GOODOC_Ajax {
	private string $security_str = 'Google Doc into WordPress 2024';

	public function __construct() {
		//Add AJAX variables
		add_action( 'admin_enqueue_scripts', [ $this, 'setup_ajax_data' ], 12 );

		//Call for Load More button
		add_action( 'wp_ajax_import_google_document', [ $this, 'import_google_document' ] );
	}

	/**
	 * Add AJAX variables to the site code
	 */
	function setup_ajax_data(): void {
		$ajax_data = $this->get_ajax_data();
		wp_add_inline_script( 'goodoc', 'const GOODOC_AJAX = ' . json_encode( $ajax_data ), 'before' );
	}

	/**
	 * Contains data to transfer in JS
	 * @return array
	 */
	private function get_ajax_data(): array {
		return [
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( $this->security_str ),
		];
	}

	/**
	 * Processing AJAX call of import the document;
	 *
	 * @return void
	 */
	public function import_google_document(): void {
		//check_ajax_referer( $this->security_str, 'nonce' );

		$doc_id    = htmlspecialchars( $_POST['doc_id'] ) ?? false;
		$doc_title = htmlspecialchars( $_POST['doc_title'] ) ?? false;

		$params = [ 'file_id' => $doc_id, 'title' => $doc_title ];

		$importer = new GOODOC_Import();
		$res      = $importer->import_doc( $params );

		if ( isset( $res['success'] ) && $res['success'] ) {
			wp_send_json_success( $res );
		} else {
			wp_send_json_error( $res );
		}

		wp_die();
	}
}

new GOODOC_Ajax();