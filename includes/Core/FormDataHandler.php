<?php

namespace FormCat\Core;

use FormCat\Traits\Singleton;
use Illuminate\Database\Capsule\Manager as DB;
use WPCF7_ContactForm;
use WPCF7_Submission;

/*
 * This Class is responsible for Handling Form Data
 */

class FormDataHandler {
	use Singleton;
	/*
	 * Initialize FormDataHandler Class
	 */
	public function __construct() {
		add_action('wpcf7_after_save', [ $this, 'handle_cf7form_save' ] );
		add_action('wpcf7_before_send_mail', [ $this, 'handle_cf7form_submission' ] );
	}

	public function handle_cf7form_save($cf7) {
		global $wpdb;

		$cf7_form = WPCF7_ContactForm::get_instance($cf7->id);

		$form_fields_infos = [];
		$cf7_form_fields            = $cf7_form->scan_form_tags();

		if ($cf7_form) {
			// Loop through each form field
			foreach ($cf7_form_fields as $field) {
				array_push($form_fields_infos, [
					'name' => $field->name,
					'type' => $field->type,
				]);
			}
		}

		DB::table($wpdb->formcat_forms)->updateOrInsert(['plugin_form_id' => $cf7->id], [
			'fields'         => json_encode($form_fields_infos),
			'plugin_name'    => 'cf7',
			'form_name'      => $cf7->title(),
			'plugin_form_id' => $cf7->id,
		]);

		// if (DB::table($wpdb->formcat_forms)->where('plugin_form_id', $cf7->id)->count() > 0) {
		// 	DB::table($wpdb->formcat_forms)
		// 		->where('plugin_form_id', $cf7->id)
		// 		->update([
		// 			'fields'         => json_encode($form_fields_infos),
		// 			'plugin_name'    => 'cf7',
		// 			'form_name'      => $cf7->title(),
		// 			'plugin_form_id' => $cf7->id,
		// 		]);
		// } else {
		// 	DB::table($wpdb->formcat_forms)->insert([
		// 		'fields'         => json_encode($form_fields_infos),
		// 		'plugin_name'    => 'cf7',
		// 		'form_name'      => $cf7->title(),
		// 		'plugin_form_id' => $cf7->id,
		// 	]);
		// }
	}

	public function handle_cf7form_submission($contact_form) {
		global $wpdb;

		$submission        = WPCF7_Submission::get_instance();
		$submitted_data    = $submission->get_posted_data();
		$current_form      = $submission->get_contact_form();
		$current_form_id   = $current_form->id();
		$current_form_name = $current_form->title();

		// DB::table($wpdb->formcat_submissions)->insert([
		// 	'form_name' => $current_form_name,
		// 	'form_data' => json_encode($submitted_data),
		// ]);
	}
}
