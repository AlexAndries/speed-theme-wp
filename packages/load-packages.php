<?php

namespace SpeedTheme\Packages;

/**
 * Load Theme Setup
 */
require_once('theme-setup.php');

/**
 * Load Theme Post Types
 */
require_once('post-types.php');

/**
 * Load Option Pages
 */
require_once('option-page.php');

/**
 * Load Hidden Taxonomies
 */
require_once('hidden-taxonomies.php');

new ActionsSet();
