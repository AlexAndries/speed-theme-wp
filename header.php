<?php
/**
 * The template for displaying the header
 * Displays all of the head element and header.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js" prefix="og: http://ogp.me/ns#">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <?php if (is_singular() && pings_open(get_queried_object())) : ?>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
  <?php endif; ?>
  <title><?php wp_title(''); ?></title>
  <?php do_action('header_scripts') ?>
  <?php wp_head(); ?>
</head>

<body <?php body_class(\SpeedTheme\Core\ST_ThemeFunctions::isSafari() ? 'is-safari' : '') ?> data-ng-app="speed-app">
<?php
do_action('tracking_scripts');
?>

<header data-open-target="mobile-open">

</header>

<!-- Open Main -->
<div class="main-container" role="main">