<?php

namespace SpeedTheme\Core;

if (!class_exists('\Walker_Nav_Menu_Edit')) {
  require_once(ABSPATH . 'wp-admin/includes/class-walker-nav-menu-edit.php');
}

class ST_MenuExtraField extends \Walker_Nav_Menu_Edit {
  private $fieldName;
  
  private $fieldLabel;
  
  public function __construct($fieldName, $fieldLabel) {
    $this->fieldName = $fieldName;
    $this->fieldLabel = $fieldLabel;
    
    add_action('wp_nav_menu_item_extra_fields', array($this, 'generateField'), 10, 4);
    add_action('wp_update_nav_menu_item', array($this, 'saveField'), 10, 3);
    add_filter('manage_nav-menus_columns', array($this, 'addNewField'), 99);
  }
  
  public static function getValue($id, $name) {
    $key = sprintf('menu-item-%s', $name);
    
    return get_post_meta($id, $key, true);
  }
  
  public function generateField($item) {
    $key = sprintf('menu-item-%s', $this->fieldName);
    $id = sprintf('edit-%s-%s', $key, $item->ID);
    $name = sprintf('%s[%s]', $key, $item->ID);
    $value = get_post_meta($item->ID, $key, true);
    $class = sprintf('field-%s', $this->fieldName);
    ?>
    <p class="description description-wide <?php echo esc_attr($class) ?>">
      <?php printf('<label for="%1$s">%2$s<br /><input type="text" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" /></label>', esc_attr($id), esc_html($this->fieldLabel), esc_attr($name), esc_attr($value)) ?>
    </p>
    <?php
  }
  
  public function saveField($menu_id, $menu_item_db_id, $menu_item_args) {
    $key = sprintf('menu-item-%s', $this->fieldName);
    
    if (!empty($_POST[$key][$menu_item_db_id])) {
      $value = $_POST[$key][$menu_item_db_id];
    } else {
      $value = null;
    }
    
    if (!is_null($value)) {
      update_post_meta($menu_item_db_id, $key, $value);
    } else {
      delete_post_meta($menu_item_db_id, $key);
    }
  }
  
  public function addNewField($columns) {
    $columns = array_merge($columns, array($this->fieldName => $this->fieldLabel));
    
    return $columns;
  }
}

class MenuExtraFieldsLoader {
  public static function load() {
    add_filter('wp_edit_nav_menu_walker', array(__CLASS__, '_filter_walker'), 99);
  }
  
  public static function _filter_walker($walker) {
    $walker = 'SpeedTheme\Core\MenuExtraFieldsWalker';
    
    return $walker;
  }
}

add_action('wp_loaded', array('\SpeedTheme\Core\MenuExtraFieldsLoader', 'load'), 9);

class MenuExtraFieldsWalker extends \Walker_Nav_Menu_Edit {
  protected function get_fields($item, $depth, $args = array(), $id = 0) {
    ob_start();
    do_action('wp_nav_menu_item_extra_fields', $item);
    
    return ob_get_clean();
  }
  
  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $item_output = '';
    parent::start_el($item_output, $item, $depth, $args, $id);
    
    $output .= preg_replace('/(?=<(fieldset|p)[^>]+class="[^"]*field-move)/', $this->get_fields($item, $depth, $args), $item_output);
  }
}
