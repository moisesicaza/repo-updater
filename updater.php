<?php
/**
 * Plugin Name: Repo Updater
 *
 * @version 1.0.0
 * @author Moises Icaza <icazamartinez@gmail.com>
 */

define( 'R_UPDATER_CONTEXT', 'r_updater' );
define( 'R_UPDATER_URL', plugin_dir_url(__FILE__) );
define( 'R_UPDATER_PATH', plugin_dir_path(__FILE__) );
define( 'R_UPDATER_SRC', R_UPDATER_PATH .'src' );
define( 'R_UPDATER_ASSETS_PATH', R_UPDATER_URL .'assets' );


require_once R_UPDATER_SRC .'/helpers.php';
require_once R_UPDATER_SRC .'/class-store.php';
require_once R_UPDATER_SRC .'/class-input.php';
require_once R_UPDATER_SRC .'/class-ui.php';

\RUpdater\SettingsPage::show();
