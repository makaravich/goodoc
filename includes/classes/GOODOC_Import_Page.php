<?php

namespace GOODOC_Classes;

require_once GOODOC_PLUGIN_DIR . 'includes/classes/GOODOC_Common_API.php';

class GOODOC_Import_Page extends GOODOC_Common_API {
	use GOODOC_Logger;

	/**
	 * Displays the page where a user can import Google Documents
	 *
	 * @return void
	 */
	public function render_doc_import_page(): void {
		$files = [];

		$response = $this->call_the_api( 'list' );
		@$response = json_decode( $response );

		//$this->debug_log( $response );
		?>
        <div class="goodoc-content">
            <h2><?php echo get_admin_page_title() ?></h2>

			<?php

			if ( is_object( $response ) && isset( $response->files ) ) {
				$files = $response->files;
				?>
                <div id="goodoc-files" class="goodoc-files">
					<?php
					if ( is_array( $files ) ) {
						foreach ( $files as $file ) {
							$this->render_single_file( $file );
						}
					}
					?>
                </div>
                <div id="goodoc-toolbar" class="goodoc-toolbar">
                    <button id="goodoc-start-import"
                            class="button button-primary goodoc-toolbar__btn-import"
                            disabled><?php _e( 'Start Import', 'goodoc' ) ?>
                    </button>
                    <span class="spinner" id="doc-import-spinner"></span>
                </div>

				<?php
			} elseif ( isset( $response->error ) ) {
				//$this->warning_log( 'There are errors from the GooDoc Server:' );
				//$this->error_log( $response->error );

				$msg = is_string( $response->error ) ? $response->error : __( 'There are errors in the respond from the GooDoc Server. Try logging in again on the login page.', 'goodoc' );

				printf( '<div id="message-%s" class="notice %s "><p><strong>%s:</strong> %s</p></div>',
					uniqid(),
					'notice-warning',
					GOODOC_NAME,
					$msg
				);
			}
			?>
        </div>
		<?php
	}

	/**
	 * Output a single item of Google Documents
	 *
	 * @param $file
	 *
	 * @return void
	 */
	private function render_single_file( $file ): void {
		?>
        <div class="goodoc-files__single-file" data-doc-id="<?php echo $file->id ?>"
             data-doc-title="<?php echo $file->name ?>">
            <img class="goodoc-files__single-file__logo"
                 src="<?php echo GOODOC_PLUGIN_URL ?>assets/img/google_gocs_logo.svg" width="47" height="65"
                 alt="Google document logo" title="<?php echo $file->name ?>">
            <p class="goodoc-files__single-file__name"><?php echo wp_trim_words( $file->name, 4 ) ?></p>
        </div>
		<?php
	}
}