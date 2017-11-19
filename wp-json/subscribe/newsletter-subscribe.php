<?php

namespace SpeedTheme\WpJson\Subscribe;

use SpeedTheme\Core\CredentialsList;
use SpeedTheme\Core\ST_MailChimpSubscribe;
use SpeedTheme\Core\ST_ThemeFunctions;
use SpeedTheme\WpJson\Wrapper;
use SpeedTheme\WpJson\InterfaceLoad;
use SpeedTheme\WpJson\RestSections;

class NewsletterSubscribe extends Wrapper implements InterfaceLoad {

  /**
   * @var \WP_REST_Server
   */
  private $method = \WP_REST_Server::CREATABLE;

  private $route = '/newsletter-subscribe';

  private $blockName = RestSections::SUBSCRIBE;

  private $args = array(
    'email' => array(
      'description'       => 'Email Address',
      'type'              => 'string',
      'required'          => true,
      'sanitize_callback' => 'sanitize_text_field',
      'validate_callback' => 'SpeedTheme\WpJson\ValidationManager::emailValidation'
    )
  );

  private function processData(\WP_REST_Request $request) {
    try {
      $mailChimp = new ST_MailChimpSubscribe(ST_ThemeFunctions::getCredential(CredentialsList::MAILCHIMP_API_KEY));
    
      $response = $mailChimp->setListId(ST_ThemeFunctions::getCredential(CredentialsList::MAILCHIMP_LIST_ID))
                            ->setEmail($request->get_param('email'))
                            ->subscribe(true);
    
      if ($response !== true) {
        throw new \Exception($response, 500);
      }
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage(), $e->getCode());
    }
  }

  public function getMethod() {
    return $this->method;
  }

  public function getRoute() {
    return $this->route;
  }

  public function getBlockName() {
    return $this->blockName;
  }

  public function acceptRequest(\WP_REST_Request $request) {
    $access = $this->secureMe();
    if ($access !== true) {
      return $access;
    }

    try {
      $this->processData($request);
      $this->response = array(
        'code' => 'success'
      );
      return $this->setRestResponse();
    } catch (\Exception $e) {
      return $this->setRestResponse($e->getCode(), array(
        'code'    => 'general_api_error',
        'message' => $e->getMessage()
      ));
    }
  }

  public function getArgs() {
    return $this->args;
  }
}
