<?php

namespace GOODOC_Classes;
require_once GOODOC_PLUGIN_DIR . 'includes/third-party/voku/vendor/autoload.php';

use voku\helper\HtmlDomParser;

class GOODOC_Import extends GOODOC_Common_API {

	use GOODOC_Logger;

	/**
	 * Imports a single document from Google Documents into WordPress
	 *
	 * @param $params
	 *
	 * @return array
	 */
	public function import_doc( $params ): array {
		$respond = $this->call_the_api( 'html', $params );

		$arr = [];

		@$arr = json_decode( $respond, true );
		if ( ! empty( $arr ) && isset( $arr['content'] ) ) {

			$content = $this->import_images( $arr['content'] );

			global $user_ID;
			$new_post = array(
				'post_title'   => $params['title'],
				'post_content' => $content,
				'post_status'  => 'draft',
				'post_author'  => $user_ID,
				'post_type'    => 'post',
			);
			$post_id  = wp_insert_post( $new_post );

			return [ 'success' => 1, 'postId' => $post_id, 'editUrl' => get_edit_post_link( $post_id ) ];

		} else {

			return [ 'success' => 0, 'message' => __( 'There was an error on server response' ) ];
		}
	}

	/**
	 * Uploads all the images from the document into WordPress and replaces their URLs
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	private function import_images( string $content ): string {

		$dom = HtmlDomParser::str_get_html( $content );

		foreach ( $dom->find( 'img' ) as $e ) {
			$src = $e->getAttribute( 'src' );

			$new_image_url = ! empty( $src ) ? $this->upload_image( $src ) : false;

			if ( $new_image_url ) {
				$e->setAttribute( 'src', $new_image_url );
			}
		}

		return $dom;
	}

	/**
	 * Returns file extension by the file content
	 *
	 * @param string $url
	 *
	 * @return bool|string
	 */
	function get_file_type_by_content( string $url ): bool|string {
		$types = [
			IMAGETYPE_GIF     => 'gif',
			IMAGETYPE_JPEG    => 'jpg',
			IMAGETYPE_PNG     => 'png',
			IMAGETYPE_SWF     => 'swf',
			IMAGETYPE_PSD     => 'psd',
			IMAGETYPE_BMP     => 'bmp',
			IMAGETYPE_TIFF_II => 'tiff',
			IMAGETYPE_TIFF_MM => 'tiff',
			IMAGETYPE_JPC     => 'jpc',
			IMAGETYPE_JP2     => 'jp2',
			IMAGETYPE_JPX     => 'jpx',
			IMAGETYPE_JB2     => 'jb2',
			IMAGETYPE_SWC     => 'svc',
			IMAGETYPE_IFF     => 'iff',
			IMAGETYPE_WBMP    => 'wbmp',
			IMAGETYPE_XBM     => 'xbm',
			IMAGETYPE_ICO     => 'ico',
			IMAGETYPE_WEBP    => 'webp',
			IMAGETYPE_AVIF    => 'avif',
		];

		$type = exif_imagetype( $url );
		$this->debug_log( $url . ' -> ' . $type );

		return ! empty( $types[ $type ] ) ? $types[ $type ] : '';
	}

	/**
	 * Uploads remote image from a Google Document into WordPress
	 *
	 * @param $image_url
	 *
	 * @return bool|string
	 */
	function upload_image( $image_url ): bool|string {
		$image = false;

		// Upload the image and get the attachment ID
		$upload_dir = wp_upload_dir();
		$filename   = basename( $image_url );
		$image_data = file_get_contents( $image_url );

		if ( is_wp_error( $image_data ) ) {
			$this->warning_log( 'Cannot read the image ' . $filename );
		}

		// Add the file type
		$file_type = $this->get_file_type_by_content( $image_url );

		if ( $file_type ) {
			$filename .= '.' . $file_type;
		}

		// Make filepath for WP
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		$file = wp_normalize_path( $file );

		// Save the file
		file_put_contents( $file, $image_data );

		// Add the file into WP Media Library
		$wp_filetype   = wp_check_filetype( $filename, null );
		$attachment    = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		$attachment_id = wp_insert_attachment( $attachment, $file );

		if ( ! is_wp_error( $attachment_id ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );
			// Set new image URL to be returned
			$image = wp_get_attachment_image_url( $attachment_id, 'full' );
		} else {
			$this->error_log( 'Error uploading image: ' . $attachment_id->get_error_message() );
		}

		return $image;

	}
}