<?php

if (function_exists('acf_add_options_page')) {
  acf_add_options_sub_page(array(
    'page_title'  => 'Header',
    'menu_title'  => 'Header',
    'menu_slug'   => 'header-settings',
    'parent_slug' => 'theme-general-settings',
    'capability'  => 'edit_posts'
  ));

  acf_add_options_sub_page(array(
    'page_title'  => 'Footer',
    'menu_title'  => 'Footer',
    'menu_slug'   => 'footer-settings',
    'parent_slug' => 'theme-general-settings',
    'capability'  => 'edit_posts'
  ));
  
  acf_add_options_sub_page(array(
    'page_title'  => 'Forms Options',
    'menu_title'  => 'Forms Options',
    'menu_slug'   => 'forms-settings',
    'parent_slug' => 'theme-general-settings',
    'capability'  => 'edit_posts'
  ));
  
  acf_add_options_sub_page(array(
    'page_title'  => 'Not Found Page',
    'menu_title'  => 'Not Found Page',
    'menu_slug'   => 'not-found-page',
    'parent_slug' => 'theme-general-settings',
    'capability'  => 'edit_posts'
  ));
}
