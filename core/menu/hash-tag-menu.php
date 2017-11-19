<?php
namespace SpeedTheme\Core\Menu;

use SpeedTheme\Core\ST_MenuExtraField;

/**
 * Class HashTagMenu
 */
class HashTagMenu extends \Walker_Nav_Menu {
  
  /**
   * Walker_Nav_Menu::start_el - Starts the element output.
   */
  public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    parent::start_el($output, $item, $depth, $args, $id);
    
    $hashTag = ST_MenuExtraField::getValue($item->ID, 'hash-tag');
    
    if ($hashTag) {
      $title = apply_filters( 'the_title', $item->title, $item->ID );
      $url = '<a href="' . $item->url . $hashTag . '">' .$title . '</a>';
      
      $output = str_replace('<a href="' . $item->url . '">' . $title . '</a>', $url, $output);
    }
  }
}
