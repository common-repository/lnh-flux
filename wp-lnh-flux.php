<?php
/*
Plugin Name: LNH FLUX
Description: Plugin qui va permettre l'import de données à partir d'un flux proposé par la LNH.
Version:     1.0
Author:      Couleur Citron
Author URI:  http://www.couleur-citron.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: lnh-flux
*/

require __DIR__ . '/autoload.php';

$plugin = new Lnh\Plugin\Plugin();
$plugin->init();
$plugin->initShortcode();


/* init cron */
register_activation_hook(__FILE__, 'lnh_activation');

add_action('daily_import_LNH_flux', 'daily_import_LNH_flux_action');

function lnh_activation() {
	wp_schedule_event(time(), 'daily', 'daily_import_LNH_flux');
}

function daily_import_LNH_flux_action() {
	$plugin = new Lnh\Plugin\Plugin();
        $plugin->importPageAction(true);
}

register_deactivation_hook(__FILE__, 'lnh_deactivation');

function lnh_deactivation() {
	wp_clear_scheduled_hook('daily_import_LNH_flux');
}