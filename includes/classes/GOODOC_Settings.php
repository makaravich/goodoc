<?php

namespace GOODOC_Classes;

class GOODOC_Settings {

	private string $plugin_menu_slug = 'goodoc-import';
	private string $options_name = 'goodoc-options';

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_custom_menu_page' ] );

		add_action( 'admin_menu', [ $this, 'add_plugin_options_page' ] );

		add_action( 'admin_init', [ $this, 'register_plugin_settings' ] );
	}


	function register_custom_menu_page(): void {
		$import_page = new GOODOC_Import_Page();

		add_menu_page(
			__( 'GooDoc Import', 'goodoc' ),
			__( 'GooDoc Import', 'goodoc' ),
			'edit_others_posts',
			$this->plugin_menu_slug,
			[ $import_page, 'render_doc_import_page' ],
			'dashicons-welcome-add-page',
			21 );
	}

	/**
	 * Adds the plugin options page
	 *
	 * @return void
	 */
	public function add_plugin_options_page(): void {
		add_submenu_page(
			$this->plugin_menu_slug,
			__( 'GooDoc Options', 'goodoc' ),
			__( 'GooDoc Options', 'goodoc' ),
			'manage_options',
			$this->options_name,
			[ $this, 'options_page_output' ]
		);
	}

	/**
	 * Renders the option page
	 *
	 * @return void
	 */
	public function options_page_output(): void {
		?>
        <div class="wrap <?php echo $this->options_name ?>-wrapper">
			<?php settings_errors( $this->options_name ); ?>
            <h2><?php echo get_admin_page_title() ?></h2>

            <form action="options.php" method="POST">
				<?php
				settings_fields( $this->options_name ); // hidden protection fields
				do_settings_sections( $this->options_name ); // Sections with options. We have only single 'woi_section_general'
				submit_button( __( 'Save', 'goodoc' ) );
				?>
            </form>
        </div>
		<?php
	}

	/**
	 * Register page, section and fields of the plugin settings
	 *
	 * @return void
	 */
	public function register_plugin_settings(): void {
		register_setting(
			$this->options_name,
			$this->options_name,
			[
				'sanitize_callback' => [ $this, 'validate_input' ],
				'default'           => null,
				'type'              => 'string',
				'show_in_rest'      => false,
			] );

		add_settings_section(
			'api_options',
			__( 'API Options', 'goodoc' ),
			[ $this, 'render_settings_section' ],
			$this->options_name
		);

		add_settings_field(
			'api_token',
			__( 'GooDoc Client Token', 'goodoc' ),
			[ $this, 'render_password_field' ],
			$this->options_name,
			'api_options',
			[
				'id'          => 'api_token',
				'description' => sprintf( __( 'Need a Token? Authorize on the <a href="%s"  target="_blank">plugin\'s server</a> or learn <a href="%s" target="_blank">this article</a> for more information', 'goodoc' ),
					GOODOC_SERVER_URL . '/login/',
					GOODOC_SERVER_URL . '/quick-start/',
				),
			]
		);
	}

	/**
	 * Renders Options page section
	 *
	 * @return void
	 */
	function render_settings_section(): void {
		//echo "<p>A section description.</p>";
	}

	/**
	 * Renders a password field on the Options page
	 *
	 * @param $field
	 *
	 * @return void
	 */
	function render_password_field( $field ): void {
		$field_id = $field['id'] ?? false;

		if ( ! $field_id ) {
			return;
		}

		$value = $this->get_option( $field_id );

		printf( '<input class="goodoc-input-field" id="%s" name="%s[%s]" type="password" value="%s" />',
			'goodoc_' . $field_id,
			$this->options_name,
			$field_id,
			$value,
		);
		$description = $field['description'] ?? false;
		if ( $description ) {
			printf( '<p class="goodoc-description">%s</p>', $description );
		}
	}

	/**
	 * Checks options before they will be saved
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function validate_input( $input ): mixed {
		$input['api_token'] = sanitize_text_field( $input['api_token'] );

		// Validate api_token field
		if ( ! preg_match( '/^[a-f0-9]{32}$/', $input['api_token'] ) ) {
			add_settings_error( $this->options_name, esc_attr( 'settings_updated' ), __( 'Invalid GooDoc Client Token' ), 'error' );
			$input['api_token'] = $this->get_option( 'api_token' );
		}

		return $input;
	}

	/**
	 * Returns a plugin option by its name
	 *
	 * @param string $option_name
	 *
	 * @return string|null
	 */
	public function get_option( string $option_name ): ?string {
		$options = get_option( $this->options_name, [] );

		if ( ! empty( $options ) && isset( $options[ $option_name ] ) ) {
			$value = esc_attr( $options[ $option_name ] );
		} else {
			$value = '';
		}

		return $value;
	}
}

global $goodoc_settings;

$goodoc_settings = new GOODOC_Settings();