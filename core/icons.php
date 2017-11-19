<?php
namespace SpeedTheme\Core;

class ST_Icons {
  const PATH_TO_ICONS = PATH_TO_ICONS;
  
  public static function renderIcon($icon) {
    if (file_exists(self::PATH_TO_ICONS . $icon . '.svg')) {
      include self::PATH_TO_ICONS . $icon . '.svg';
    } else {
      echo 'icon ' . $icon . ' file is not found';
    }
  }
}
