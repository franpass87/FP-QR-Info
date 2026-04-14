<?php
/**
 * Plugin Name: FP QR Info
 * Plugin URI: https://github.com/franpass87/FP-QR-Info
 * Description: Landing standalone per QR code con contenuti IT/EN su smaltimento, nutrizionali e ingredienti.
 * Version: 0.1.8
 * Author: Francesco Passeri
 * Author URI: https://francescopasseri.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fp-qr-info
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('FP_QR_INFO_VERSION', '0.1.8');
define('FP_QR_INFO_FILE', __FILE__);
define('FP_QR_INFO_DIR', plugin_dir_path(__FILE__));
define('FP_QR_INFO_URL', plugin_dir_url(__FILE__));

$autoloadPath = FP_QR_INFO_DIR . 'vendor/autoload.php';
if (is_readable($autoloadPath)) {
    require_once $autoloadPath;
}

if (!class_exists(\FP\QrInfo\Core\Plugin::class)) {
    return;
}

register_activation_hook(
    FP_QR_INFO_FILE,
    static function (): void {
        \FP\QrInfo\Core\Plugin::activate();
    }
);

register_deactivation_hook(
    FP_QR_INFO_FILE,
    static function (): void {
        \FP\QrInfo\Core\Plugin::deactivate();
    }
);

add_action(
    'plugins_loaded',
    static function (): void {
        load_plugin_textdomain('fp-qr-info', false, dirname(plugin_basename(FP_QR_INFO_FILE)) . '/languages');
        (new \FP\QrInfo\Core\Plugin())->register();
    }
);
