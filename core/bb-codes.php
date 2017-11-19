<?php

namespace SpeedTheme\Core;

class BbCodes {
  
  private static function handlerItem(&$string, $item) {
    if (isset($item['class'])) {
      $string = str_replace('[' . $item['code'] . ']', '<' . $item['tag'] . ' class="' . $item['class'] . '">', $string);
      $string = str_replace('[/' . $item['code'] . ']', '</' . $item['tag'] . '>', $string);
    } elseif (isset($item['tag'])) {
      $string = str_replace('[' . $item['code'] . ']', '<' . $item['tag'] . '>', $string);
      $string = str_replace('[/' . $item['code'] . ']', '</' . $item['tag'] . '>', $string);
    } else {
      $string = str_replace('[' . $item['code'] . ']', '<' . $item['code'] . '>', $string);
      $string = str_replace('[/' . $item['code'] . ']', '</' . $item['code'] . '>', $string);
    }
  }
  
  public static function renderBBCodes($string) {
    $bbCodeList = array(
      array(
        'code' => 'b',
        'tag'  => 'strong'
      ),
      array(
        'code' => 'i'
      ),
      array(
        'code' => 'br'
      ),
      array(
        'code' => 'span'
      ),
      array(
        'code'  => 'green',
        'tag'   => 'span',
        'class' => 'other__color--light'
      )
    );
    
    if (!empty($bbCodeList)) {
      foreach ($bbCodeList as $item) {
        self::handlerItem($string, $item);
      }
    }
    
    return $string;
  }
}