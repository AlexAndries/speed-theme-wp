<?php

namespace SpeedTheme\WpJson;

use SpeedTheme\Core\ST_ThemeFunctions;

class Core {
  
  const SALT_SIZE = 3;
  
  const API_ENDPOINT = 'api-endpoint';
  
  private static function decryptingAccessToken($token) {
    $token = substr($token, self::SALT_SIZE);
    $token = substr($token, 0, self::SALT_SIZE * -1);
    $token = base64_decode($token);
    $token = str_split($token);
    $access = '';
    for ($i = 0; $i < sizeof($token); $i += self::SALT_SIZE + 1) {
      $access .= $token[$i];
    }
    
    return $access;
  }
  
  public static function init() {
    $class = new self();
    add_filter('rest_authentication_errors', array($class, 'removeWPJson'));
    
    add_filter('rest_url_prefix', array($class, 'changeApiRoute'));
  }
  
  public function changeApiRoute($prefix) {
    return self::API_ENDPOINT;
  }
  
  public static function encryptingAccessToken($string = false) {
    $string = $string ? $string : API_USER . ':' . API_PASSWORD;
    $string = str_split($string);
    $accessToken = '';
    for ($i = 0; $i < sizeof($string); $i++) {
      $accessToken .= $string[$i] . ST_ThemeFunctions::generateID(self::SALT_SIZE);
    }
    
    return ST_ThemeFunctions::generateID(self::SALT_SIZE) . base64_encode($accessToken) . ST_ThemeFunctions::generateID(self::SALT_SIZE);
  }
  
  public static function checkAccess($token) {
    return self::decryptingAccessToken($token) === API_USER . ':' . API_PASSWORD;
  }
  
  public function removeWPJson($access) {
    if (strpos($_SERVER['REQUEST_URI'], Register::NAMESPACE_NAME) !== false) {
      $headers = getallheaders();
      
      if (strpos($_SERVER['REQUEST_URI'], 'secured')) {
        if (isset($headers['Authorization'])) {
          $token = explode(' ', $headers['Authorization']);
          if (isset($token[1]) && self::checkAccess($token[1])) {
            return $access;
          }
        }
      } else {
        return $access;
      }
    }
    
    return new \WP_Error('rest_cannot_access', __('REST API DISABLED', 'disable-json-api'), array(
      'status' => rest_authorization_required_code()
    ));
  }
}
