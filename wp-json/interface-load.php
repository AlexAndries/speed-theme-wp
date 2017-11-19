<?php

namespace SpeedTheme\WpJson;

interface InterfaceLoad {
  public function getBlockName();
  
  public function acceptRequest(\WP_REST_Request $request);
  
  public function getRoute();
  
  public function getMethod();
  
  public function getArgs();
}
