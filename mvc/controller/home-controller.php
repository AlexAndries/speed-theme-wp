<?php

namespace SpeedTheme\Mvc\Controller;

use SpeedTheme\Mvc\Core\ViewRender;

class HomeController {
  public static function testRender() {
    ViewRender::renderView('homepage/header.twig', array('demo' => 'perfect'));
  }
}