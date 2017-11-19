<?php

/**
 * Template Name: Homepage Template
 */

use SpeedTheme\Mvc\Controller\HomeController;

get_header();
HomeController::testRender();
get_footer();

