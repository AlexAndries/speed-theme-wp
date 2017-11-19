<?php

namespace SpeedTheme\Mvc\Core;

use Twig_Environment;
use Twig_Loader_Filesystem;

class ViewRender {
  
  const PATH_TO_VIEWS = THEME_PATH . 'mvc/views';
  
  public static function renderView($view, $params) {
    
    $loader = new Twig_Loader_Filesystem(self::PATH_TO_VIEWS);
    $twig = new Twig_Environment($loader);
    $template = $twig->load($view);
    echo $template->render($params);
  }
}