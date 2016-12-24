<?php

namespace auth_token;

/**
 * Implements hook_permission()
 */
function permission() {
  return array();
}

/**
 * Implements hook_menu()
 */
function menu() {
  return array();
}

/**
 * Implements hook_url()
 */
function url() {
  return array();
}

/**
 * Implements hook_libraries()
 */
function libraries() {
  return array(
    'AuthTokenAPI.php'
  );
}

/**
 * Implements hook_cron()
 */
function cron() {
  // execute actions to be performed on cron
}

/**
 * Implements hook_twig_function()
 */
function twig_function() {
  // return an array of key value pairs.
  // key: twig_function_name
  // value: actual_function_name
  // You may use object functions as well
  // e.g. ObjectClass::actual_function_name  
  return array();
}

/**
 * Implements hook_preprocess_page()
 */
function preprocess_page() {
  // execute actions just before the page is rendered.
}

/**
 * Implements hook_preprocess_boot()
 */
function preprocess_boot() {
  // execute actions after the core has been loaded but before the extensions have been loaded.
}

/**
 * Implements hook_postprocess_boot()
 */
function postprocess_boot() {
  // execute actions after core and extensions have been loaded.
}