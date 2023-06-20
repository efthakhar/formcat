<?php

namespace Formcat\Api;

use Illuminate\Database\Capsule\Manager as DB;

class Submissions {
	public function __construct() {
		add_action( 'rest_api_init', [$this, 'formcat_submissions_routes'] );
	}

	public function formcat_submissions_routes() {
		register_rest_route( 'formcat/v1', '/submissions/', [
			'methods'  => 'GET',
			'callback' => [$this, 'get_submissions'],
		]);
	}

	public function get_submissions( $request ) {
		$form_id = $request->get_param('form_id');
		$page    = $request->get_param('page') ?? 1;
		$perpage = $request->get_param('perpage') ?? 10;
		global $wpdb;

		$form_details                = DB::table($wpdb->formcat_forms)->find($form_id);
		$fields_visible_in_datatable = json_decode($form_details->fields_visible_in_datatable);
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
		];

		return rest_ensure_response( $data);
	}
}
