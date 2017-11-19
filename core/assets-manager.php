<?php

namespace SpeedTheme\Core;

class AssetsManager {
  /**
   * @var array
   */
  private $styles;
  
  /**
   * @var array
   */
  private $scripts;
  
  /**
   * @var ST_Minify
   */
  private $minifyClient;
  
  /**
   * @var ST_EnqueueScripts
   */
  private $client;
  
  public function __construct($inFooter = false) {
    $this->client = new ST_EnqueueScripts($inFooter);
    $this->minifyClient = new ST_Minify();
  }
  
  private function processStyle() {
    if (empty($this->styles)) {
      return;
    }
    
    if (IS_IN_DEVELOPMENT) {
      foreach ($this->styles as $name => $config) {
        $path = $config['external'] ? $config['path'] : THEME_URL . $config['path'];
        $this->client->addStyle($name, $path);
      }
    } elseif (DEV_ENV) {
      foreach ($this->styles as $name => $config) {
        if (!$config['external']) {
          $this->minifyClient->addStyle($name, THEME_PATH . $config['path']);
        } else {
          $this->client->addStyle($name, $config['path']);
        }
      }
    }
    
    if (!IS_IN_DEVELOPMENT) {
      foreach ($this->styles as $name => $config) {
        if ($config['external']) {
          $this->client->addStyle($name, $config['path']);
        }
      }
      
      $this->client->addStyle('main-css', ST_Minify::getCssFile());
    }
  }
  
  private function processScripts() {
    if (empty($this->scripts)) {
      return;
    }
    
    if (IS_IN_DEVELOPMENT) {
      foreach ($this->scripts as $name => $config) {
        $path = $config['external'] ? $config['path'] : THEME_URL . $config['path'];
        $this->client->addScript($name, $path, $config['async']);
      }
    } elseif (DEV_ENV) {
      foreach ($this->scripts as $name => $config) {
        if (!$config['external']) {
          $this->minifyClient->addScript($name, THEME_PATH . $config['path']);
        } else {
          $this->client->addScript($name, $config['path'], $config['async']);
        }
      }
    }
    
    if (!IS_IN_DEVELOPMENT) {
      foreach ($this->scripts as $name => $config) {
        if ($config['external']) {
          $this->client->addScript($name, $config['path'], $config['async']);
        }
      }
      
      $this->client->addScript('main-js', ST_Minify::getJsFile());
    }
  }
  
  public function addStyle($name, $relativePath, $external = false) {
    $this->styles[$name] = array(
      'path'     => $relativePath,
      'external' => $external
    );
    
    return $this;
  }
  
  public function addScript($name, $relativePath, $async = false, $external = false) {
    $this->scripts[$name] = array(
      'path'     => $relativePath,
      'async'    => $async,
      'external' => $external
    );
    
    return $this;
  }
  
  public function build() {
    $this->processStyle();
    $this->processScripts();
  
    if (!IS_IN_DEVELOPMENT) {
      $this->minifyClient->developmentBuild();
    }
  }
}