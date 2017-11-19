<?php

namespace SpeedTheme\Core;

use SpeedTheme\WPJSON\Core;
use SpeedTheme\WPJSON\Register;

class ST_ActionsSet {
  
  public function __construct() {
    add_action('after_setup_theme', array($this, 'afterSetupTheme'));
    
    add_filter('style_loader_src', array($this, 'removeVersionForStaticResources'), 10, 2);
    add_filter('script_loader_src', array($this, 'removeVersionForStaticResources'), 10, 2);
    
    add_action('wp_head', array($this, 'generalJavaScriptVariables'), 1);
    
    add_action('loader_section', array($this, 'loaderSiteBlock'));
    
    add_action('tracking_scripts', array($this, 'trackingScripts'));
    
    add_action('init', array($this, 'removeHeadLinks'));
    
    add_action('init', array($this, 'disableEmbedsInit'), 9999);
    
    add_action('wp_before_admin_bar_render', array($this, 'removeCommentsFromTopBar'));
    
    add_filter('sanitize_file_name', array($this, 'sanitizeFilesToLowerCase'), 10);
    
    add_action('wp_head', array($this, 'favicon'), 99);
    add_action('login_enqueue_scripts', array($this, 'favicon'), 99);
    add_action('admin_enqueue_scripts', array($this, 'favicon'));
    
    add_filter('admin_footer_text', array($this, 'updateAdminFooterText'));
    
    remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_resource_hints', 2);
    remove_action('template_redirect', 'rest_output_link_header');
    remove_action('wp_head', 'wp_generator');
    remove_filter('template_redirect', 'redirect_canonical');
    
    add_action('template_redirect', array($this, 'disableAuthorArchive'));
    
    add_filter('acf/fields/google_map/api', array($this, 'acfGoogleMapApi'));
  }
  
  public function afterSetupTheme() {
    ST_CookiesDb::createCookiesTable();
    
    show_admin_bar(false);
  }
  
  public function removeVersionForStaticResources($src) {
    if (strpos($src, '?ver=')) {
      $src = remove_query_arg('ver', $src);
    }
    
    return $src;
  }
  
  public function generalJavaScriptVariables() {
    ?>
    <script>
      var siteUrl = '<?php echo SITE_URL; ?>',
        CDN_CNAME = '<?php echo CDN_CNAME ?>',
        ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>',
        restUrl = '<?php echo Register::getApiRoute(); ?>',
        sliders = [],
        config = {
          FID: '<?php the_field('facebook_app_id', 'options') ?>'
        },
        dataLayer = [],
        appModules = {},
        themeUrl = '<?php echo THEME_URL ?>',
        hash = '<?php echo Core::encryptingAccessToken() ?>',
        getCookie = function(name) {
          var value = '; ' + document.cookie;
          var parts = value.split('; ' + name + '=');
          if (parts.length === 2) {
            return parts.pop().split(';').shift();
          }
        };
    </script>
    <?php
  }
  
  public function loaderSiteBlock() {
    global $cookiesDB;
    $currentUserDeploy = $cookiesDB->getCookie('loading');
    if (!$currentUserDeploy || $currentUserDeploy < ST_Core::getVersionNumber()) {
      $logoFade = get_field('loader_fade_image', 'options');
      $logo = get_field('loader_complete_image', 'options');
      if ($logo && $logoFade) { ?>
        <div class="loading-wrapper" id="loading-wrapper"
             style="background-color:<?php the_field('loader_background', 'options') ?>">
          <div class="loading-content">
            <img src="<?php echo $logoFade['url'] ?>" height="<?php echo $logoFade['height'] ?>"
                 width="<?php echo $logoFade['width'] ?>" class="img-responsive"
                 alt="<?php bloginfo('name') ?>">
            <div class="loader-effect">
              <img src="<?php echo $logo['url'] ?>" height="<?php echo $logo['height'] ?>"
                   width="<?php echo $logo['width'] ?>" class="img-responsive"
                   alt="<?php bloginfo('name') ?>">
            </div>
          </div>
        </div>
        <script>
          if (typeof getCookie('loading') === 'undefined' || getCookie('loading') < <?php echo ST_Core::getVersionNumber() ?> ) {
            var loader = document.getElementById('loading-wrapper');
            loader.style.display = null;
          }
        </script>
        <?php
      }
    }
  }
  
  public function trackingScripts() {
    the_field('ga_code', 'options');
    the_field('pixel_code', 'options');
  }
  
  public function disableEmbedsInit() {
    remove_action('rest_api_init', 'wp_oembed_register_route');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    add_filter('tiny_mce_plugins', array($this, 'disableEmojiconsTinymce'));
  }
  
  public function disableEmojiconsTinymce($plugins) {
    if (is_array($plugins)) {
      return array_diff($plugins, array('wpemoji'));
    }
    
    return array();
  }
  
  public function removeCommentsFromTopBar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
  }
  
  public function sanitizeFilesToLowerCase($filename) {
    
    return strtolower($filename);
  }
  
  public function favicon() {
    ?>
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo THEME_URL ?>images/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo THEME_URL ?>images/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo THEME_URL ?>images/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo THEME_URL ?>images/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo THEME_URL ?>images/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo THEME_URL ?>images/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo THEME_URL ?>images/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo THEME_URL ?>images/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo THEME_URL ?>images/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"
          href="<?php echo THEME_URL ?>images/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo THEME_URL ?>images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo THEME_URL ?>images/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo THEME_URL ?>images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo THEME_URL ?>images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="<?php echo ST_Core::THEME_COLOR ?>">
    <meta name="msapplication-TileImage" content="<?php echo THEME_URL ?>images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="<?php echo ST_Core::THEME_COLOR ?>">
    <?php
  }
  
  public function updateAdminFooterText() {
    echo 'Fueled by <a href="http://www.wordpress.org" target="_blank">WordPress</a> |
    Designed by <a href="http://www.xivic.com" target="_blank">Xivic Inc.</a></p>';
  }
  
  public function removeHeadLinks() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
  }
  
  public function disableAuthorArchive() {
    if (is_author()) {
      global $wp_query;
      $wp_query->set_404();
      status_header(404);
    } else {
      redirect_canonical();
    }
  }
  
  public function acfGoogleMapApi($api) {
    
    $api['key'] = ST_ThemeFunctions::getCredential(CredentialsList::GOOGLE_MAPS_API);
    
    return $api;
  }
}
