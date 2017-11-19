<?php

class CustomAdminCore {
  
  public function __construct() {
    add_action('admin_enqueue_scripts', array($this, 'registerResourcesAdmin'));
    add_action('login_enqueue_scripts', array($this, 'registerResourcesLogin'), 99);
    add_filter('login_headerurl', array($this, 'loginLogoUrl'));
    add_filter('login_headertitle', array($this, 'loginTitle'));
  }
  
  public function registerResourcesAdmin() {
    wp_enqueue_style('main-css-custom-admin', THEME_ADMIN_URL . 'css/main.css', false, '1.0.0');
    wp_enqueue_script('main-js-pace', THEME_ADMIN_URL . 'js/pace.min.js', true, '1.0.0');
    wp_enqueue_script('main-js-custom-admin', THEME_ADMIN_URL . 'js/custom-admin.js', true, '1.0.0');
  }
  
  public function registerResourcesLogin() {
    wp_enqueue_style('custom-login', THEME_ADMIN_URL . 'css/login.css', false, '1.0.0');
  }
  
  public function loginLogoUrl() {
    return home_url();
  }
  
  public function loginTitle() {
    return get_bloginfo('name');
  }
}
