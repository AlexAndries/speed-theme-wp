<?php
namespace SpeedTheme\Core;

class ST_MenuRegisterLocations {
  public $menus = array();
  
  /**
   * @param array $array
   */
  public function __construct($array = array()) {
    if (!empty($array)) {
      $this->menus = $array;
    }
  }
  
  /**
   * @param array $array
   *
   * @return $this
   */
  public function addMenu($array = array()) {
    $this->menus[] = $array;
    
    return $this;
  }
  
  /**
   * @param $id
   *
   * @return $this
   */
  public function removeMenu($id) {
    unset($this->menus[$id]);
    
    return $this;
  }
  
  /**
   * Register Menus
   */
  public function registerMenus() {
    for($i = 0; $i < sizeof($this->menus); $i++) {
      register_nav_menus($this->menus[$i]);
    }
  }
}
