<?php

namespace SpeedTheme\Core;

class ST_EnqueueScripts {
  /**
   * @var array
   */
  private $scripts = array();
  
  /**
   * @var array
   */
  private $style = array();
  
  /**
   * @var array
   */
  private $adminStyle = array();
  
  /**
   * @var array
   */
  private $adminScripts = array();
  
  /**
   * Constructor
   *
   * @param bool|false $inFooter
   */
  public function __construct($inFooter = false) {
    if ($inFooter) {
      add_action('footer_scripts', array($this, 'registerStaticResources'));
    } else {
      add_action('header_scripts', array($this, 'registerStaticResources'));
    }
    
    if (is_admin()) {
      add_action('admin_enqueue_scripts', array($this, 'registerAdminScripts'));
    }
  }
  
  /**
   * Add script frontend
   *
   * @param            $handle
   * @param            $src
   * @param bool|false $async
   *
   * @return $this
   */
  public function addScript($handle, $src = false, $async = false) {
    $this->scripts[$handle] = array(
      'handle' => $handle,
      'src'    => $src,
      'async'  => $async
    );
    
    return $this;
  }
  
  /**
   * Add style frontend
   *
   * @param            $handle
   * @param bool|false $src
   * @param string     $media
   *
   * @return $this
   */
  public function addStyle($handle, $src = false, $media = 'all') {
    $this->style[$handle] = array(
      'handle' => $handle,
      'src'    => $src,
      'media'  => $media
    );
    
    return $this;
  }
  
  /**
   * remove script
   *
   * @param $handle
   *
   * @return $this
   */
  public function removeScript($handle) {
    unset($this->scripts[$handle]);
    
    return $this;
  }
  
  /**
   * remove style
   *
   * @param $handle
   *
   * @return $this
   */
  public function removeStyle($handle) {
    unset($this->style[$handle]);
    
    return $this;
  }
  
  public function registerStaticResources() {
    echo $this->buildStyle();
    echo $this->buildScripts();
  }
  
  public function buildStyle() {
    if (!empty($this->style)) {
      $content = '';
      foreach ($this->style as $id => $values) {
        $content .= '<link rel="stylesheet" id="' . $id . '" href="' . $values['src'] . '" type="text/css" ' . ($values['media'] ? 'media="' . $values['media'] . '"' : '') . ' />';
      }
      
      return $content;
    } else {
      return false;
    }
  }
  
  public function buildScripts() {
    if (!empty($this->scripts)) {
      $content = '';
      foreach ($this->scripts as $id => $values) {
        $content .= '<script type="text/javascript" src="' . $values['src'] . '" ' . ($values['async'] ? 'async' : '') . '></script>';
      }
      
      return $content;
    } else {
      return false;
    }
  }
  
  public function addScriptAdmin($handle, $src = false, $in_footer = true, $async = false) {
    $this->adminScripts[$handle] = array(
      'handle'    => $handle,
      'src'       => $src,
      'in_footer' => $in_footer,
      'async'     => $async
    );
    
    return $this;
  }
  
  /**
   * Add style Backend
   *
   * @param            $handle
   * @param bool|false $src
   * @param string     $media
   *
   * @return $this
   */
  public function addStyleAdmin($handle, $src = false, $media = 'all') {
    $this->adminStyle[$handle] = array(
      'handle' => $handle,
      'src'    => $src,
      'media'  => $media
    );
    
    return $this;
  }
  
  public function registerAdminScripts() {
    if (!empty($this->adminScripts)) {
      wp_deregister_script('jquery');
      foreach ($this->adminScripts as $script) {
        if ($script['src']) {
          wp_register_script($script['handle'], $script['src'], array(), '1.0.0', $script['in_footer']);
        }
        wp_enqueue_script($script['handle']);
      }
    }
    
    if (!empty($this->adminStyle)) {
      foreach ($this->adminStyle as $style) {
        wp_enqueue_style($style['handle'], $style['src']);
      }
    }
  }
}
