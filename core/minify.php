<?php

namespace SpeedTheme\Core;

use MatthiasMullie\Minify;

class ST_Minify {
  const PATH_TO_MINIFY_FOLDER = 'minify/';
  
  const CSS_FILE = 'main{version}.min.css';
  
  const JS_FILE = 'main{version}.min.js';
  
  private $styles = array();
  
  private $scripts = array();
  
  private $clearMinifyMessage;
  
  public function __construct($devEnv = false) {
    $this->clearMinifyMessage = __('Minify files regenerated successful!', 'speed-theme');
    
    try {
      if (!file_exists(THEME_PATH . self::PATH_TO_MINIFY_FOLDER)) {
        mkdir(THEME_PATH . self::PATH_TO_MINIFY_FOLDER);
      }
    } catch (\Exception $e) {
      var_dump($e);
    }
  }
  
  private static function replaceNameWithVersion($name) {
    return str_replace('{version}', '-version-' . ST_VERSION, $name);
  }
  
  public static function getCssFile() {
    return THEME_URL . self::PATH_TO_MINIFY_FOLDER . self::replaceNameWithVersion(self::CSS_FILE);
  }
  
  public static function getJsFile() {
    return THEME_URL . self::PATH_TO_MINIFY_FOLDER . self::replaceNameWithVersion(self::JS_FILE);
  }
  
  public function addStyle($name, $path) {
    $this->styles[$name] = $path;
    
    return $this;
  }
  
  public function addScript($name, $path) {
    $this->scripts[$name] = $path;
    
    return $this;
  }
  
  public function runStyleMinify() {
    $minifier = new Minify\CSS();
    if (!empty($this->styles)) {
      foreach ($this->styles as $item) {
        $minifier->add($item);
      }
      try {
        $minifier->minify(THEME_PATH . self::PATH_TO_MINIFY_FOLDER . self::replaceNameWithVersion(self::CSS_FILE));
      } catch (\Exception $e) {
        $this->clearMinifyMessage = $e->getMessage();
      }
    }
    
    return $this;
  }
  
  public function runScriptMinify() {
    $minifier = new Minify\JS();
    if (!empty($this->scripts)) {
      foreach ($this->scripts as $item) {
        $minifier->add($item);
      }
      try {
        $minifier->minify(THEME_PATH . self::PATH_TO_MINIFY_FOLDER . self::replaceNameWithVersion(self::JS_FILE));
      } catch (\Exception $e) {
        $this->clearMinifyMessage = $e->getMessage();
      }
    }
    
    return $this;
  }
  
  public function minifyNotice() {
    ?>
    <div class="notice notice-success is-dismissible">
      <p><?php echo $this->clearMinifyMessage; ?></p>
    </div>
    <?php
  }
  
  public function developmentBuild() {
    if (defined('DEV_ENV') && DEV_ENV) {
      $this->runScriptMinify()
           ->runStyleMinify();
    }
  }
}
