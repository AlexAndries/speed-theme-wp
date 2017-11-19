<?php

namespace SpeedTheme;

use SpeedTheme\Admin\Exporting\Settings;
use SpeedTheme\Core\ST_Core;
use SpeedTheme\WpJson\Core as WpJsonCore;

define('THEME_URL', get_bloginfo('stylesheet_directory') . '/');
define('THEME_URL_BOWER', THEME_URL . 'bower_components/');
define('SITE_URL', home_url('/'));
define('THEME_PATH', __DIR__ . '/');
define('PATH_TO_ICONS', THEME_PATH . 'images/icons/');
define('IMAGE_PLACEHOLDER_SRC', THEME_URL . 'images/angular-place-holder-image.png');

if (file_exists(__DIR__ . '/credentials/st-env.php')) {
  require_once('credentials/st-env.php');
}

if (file_exists(__DIR__ . '/credentials/st-access.php')) {
  require_once('credentials/st-access.php');
}
if (file_exists(__DIR__ . '/credentials/st-version.php')) {
  require_once('credentials/st-version.php');
}

require_once('core/init-core.php');

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
  require_once('vendor/autoload.php');
}

if (file_exists(__DIR__ . '/includes/admin-theme/admin-theme.php')) {
  require_once('includes/admin-theme/admin-theme.php');
}

if (ST_Core::checkThemeDependencies()) {
  if (file_exists(__DIR__ . '/packages/load-packages.php')) {
    require_once('packages/load-packages.php');
  }

  /*if (file_exists(__DIR__ . '/api/load-api.php')) {
    require_once('api/load-api.php');
  }*/

  WpJsonCore::init();

  if (file_exists(__DIR__ . '/wp-json/load-rest.php')) {
    require_once(__DIR__ . '/wp-json/load-rest.php');
  }

  if (file_exists(__DIR__ . '/admin/load-admin.php')) {
    require_once(__DIR__ . '/admin/load-admin.php');
  }

  new Settings();
}