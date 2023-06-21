<?php

namespace Formcat\Api;

use Illuminate\Database\Capsule\Manager as DB;
use WP_Error;

class Forms {
	public function __construct() {
		add_action( 'rest_api_init', [$this, 'formcat_forms_routes'] );
	}

	public function formcat_forms_routes() {
		register_rest_route( 'formcat/v1', '/forms/', [
			'methods'  => 'GET',
			'callback' => [$this, 'get_forms'],
			'permission_callback' => [ $this, 'get_forms_permissions_check' ],
		]);
	}

	public function get_forms( $request ) {
		global $wpdb;
		$forms = DB::table($wpdb->formcat_forms)
			->paginate( $request->get_param('perpage') ?? 10, ['*'], 'page', $request->get_param('page'));

		return rest_ensure_response( $forms );
	}

	public function get_forms_permissions_check( $request ) {
		if ( current_user_can( 'formcat_view_forms' ) ) {
			return true;
		}

		return new WP_Error( 'rest_forbidden', 'you cannot view forms', [ 'status' => 403 ] );
	}
}
