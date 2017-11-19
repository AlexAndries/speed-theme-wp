<?php

namespace SpeedTheme\Core\Menu;

use SpeedTheme\Core\ST_MenuExtraField;
use SpeedTheme\Core\ST_ThemeFunctions;

/**
 * Class PreviewMenu
 */
class PreviewMenu extends HashTagMenu {
  private $menuArray = array();
  
  /**
   * PreviewMenu constructor.
   */
  public function __construct() {
    add_action('wp_footer', array($this, 'getMenuJson'));
  }
  
  /**
   * getElementPreviewData
   *
   * @param $item
   *
   * @return array
   */
  private function getElementPreviewData($item) {
    $target = ST_MenuExtraField::getValue($item->ID, 'preview-target');
    
    if ($target) {
      $type = explode('_', $target);
      $target = false;
      
      if (sizeof($type) > 1) {
        switch ($type[0]) {
          case 'image':
            $target = $item->object_id;
            $image = wp_get_attachment_image_src($type[1], 'large');
            
            return array(
              'title'       => str_replace('>', '', $item->title),
              'image'       => $image ? $image[0] : '',
              'description' => get_field('menu_description', $target),
            );
            break;
        }
      }
    }
    
    if (!$target) {
      if ($item->type === "taxonomy") {
        $term = get_term($item->object_id);
        $target = $term->taxonomy . '_' . $term->term_taxonomy_id;
      } else {
        $target = $item->object_id;
      }
    }
    
    if (get_field('display_on_preview', $target)) {
      $image = get_field('menu_image', $target);
      
      return array(
        'title'       => str_replace('>', '', $item->title),
        'image'       => $image ? $image['sizes']['large'] : '',
        'description' => get_field('menu_description', $target),
      );
    }
  }
  
  public function start_lvl(&$output, $depth = 0, $args = array()) {
    parent::start_lvl($output, $depth, $args);
  }
  
  /**
   * Walker_Nav_Menu::end_lvl - Ends the list of after the elements are added.
   */
  public function end_lvl(&$output, $depth = 0, $args = array()) {
    $output .= '
      <li class="column-4">
        <div class="preview-menu">
          <div class="preview-menu__loader" data-ng-class="{\'show\':npc.NavigationPreviewEntity.loading}">
            <span class="fa fa-spinner fa-pulse fa-fw"></span>
          </div>
          <div class="preview-menu__image"
               data-ng-class="{\'show\':npc.NavigationPreviewEntity.currentPreview.image && !npc.NavigationPreviewEntity.loading}">
            <img ng-src="{{ npc.NavigationPreviewEntity.currentPreview.image }}" src="" class="img-responsive">
          </div>
          <div class="preview-menu__title font-bold"
               data-ng-class="{\'show\':npc.NavigationPreviewEntity.currentPreview.title && !npc.NavigationPreviewEntity.loading}">
            <span data-ng-bind-html="npc.NavigationPreviewEntity.currentPreview.title"></span>
          </div>
          <div class="preview-menu__description"
               data-ng-class="{\'show\':npc.NavigationPreviewEntity.currentPreview.description && !npc.NavigationPreviewEntity.loading}">
            <span data-ng-bind-html="npc.NavigationPreviewEntity.currentPreview.description"></span>
          </div>
         </div>
      </li>
      ';
    
    parent::end_lvl($output, $depth, $args);
  }
  
  /**
   * Walker_Nav_Menu::start_el - Starts the element output.
   */
  public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    if ($depth === 1 && in_array('start-column-1', $item->classes)) {
      $output .= '<li class="column-1"><ul class="column-container">';
    }
    
    if ($depth === 1 && in_array('start-column-2', $item->classes)) {
      $output .= '<li class="column-2"><ul class="column-container">';
    }
    
    if ($depth === 1 && in_array('start-column-3', $item->classes)) {
      $output .= '<li class="column-3"><ul class="column-container">';
    }
    
    parent::start_el($output, $item, $depth, $args, $id);
  
    $stringMethods = '" data-ng-mouseenter="npc.mouseEnterHandler(' . $item->ID . ')"  data-ng-mouseleave="npc.mouseLeaveHandler(' . $item->ID . ')"';
    $output = str_replace(' menu-item-' . $item->ID . '"', $stringMethods, $output);
  
    $menuPreview = $this->getElementPreviewData($item);
    $menuPreview['parent'] = $item->menu_item_parent;
    $menuPreview['id'] = $item->ID;
  
    if ($menuPreview) {
      $this->menuArray['menu-' . $item->ID] = $menuPreview;
    } else {
      $this->menuArray['menu-' . $item->ID] = $this->menuArray['menu-' . $item->menu_item_parent];
    }
  }
  
  public function end_el(&$output, $item, $depth = 0, $args = array()) {
    parent::end_el($output, $item, $depth, $args);
    
    if ($depth === 1 && (in_array('end-column-1', $item->classes) || in_array('end-column-2', $item->classes) || in_array('end-column-3', $item->classes))) {
      $output .= '</li></ul>';
    }
  }
  
  /**
   * Get menu Json
   */
  public function getMenuJson() {
    ?>
    <script>
      var previewMenu = <?php echo json_encode($this->menuArray) ?>;
    </script>
    <?php
  }
  
}
