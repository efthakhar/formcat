<?php

namespace FormCat\Core;

use FormCat\Traits\Singleton;
use Illuminate\Database\Capsule\Manager as DB;
use WPCF7_ContactForm;

/*
 * This Class is responsible for Handling Form Data
 */

class FormDataHandler {
	use Singleton;
	/*
	 * Initialize FormDataHandler Class
	 */
	public function __construct() {
		// add_action('formcat_activate', [ $this, 'sync_exsisting_cf7forms' ] );
		// add_filter('parse_request', [ $this, 'sync_exsisting_cf7forms' ] );
		add_action('wpcf7_after_save', [ $this, 'handle_cf7form_save' ] );
		add_action('wpcf7_before_send_mail', [ $this, 'handle_cf7form_submission'] );
		add_action('delete_post', [$this, 'cf7_form_delete'], 10,2);
	}

	public function sync_exsisting_cf7forms() {
		$contact_forms = WPCF7_ContactForm::find();

		foreach ($contact_forms as $cf7_form) {
			$cf7_form->save();
		}
	}

	public function cf7_form_delete($id,$post) {
		if (get_post_type($id) === 'wpcf7_contact_form') {
			global $wpdb;
			$form = DB::table($wpdb->formcat_forms)->where('plugin_form_id', $id)->first();

			$submissions = DB::table($wpdb->formcat_submissions)->where('form_id', $form->id)->pluck('id');
			DB::table($wpdb->formcat_entries)->whereIn('submission_id', $submissions)->delete();
			DB::table($wpdb->formcat_submissions)->where('form_id', $form->id)->delete();
			DB::table($wpdb->formcat_forms)->where('id', $form->id)->delete();
		}
	}

	public function handle_cf7form_save($cf7_form) {
		global $wpdb;

		$form_fields_infos = [];
		$cf7_form_fields   = $cf7_form->scan_form_tags();

		if ($cf7_form) {
			foreach ($cf7_form_fields as $field) {
				array_push($form_fields_infos, [
					'name' => $field->name,
					'type' => $field->type,
				]);
			}
		}

		$previusly_saved_form = DB::table($wpdb->formcat_forms)
			->where('plugin_form_id', $cf7_form->id)
			->where('plugin_name', 'cf7', )->first();

		// echo '<pre>';
		// var_dump($duplicate_free_fields);
		// echo '</pre>';
		// wp_die();

		if ($previusly_saved_form) {
			$previusly_saved_form_fields    = json_decode($previusly_saved_form->fields, true);
			$duplicate_free_new_form_fields = array_unique(array_merge($form_fields_infos, $previusly_saved_form_fields), SORT_REGULAR);

			DB::table($wpdb->formcat_forms)
				->where('plugin_form_id', $cf7_form->id)
				->where('plugin_name', 'cf7', )
				->update([
					'fields'         => json_encode($duplicate_free_new_form_fields),
					'plugin_name'    => 'cf7',
					'form_name'      => $cf7_form->title(),
					'plugin_form_id' => $cf7_form->id,
				]);
		} else {
			$visible_fields = [];
			$fields_alias   = [];

			foreach ($cf7_form_fields as $field) {
				array_push($visible_fields, $field->name);
				array_push($fields_alias, [ $field->name => $field->name ] );
			}
			DB::table($wpdb->formcat_forms)->insert([
				'fields'                      => json_encode($form_fields_infos),
				'plugin_name'                 => 'cf7',
				'form_name'                   => $cf7_form->title(),
				'plugin_form_id'              => $cf7_form->id,
				'fields_visible_in_datatable' => json_encode($visible_fields),
				'fields_alias'                => json_encode($fields_alias),
			]);
		}
	}

	public function handle_cf7form_submission($form) {
		global $wpdb;
		$entries = [];

		$form_details = DB::table($wpdb->formcat_forms)
			->where('plugin_form_id', $form->id)
			->where('plugin_name', 'cf7', )->first();

		$form_submission_id = DB::table($wpdb->formcat_submissions)->insertGetId([
			'form_id' => $form_details->id,
		]);

		$form_fields = json_decode($form_details->fields);

		foreach ($form_fields as $field) {
			if ('file' != $field->type && 'submit' != $field->type && $_REQUEST[$field->name] ) {
				array_push($entries, [
					'submission_id' => $form_submission_id,
					'field'         => $field->name,
					'value'         => $_REQUEST[$field->name],
				]);
			}
		}

		DB::table($wpdb->formcat_entries)->insert($entries);
	}
}
