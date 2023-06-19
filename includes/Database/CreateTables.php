<?php

namespace FormCat\Database;

class CreateTables {
	public function __construct() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$this->create_formcat_forms_table();
		$this->create_formcat_submissions_table();
		$this->create_formcat_entries_table();
	}

	private function prefix() {
		global $wpdb;

		return $wpdb->prefix;
	}

	private function create_formcat_forms_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}formcat_forms` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`plugin_name` varchar(100) NOT NULL,
			`plugin_form_id` bigint(20) unsigned NOT NULL,
			`form_name` varchar(255) NOT NULL,
			`fields` text,
			`fields_alias` text,
			`fields_visible_in_datatable` text,
			PRIMARY KEY (`id`)
		) {$charset_collate};";

		dbDelta( $sql );
	}

	private function create_formcat_submissions_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}formcat_submissions` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`date` datetime,
            -- `custom_data` longtext,
			PRIMARY KEY (`id`)
		) {$charset_collate};";

		dbDelta( $sql );
	}

	private function create_formcat_entries_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}formcat_entries` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`submission_id` bigint(20) unsigned NOT NULL,
			`field` varchar(100) NOT NULL,
			`value` varchar(100) NOT NULL,
			
			PRIMARY KEY (`id`)
		) {$charset_collate};";

		dbDelta( $sql );
	}

}
