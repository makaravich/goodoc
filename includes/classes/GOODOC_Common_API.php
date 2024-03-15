<?php

namespace GOODOC_Classes;


class GOODOC_Common_API {
	/**
	 * Calls any url with parameters and headers
	 *
	 * @param string $url
	 * @param array $params
	 * @param array $headers
	 * @param string $protocol
	 *
	 * @return bool|string
	 */
	public function call_abstract_url( string $url, array $params = [], array $headers = [], string $protocol = 'get' ): bool|string {

		if ( $protocol == trim( strtolower( 'get' ) ) && ! empty( $params ) ) {
			$url = $url . '?' . http_build_query( $params );
		}

		$ch = curl_init( trim( $url ) );

		# Setup request to send json via POST.
		if ( $protocol == trim( strtolower( 'post' ) ) ) {
			$params_json = json_encode( $params );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $params_json );
			curl_setopt( $ch, CURLOPT_POST, 1 );
		} elseif ( trim( strtolower( 'get' ) ) ) {
			curl_setopt( $ch, CURLOPT_HTTPGET, true );
		}

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		# Return response instead of printing.
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		if ( wp_get_environment_type() == 'local' ) {
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		}

		# Send request.
		$response = curl_exec( $ch );

		if ( curl_errno( $ch ) ) {
			return "Error: " . curl_error( $ch );
		}

		curl_close( $ch );

		return $response;
	}

	/**
	 * Returns the URL to call the API with a relative path
	 *
	 * @param $api_relative_path
	 *
	 * @return string
	 */
	protected function get_full_api_url( $api_relative_path ): string {
		while ( substr( $api_relative_path, 0, 1 ) == '//' ) {
			$api_relative_path = substr( $api_relative_path, 1 );
		}

		return trailingslashit( trailingslashit( GOODOC_API_URL ) . $api_relative_path );
	}

	/**
	 * Prepares an array with the message about the unfilled token field
	 *
	 * @return array
	 */
	private function get_no_token_err(): array {
		return [
			'success' => false,
			'code'    => 'token_excepts',
			'error'   => sprintf( __( 'There is no token in the plugin options. Go to the <a href="%s">Options page</a> and fill it.', 'goodoc' ),
				get_admin_url( null, 'admin.php?page=goodoc_opt-options' )
			),
		];
	}

	/**
	 * Calls the API. Can accept relative address, without the full path
	 *
	 * @param string $url
	 * @param array $params
	 * @param array $headers
	 * @param string $protocol
	 *
	 * @return bool|string
	 */
	public function call_the_api( string $url, array $params = [], array $headers = [], string $protocol = 'get' ): bool|string {
		global $goodoc_options;
		$users_token = $goodoc_options->get_option( 'opt_google_api_client_token' );

		if ( ! empty( $users_token ) ) {
			$headers = array_merge( $headers, [ 'Authorization: Bearer ' . $users_token ] );
		} else {
			return json_encode( $this->get_no_token_err() );
		}

		if ( ! ( str_starts_with( $url, 'http://' ) || str_starts_with( $url, 'https://' ) ) ) {
			$url = $this->get_full_api_url( $url );
		}

		return $this->call_abstract_url( $url, $params, $headers, $protocol );
	}
}