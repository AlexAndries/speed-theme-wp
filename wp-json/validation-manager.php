<?php

namespace SpeedTheme\WpJson;

class ValidationManager {
  const REQUIRED = 'REQUIRED';
  
  public static function requiredValidation($param, $request, $key) {
    return trim($param) && $param != "" && strlen($param) > 1;
  }
  
  public static function nameValidation($param, $request, $key) {
    if (!$param) {
      return true;
    }
    
    if ($param && preg_match('/[a-zA-Z -]+$/', $param)) {
      return true;
    }
    
    return false;
  }
  
  public static function zipCodeValidation($param, $request, $key) {
    if (!$param) {
      return true;
    }
    
    if ($param && preg_match('/^\d{5}(?:[-\s]\d{4})?$/', $param)) {
      return true;
    }
    
    return false;
  }
  
  public static function emailValidation($param, $request, $key) {
    if ($param && filter_var($param, FILTER_VALIDATE_EMAIL)) {
      return true;
    }
    
    return false;
  }
  
  public static function yearsOfServiceValidation($param, $request, $key) {
    if ($param && preg_match('/[0-9]{4}[-][0-9]{4}$/', $param)) {
      return true;
    }
  
    return false;
  }
}
