<?php

namespace SpeedTheme\Core;

use DrewM\MailChimp\MailChimp;

class ST_MailChimpSubscribe {
  /**
   * @var string
   */
  private $email;
  
  /**
   * @var array
   */
  private $mergeFields;
  
  /**
   * @var string
   */
  private $listId;
  
  /**
   * @var \DrewM\MailChimp\MailChimp
   */
  private $client;
  
  /**
   * @var array
   */
  private $data;
  
  public function __construct($apiKey) {
    $this->client = new MailChimp($apiKey);
    $this->client->verify_ssl = false;
  }
  
  private function buildData() {
    $this->data = array(
      'email_address' => $this->email,
      'status'        => "subscribed"
    );
    
    if (isset($this->mergeFields) && is_array($this->mergeFields) && !empty($this->mergeFields)) {
      $this->data['merge_fields'] = $this->mergeFields;
    }
  }
  
  private function updateSubscriber() {
    $subscriberHash = $this->client->subscriberHash($this->email);
    if ($subscriberHash) {
      $this->client->patch("lists/" . $this->listId . "/members/" . $subscriberHash, $this->data);
      
      if (!$this->client->success()) {
        return $this->client->getLastResponse();
      }
    }
    
    return true;
  }
  
  /**
   * @return string
   */
  public function getEmail() {
    return $this->email;
  }
  
  /**
   * @param string $email
   *
   * @return ST_MailChimpSubscribe
   */
  public function setEmail($email) {
    $this->email = $email;
    
    return $this;
  }
  
  /**
   * @return array
   */
  public function getMergeFields() {
    return $this->mergeFields;
  }
  
  /**
   * @param array $mergeFields
   *
   * @return ST_MailChimpSubscribe
   */
  public function setMergeFields($mergeFields) {
    $this->mergeFields = $mergeFields;
    
    return $this;
  }
  
  /**
   * @return string
   */
  public function getListId() {
    return $this->listId;
  }
  
  /**
   * @param string $listId
   *
   * @return ST_MailChimpSubscribe
   */
  public function setListId($listId) {
    $this->listId = $listId;
    
    return $this;
  }
  
  public function subscribe($update = false) {
    if (!isset($this->listId)) {
      throw new \Exception('MailChimp List ID is required');
    }
    
    if (!isset($this->email)) {
      throw new \Exception('Email is required');
    }
    
    $this->buildData();
    
    $this->client->post("lists/" . $this->listId . "/members", $this->data);
    
    if (!$this->client->success()) {
      $error = $this->client->getLastResponse();
      
      if ($error['headers']['http_code'] == 400) {
        if ($update) {
          return $this->updateSubscriber();
        }
      }
    }
    
    return true;
    
  }
}
