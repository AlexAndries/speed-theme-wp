<?php

namespace SpeedTheme\Core;

class ST_Core {
  const THEME_COLOR = '#006dc0';
  
  public static function getVersionNumber() {
    return (int)str_replace('.', '', ST_VERSION);
  }
  
  public static function checkThemeDependencies() {
    
    if (!function_exists('get_field')) {
      add_action('admin_notices', array(get_called_class(), 'themeDependenciesNotice'));
      
      return false;
    }
    
    return true;
  }
  
  public function themeDependenciesNotice() {
    ?>
    <div class="notice notice-warning">
      <p><?php _e('Plugin ', 'speed-theme') ?>
        <a href="https://www.advancedcustomfields.com/" target="_blank">
          <?php _e('Advanced Custom Fields PRO', 'speed-theme') ?>
        </a>
        <?php _e(' need to be installed and active in order to use this theme! Plugin can be added from ', 'speed-theme') ?>
        <a href="<?php echo admin_url('plugins.php') ?>"><?php _e('here', 'speed-theme') ?></a>.
      </p>
    </div>
    <?php
  }
}
