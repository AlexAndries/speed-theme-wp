<?php
namespace SpeedTheme\Core;

class ST_MenusAdminWrapper {
  /**
   * @var array
   */
  private $menuHide = array();
  
  /**
   * @var array
   */
  private $subMenuHide = array();
  
  /**
   * MenusAdminWrapper constructor.
   */
  public function __construct() {
    add_action('admin_menu', array($this, 'removeMenu'), 999);
  }
  
  /**
   * @param $slug
   *
   * @return $this
   */
  public function addMenuToHide($slug) {
    $this->menuHide[] = $slug;
    
    return $this;
  }
  
  /**
   * @param $page
   * @param $subPage
   *
   * @return $this
   */
  public function addSubMenuToHide($page, $subPage) {
    $this->subMenuHide[] = array(
      'page'    => $page,
      'subPage' => $subPage
    );
    
    return $this;
  }
  
  public function removeMenu() {
    global $submenu;
    
    if (!empty($this->menuHide)) {
      foreach ($this->menuHide as $item) {
        remove_menu_page($item);
      }
    }
    if (!empty($this->subMenuHide)) {
      foreach ($this->subMenuHide as $item) {
        remove_submenu_page($item['page'], $item['subPage']);
      }
    }
    
    unset($submenu['themes.php'][6]);
  }
}
