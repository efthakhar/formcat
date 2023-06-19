<?php
/**
 * Plugin Name: Form Cat
 * Plugin URI:
 * Description: Form Storage Plugin for Wordpress
 * Author: Efthakhar Bin Alam
 * Author URI: https://github.com/efthakhar
 * Version: 1.0.0
 * Text Domain: dpos
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

use FormCat\Assets;
use FormCat\Core\FormDataHandler;
use FormCat\Database\CreateTables;
use FormCat\Pages;
use FormCat\Traits\Singleton;

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

class FormCat {
	use Singleton;

	public function __construct() {
		$this->define_constants();
		$this->configure_illuminate_database();
		$this->wpdb_table_shortcuts();

		
		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );
		
		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
		
	}

	public function define_constants() {
		define('FORMCAT', __FILE__ );
		define('FORMCAT_DIR', plugin_dir_url(__FILE__));
	}

	public function wpdb_table_shortcuts() {
		global $wpdb;
		$wpdb->formcat_forms = $wpdb->prefix . 'formcat_forms';
		$wpdb->formcat_submissions = $wpdb->prefix . 'formcat_submissions';
		$wpdb->formcat_entries = $wpdb->prefix . 'formcat_entries';
	}

	public function activate() {
		new CreateTables();

		do_action( 'formcat_activate');
	}

	public function init_plugin() {
		$this->includes();
		do_action( 'formcat_loaded' );
		$this->init_classes();
		// $this->init_hooks();
	}

	public function includes() {
	}

	public function init_classes() {
		Pages::instance();
		Assets::instance();
		FormDataHandler::instance();
	}

	/*
	 * Init the illuminate Database
	 */
	public function configure_illuminate_database() {
		$capsule = new \Illuminate\Database\Capsule\Manager();

		$capsule->addConnection([
			'driver'    => 'mysql',
			'host'      => DB_HOST,
			'database'  => DB_NAME,
			'username'  => DB_USER,
			'password'  => DB_PASSWORD,
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		]);

		// $capsule->setEventDispatcher(new \Illuminate\Contracts\Events\Dispatcher(new \Illuminate\Container\Container));
		$capsule->setAsGlobal();
		$capsule->bootEloquent();
	}

	public function deactivate() {
		$this->remove_database_tables();
	}

	public function remove_database_tables() {
		global $wpdb;
		$tableArray = [
			$wpdb->formcat_forms,
			$wpdb->formcat_submissions,
			$wpdb->formcat_entries
		];

		foreach ($tableArray as $tablename) {
			$wpdb->query("DROP TABLE IF EXISTS {$tablename}");
		}
	}
}

FormCat::instance();
