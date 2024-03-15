<?php

namespace GOODOC_Classes;

class GOODOC_Settings {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_custom_menu_page' ] );

		add_action( 'init', [ $this, 'add_options' ] );
	}


	function register_custom_menu_page(): void {
		$import_page = new GOODOC_Import_Page();

		add_menu_page(
			__( 'GooDoc Import', 'goodoc' ),
			__( 'GooDoc Import', 'goodoc' ),
			'edit_others_posts',
			'goodoc-import',
			[ $import_page, 'render_doc_import_page' ],
			'dashicons-welcome-add-page',
			21 );
	}

	/**
	 * Add the plugin settings page
	 *
	 * @return void
	 */
	public function add_options(): void {
		$settings_model = [
			'id'          => 'goodoc_opt',
			'location'    => 'goodoc-import',
			'page_title'  => __( 'GooDoc Options', 'goodoc' ),
			//It is the title, will appear as header of the options page
			'menu_title'  => __( 'GooDoc Options', 'goodoc' ),
			//It will appear in the admin menu
			'save_button' => __( 'Save', 'goodoc' ),
			//It is the caption for the "Save" button
			'groups'      => [
				'opt' => [
					'sections' => [
						'google_api' => [
							'title'  => __( 'GooDoc API options', 'goodoc' ),
							'fields' => [
								'client_token' => [
									'title' => __( 'GooDoc Client Token', 'goodoc' ),
									'type'  => 'password',
								],//Can add other fields here
							],
						],//Can add other sections here
					],
				],//Can add other groups here
			],
		];

		global $goodoc_options;
		$goodoc_options = new GOODOC_Custom_Options( $settings_model );
	}
}

new GOODOC_Settings();