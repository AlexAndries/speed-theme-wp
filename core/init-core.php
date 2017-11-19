<?php

namespace SpeedTheme\Core;

/**
 * Autoload Theme Classes
 */
spl_autoload_register(function ($className) {
  $path = explode('\\', $className);
  if ($path[0] === 'SpeedTheme') {
    unset($path[0]);
    
    $filePath = THEME_PATH;
    
    for ($i = 1; $i < sizeof($path); $i++) {
      $dirName = preg_split('/(?=[A-Z])/', $path[$i], -1, PREG_SPLIT_NO_EMPTY);
      foreach ($dirName as $word) {
        $filePath .= strtolower($word) . '-';
      }
      $filePath = trim($filePath, '-') . DIRECTORY_SEPARATOR;
    }
    
    $path = $path[$i];
    $path = str_replace('ST_', '', $path);
    $path = preg_split('/(?=[A-Z])/', $path, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($path as $item) {
      $filePath .= strtolower($item) . '-';
    }
    
    $filePath = trim($filePath, '-');
    $filePath .= '.php';
    
    if (file_exists($filePath)) {
      require_once $filePath;
    }
  }
});

if (ST_Core::checkThemeDependencies()) {
  
  $cookiesDB = new ST_CookiesDb();
  
  new ST_ActionsSet();
  
  /**
   * Load Option Pages
   */
  require_once('option-page.php');
}


