<?php

namespace SpeedTheme\WpJson;

class Register {
  const NAMESPACE_NAME = 'speed-rest';
  
  const VERSION = 'v1';
  
  private $rests = array();
  
  public function __construct() {
    add_action('rest_api_init', array($this, 'registerREST'));
  }
  
  public static function getNamespace() {
    return self::NAMESPACE_NAME . '/' . self::VERSION;
  }
  
  public function addRest($class) {
    $this->rests[] = $class;
    
    return $this;
  }
  
  public function getRestsClasses() {
    return $this->rests;
  }
  
  public static function getApiRoute($path = '/') {
    return site_url('/') . Core::API_ENDPOINT . '/' . self::getNamespace() . $path;
  }
  
  public function registerREST() {
    if (!empty($this->rests)) {
      foreach ($this->rests as $rest) {
        $restClass = new $rest();
        if ($restClass instanceof InterfaceLoad) {
          register_rest_route(self::getNamespace(), $restClass->getRoute(), array(
            'methods'  => $restClass->getMethod(),
            'callback' => array($restClass, 'acceptRequest'),
            'args'     => $restClass->getArgs()
          ));
        }
      }
    }
  }
}
