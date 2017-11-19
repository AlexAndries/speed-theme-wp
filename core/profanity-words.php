<?php

namespace SpeedTheme\Core;

class ST_ProfanityWords {
  private $words;
  
  public function __construct() {
    $this->words = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'profanity-words.json'));
  }
  
  public function testWord($words) {
    foreach ($this->words as $word) {
      if (strpos(strtolower($words), strtolower($word)) > -1) {
        return false;
      }
    }
    
    return true;
  }
  
  public static function testWordStatic($words) {
    $tester = new self();
    
    return $tester->testWord($words);
  }
}
