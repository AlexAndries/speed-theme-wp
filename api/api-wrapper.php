<?php

namespace SpeedTheme\Api;

use SpeedTheme\Core\CredentialsList;
use SpeedTheme\Core\ST_FormValidation;
use SpeedTheme\Core\ST_Mail;
use SpeedTheme\Core\ST_SwiftMailerWrapper;
use SpeedTheme\Core\ST_ThemeFunctions;

class ApiWrapper {
  
  protected $response = array();
  
  protected $request;
  
  private static function getChildrenClasses() {
    foreach (get_declared_classes() as $class) {
      if (is_subclass_of($class, get_called_class())) {
        try {
          $apiName = ApiWrapper::getApiName($class);
          $api = new $class();
          add_action('wp_ajax_' . $apiName, array($api, 'acceptRequest'));
          add_action('wp_ajax_nopriv_' . $apiName, array($api, 'acceptRequest'));
        } catch (\Exception $e) {
          var_dump($e->getMessage());
        }
      }
    }
  }
  
  /**
   * @param $options
   *
   * @return \SpeedTheme\Core\ST_Mail|\SpeedTheme\Core\ST_SwiftMailerWrapper
   */
  protected function getMailProvided($options) {
    switch (get_option('mail_provided', 'options')) {
      case 'smtp' :
        return new ST_SwiftMailerWrapper($options);
        break;
      
      default:
        return new ST_Mail($options);
        break;
    }
  }
  
  protected function validate($type) {
    switch ($type) {
      case 'contact-form':
        if (!ST_FormValidation::validateRequired($this->request['subject'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'subject',
            'message' => get_field('faq_error_message_contact_us_form', 'options')
          );
          $this->setResponse();
        }
        
        if (!ST_FormValidation::validateName($this->request['firstName'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'firstName',
            'message' => get_field('first_name_error_message_contact_us_form', 'options')
          );
          $this->setResponse();
        }
        
        if (!ST_FormValidation::validateName($this->request['lastName'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'lastName',
            'message' => get_field('last_name_error_message_contact_us_form', 'options')
          );
          $this->setResponse();
        }
        
        if (!ST_FormValidation::validateEmail($this->request['email'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'email',
            'message' => get_field('email_error_message_contact_us_form', 'options')
          );
          $this->setResponse();
        }
        
        if (!ST_FormValidation::validateNumber($this->request['phone'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'phone',
            'message' => get_field('phone_error_message_contact_us_form', 'options')
          );
          $this->setResponse();
        }
        
        if (ST_FormValidation::validateRequired($this->request['zip']) && !ST_FormValidation::validateZipCode($this->request['zip'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'zip',
            'message' => get_field('zip_error_message_contact_us_form', 'options')
          );
          $this->setResponse();
        }
        
        if (!ST_FormValidation::validateRequired($this->request['message'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'message',
            'message' => get_field('message_error_message_contact_us_form', 'options')
          );
          $this->setResponse();
        }
        
        break;
      case 'story-form':
        if (!ST_FormValidation::validateName($this->request['firstName'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'firstName',
            'message' => get_field('first_name_error_message_story_form', 'options')
          );
          $this->setResponse();
        }

        if (!ST_FormValidation::validateName($this->request['lastName'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'lastName',
            'message' => get_field('last_name_error_message_story_form', 'options')
          );
          $this->setResponse();
        }

        if (!ST_FormValidation::validateEmail($this->request['email'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'email',
            'message' => get_field('email_error_message_story_form', 'options')
          );
          $this->setResponse();
        }

        if (!ST_FormValidation::validateRequired($this->request['phone'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'phone',
            'message' => get_field('phone_error_message_story_form', 'options')
          );
          $this->setResponse();
        }

        if (!ST_FormValidation::validateRequired($this->request['serviceBranch'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'serviceBranch',
            'message' => get_field('service_branch_error_message_story_form', 'options')
          );
          $this->setResponse();
        }

        if (!ST_FormValidation::validateRequired($this->request['serviceYears'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'serviceYears',
            'message' => get_field('service_years_error_message_story_form', 'options')
          );
          $this->setResponse();
        }

        if (!ST_FormValidation::validateRequired($this->request['storyContent'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'storyContent',
            'message' => get_field('story_content_error_message_story_form', 'options')
          );
          $this->setResponse();
        }

        if (!ST_FormValidation::validateRequired($this->request['image'])) {
          $this->response = array(
            'error'   => true,
            'target'  => 'image',
            'message' => get_field('image_error_message_story_form', 'options')
          );
          $this->setResponse();
        }

        break;
    }
  }
  
  protected function getFromEmail() {
    return ST_ThemeFunctions::getCredential(CredentialsList::NO_REPLY_EMAIL);
  }
  
  protected function getRequest($method = "GET", $secure = true) {
    if ($secure) {
      ST_ThemeFunctions::ajaxSecurity(true);
    }
    
    if ($_SERVER['REQUEST_METHOD'] != $method) {
      $this->response = array(
        'error'   => true,
        'message' => 'Method not allow'
      );
      
      $this->setResponse();
    }
    
    $this->request = ST_ThemeFunctions::getRequest($method);
  }
  
  protected function setResponse() {
    ST_ThemeFunctions::printResult($this->response);
  }
  
  public static function getApiName($class) {
    $path = explode('\\', $class);
    $string = preg_split('/(?=[A-Z])/', $path[sizeof($path) - 1], -1, PREG_SPLIT_NO_EMPTY);
    $apiName = '';
    foreach ($string as $item) {
      $apiName .= strtolower($item) . '-';
    }
    $apiName = str_replace('-api', '', $apiName);
    
    return $apiName . 'api';
  }
  
  public static function loadApiFile(array $array) {
    if (!empty($array)) {
      foreach ($array as $api) {
        $apiName = self::getApiName($api);
        
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $apiName . '.php')) {
          require_once __DIR__ . DIRECTORY_SEPARATOR . $apiName . '.php';
        }
      }
    }
    
    self::getChildrenClasses();
  }
}
