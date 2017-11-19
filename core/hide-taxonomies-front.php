<?php
namespace SpeedTheme\Core;

class ST_HideTaxonomiesFront {
  private $taxonomies;
  
  public function __construct() {
    $this->taxonomies = array();
    add_action('pre_get_posts', array($this, 'hideTaxonomiesAction'));
  }
  
  public function hideTaxonomiesAction($wpQuery) {
    if (!empty($this->taxonomies)) {
      foreach ($this->taxonomies as $taxonomy) {
        if (is_tax($taxonomy)) {
          $wpQuery->set_404();
          status_header( 404 );
          nocache_headers();
          include( get_query_template( '404' ) );
          exit;
        }
      }
    }
  }
  
  public function addTaxonomy($taxonomy) {
    if (!in_array($taxonomy, $this->taxonomies)) {
      $this->taxonomies[] = $taxonomy;
    }
    
    return $this;
  }
}
