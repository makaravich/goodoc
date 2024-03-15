<?php

namespace GOODOC_Classes;

/**
 * Writes errors and other info into the WordPress log file (normally /wp-content/debug.log)
 */

trait GOODOC_Logger {

	/**
	 * Writes single message
	 *
	 * @param $single_log
	 * @param $state
	 *
	 * @return void
	 */
	private function write_single_log( $single_log, $state = '' ): void {
		$state = ! empty( $state ) ? $state . ': ' : '';

		if ( is_array( $single_log ) || is_object( $single_log ) ) {
			error_log( $state . print_r( $single_log, true ) );
		} else {
			error_log( $state . $single_log );
		}
	}

	/**
	 * Checks if debugging is on and tries to write a single log if got a string
	 * and explode an array elsewhere
	 *
	 * @param $state
	 * @param $log
	 *
	 * @return void
	 */
	private function common_log( $state, $log ): void {
		if ( true === WP_DEBUG ) {
			if ( count( $log ) > 1 ) {
				foreach ( $log as $single_log ) {
					$this->write_single_log( $single_log, $state );
				}
			} else {
				$this->write_single_log( $log[0], $state );
			}
		}
	}

	public function debug_log( ...$log ): void {
		$this->common_log( GOODOC_NAME . ' Plugin DEBUG', $log );
	}

	public function error_log( ...$log ): void {
		$this->common_log( GOODOC_NAME . ' Plugin ERROR', $log );
	}

	public function warning_log( ...$log ): void {
		$this->common_log( GOODOC_NAME . ' Plugin WARNING', $log );
	}

	public function info_log( ...$log ): void {
		$this->common_log( GOODOC_NAME . ' Plugin INFO', $log );
	}
}