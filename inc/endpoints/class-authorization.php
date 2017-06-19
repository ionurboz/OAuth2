<?php

namespace WP\OAuth2\Endpoints;

use WP\OAuth2\Client;
use WP\OAuth2\Types;

class Authorization {
	const LOGIN_ACTION = 'oauth2_authorize';

	/**
	 * Register required actions and filters
	 */
	public function register_hooks() {
		add_action( 'login_form_' . static::LOGIN_ACTION, array( $this, 'handle_request' ) );
		add_action( 'oauth2_authorize_form', array( $this, 'render_page_fields' ) );
	}

	public function handle_request() {
		// If the form hasn't been submitted, show it.
		$type = wp_unslash( $_GET['response_type'] );

		switch ( $type ) {
			case 'code':
				$handler = new Types\Authorization_Code();
				break;

			case 'token':
				$handler = new Types\Implicit();
				break;

			default:
				return new WP_Error(
					'oauth2.endpoints.authorization.handle_request.invalid_type',
					__( 'Invalid response type specified.', 'oauth2' )
				);
		}

		$result = $handler->handle_authorisation();
		if ( is_wp_error( $result ) ) {
			// TODO: Handle it.
			wp_die( $result->get_error_message() );
		}
	}

	public function render_page_fields() {
		wp_nonce_field( 'json_oauth2_authorize' );
	}
}
