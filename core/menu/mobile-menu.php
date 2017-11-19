<?php

namespace SpeedTheme\Core\Menu;

/**
 * Class MobileMenu
 */
class MobileMenu extends HashTagMenu {
  /**
   * Walker_Nav_Menu::start_el - Starts the element output.
   *
   * @param string   $output
   * @param \WP_Post $item
   * @param int      $depth
   * @param array    $args
   * @param int      $id
   */
  public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    
    parent::start_el($output, $item, $depth, $args, $id);
    if (isset($args->walker) && isset($args->walker->has_children) && $args->walker->has_children) {
      $output .= '<span class="fa fa-angle-down open-submenu-handler" data-open-mobile-submenu aria-hidden="true"></span>';
    }
  }
}
