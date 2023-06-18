<?php

namespace FormCat\Core;

use FormCat\Traits\Singleton;
use Illuminate\Database\Capsule\Manager as DB;
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
		add_action('wpcf7_before_send_mail', [ $this, 'handle_cf7form_submission' ] );
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
