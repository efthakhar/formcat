<?php

namespace Formcat\Api;

use Illuminate\Database\Capsule\Manager as DB;

class Forms {
	public function __construct() {
		add_action( 'rest_api_init', [$this, 'formcat_forms_routes'] );
	}

	public function formcat_forms_routes() {
		register_rest_route( 'formcat/v1', '/forms/', [
			'methods'  => 'GET',
			'callback' => [$this, 'get_forms'],
		]);
	}

	public function get_forms( $request ) {
		global $wpdb;
		$forms = DB::table($wpdb->formcat_forms)
			->paginate( $request->get_param('perpage') ?? 10, ['*'], 'page', $request->get_param('page'));

		return rest_ensure_response( $forms );
	}
}
