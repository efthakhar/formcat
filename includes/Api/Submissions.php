<?php

namespace Formcat\Api;

use Illuminate\Database\Capsule\Manager as DB;
use WP_Error;

class Submissions {
	public function __construct() {
		add_action( 'rest_api_init', [$this, 'formcat_submissions_routes'] );
	}

	public function formcat_submissions_routes() {
		register_rest_route( 'formcat/v1', '/submissions/', [
			'methods'             => 'GET',
			'callback'            => [$this, 'get_submissions'],
			'permission_callback' => [ $this, 'get_submissions_permissions_check' ],
		]);
	}

	public function get_submissions( $request ) {
		$form_id = $request->get_param('form_id');
		$page    = $request->get_param('page') ?? 1;
		$perpage = $request->get_param('perpage') ?? 10;
		global $wpdb;

		$form_details                = DB::table($wpdb->formcat_forms)->find($form_id);
		$fields_visible_in_datatable = NULL !== $form_details ? json_decode($form_details->fields_visible_in_datatable) : [];
		$fields_alias = NULL !== $form_details ? json_decode($form_details->fields_alias) : [];
		$submissions                 = DB::table($wpdb->formcat_submissions)
			->where('form_id', $form_id)
			->paginate( $perpage, 'id', 'page', $page);

		$submission_ids = [];

		foreach ($submissions->items() as  $item) {
			$submission_ids[] = $item->id;
		}

		$entries = DB::table($wpdb->formcat_entries)
			->whereIn('submission_id', $submission_ids)
			// ->when($fields_visible_in_datatable, function ($query, $fields_visible_in_datatable ) {
			// 	$query->whereIn('field', $fields_visible_in_datatable );
			// }
			// ,function ($query) {
			// 	$query->whereIn('field', [] );
			// }
			// )
			->orderBy('submission_id')
			->get();

		$data = [
			'current_page'                => $submissions->currentPage(),
			'last_page'                   => $submissions->lastPage(),
			'entries'                     => $entries,
			'submission_ids'              => $submission_ids,
			'fields_visible_in_datatable' => $fields_visible_in_datatable,
			'fields_alias' => $fields_alias,
		];

		return rest_ensure_response( $data);
	}

	public function get_submissions_permissions_check( $request ) {
		if ( current_user_can( 'formcat_view_submissions' ) ) {
			return true;
		}

		return new WP_Error( 'rest_forbidden', 'you cannot view forms', [ 'status' => 403 ] );
	}
}
