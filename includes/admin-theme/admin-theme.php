<?php
define('THEME_ADMIN_DIR', __DIR__ . '/');
define('THEME_ADMIN_URL', THEME_URL . 'includes/admin-theme/');

require_once('classes/sass-compiler.php');
require_once('classes/custom-admin-core.php');

if (defined('DEV_ENV') && DEV_ENV && isset($_GET['compile'])) {
  $scss = new SassCompiler();
  $scss->compile();
}
$core = new CustomAdminCore();
