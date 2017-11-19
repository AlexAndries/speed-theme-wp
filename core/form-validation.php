<?php

namespace SpeedTheme\Core;

class ST_FormValidation {
  
  public static function validateName($value) {
    if ($value && preg_match('/[a-zA-Z -]+$/', $value) && ST_ProfanityWords::testWordStatic($value)) {
      return true;
    }
    
    return false;
  }
  
  public static function validateRequired($value) {
    if ($value && ST_ProfanityWords::testWordStatic($value)) {
      return true;
    }
    
    return false;
  }
  
  public static function validateEmail($value) {
    if ($value && filter_var($value, FILTER_VALIDATE_EMAIL) && ST_ProfanityWords::testWordStatic($value)) {
      return true;
    }
    
    return false;
  }
  
  public static function validateDate($value) {
    if ($value && preg_match('/[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}$/', $value)) {
      try {
        $dumpData = new \DateTime($value);
        $today = new \DateTime();
        $today->add(new \DateInterval('P1D'));
        if ($today >= $dumpData) {
          return false;
        }
        
        return true;
      } catch (\Exception $e) {
        return false;
      }
    }
    
    return false;
  }
  
  public static function validateNumber($value) {
    if ($value && preg_match('/[0-9]{9}[0-9]+$/', $value) && strlen($value) < 11) {
      return true;
    }
    
    return false;
  }
  
  public static function validateZipCode($value) {
    if ($value && preg_match('/^\d{5}(?:[-\s]\d{4})?$/', $value)) {
      return true;
    }
    
    return false;
  }
}
