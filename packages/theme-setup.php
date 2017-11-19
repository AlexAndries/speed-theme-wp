<?php

use SpeedTheme\Core\ST_MenuRegisterLocations;
use SpeedTheme\Core\ST_MenusAdminWrapper;
use SpeedTheme\Core\ST_ThemeFunctions;
use SpeedTheme\Core\AssetsManager;

/**
 * Hide menu elements admin
 */
$adminMenu = new ST_MenusAdminWrapper();
$adminMenu->addSubMenuToHide('themes.php', 'theme-editor.php')
          ->addSubMenuToHide('themes.php', 'customize.php')
          ->addSubMenuToHide('plugins.php', 'plugin-editor.php')
          ->addMenuToHide('edit-comments.php')
          ->addMenuToHide('edit.php');

/**
 * Register Menus
 */
$menus = new ST_MenuRegisterLocations();
$menus->addMenu(array('primary-menu' => 'Main Menu'))
      ->addMenu(array('footer-menu' => 'Footer Menu'))
      ->registerMenus();

/**
 * Header scripts
 */
$headerAssetsManager = new AssetsManager();
$headerAssetsManager->addStyle('owl.carousel-css', 'bower_components/owl.carousel/dist/assets/owl.carousel.min.css')
                    ->addStyle('main-css', 'css/front-main.css')
                    ->build();

/**
 * Footer Scripts
 */
$footerAssetsManager = new AssetsManager(true);
$footerAssetsManager->addScript('youtube-api', 'https://www.youtube.com/iframe_api', true, true)
                    ->addScript('jquery', 'bower_components/jquery/dist/jquery.min.js')
                    ->addScript('owl.carousel', 'bower_components/owl.carousel/dist/owl.carousel.min.js')
                    ->addScript('hash-tag-handler', 'js/hash-tag-handler.js')
                    ->addScript('event-manager', 'js/event-listener.js')
                    ->addScript('screen-handler', 'js/screen-handler.js')
                    ->addScript('youtube-api-wrapper', 'js/youtube-api.js')
                    ->addScript('modal-handler', 'js/modal-handler.js')
                    ->addScript('toggle-class-handler', 'js/toggle-class-handler.js')
                    ->addScript('keyboard-control-js', 'js/keyboard-control.js')
                    ->addScript('ofi.browser', 'js/ofi.browser.js')
                    ->addScript('owl-slider-handler-js', 'js/owl-slider-handler.js')
                    ->addScript('fixed-header-handler-js', 'js/fixed-header-handler.js')
                    ->addScript('main-js', 'js/main.js');

$footerAssetsManager->addScript('angular', 'bower_components/angular/angular.min.js')
                    ->addScript('angular-resource', 'bower_components/angular-resource/angular-resource.min.js')
                    ->addScript('angular-sanitize', 'bower_components/angular-sanitize/angular-sanitize.js')
                    ->addScript('ng-upload-file', 'bower_components/ng-file-upload/ng-file-upload.min.js')
                    ->addScript('app', 'js/app/app.js')
                    ->addScript('form-entity', 'js/app/forms/form-entity.js')
                    ->addScript('form-controller', 'js/app/forms/form-controller.js')
                    ->addScript('submit-form-command', 'js/app/forms/submit-form-command.js')
                    ->build();
/**
 * Theme Support
 */
ST_ThemeFunctions::addThemeSupport('post-thumbnails');
ST_ThemeFunctions::addThemeSupport('menus');
