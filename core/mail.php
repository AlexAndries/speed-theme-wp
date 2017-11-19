<?php

namespace SpeedTheme\Core;

use SendGrid\Mail;
use SendGrid\Attachment;
use SendGrid\Personalization;
use SendGrid\Email;
use SendGrid\Content;

class ST_Mail {
  private $from;
  
  private $fromName;
  
  private $to;
  
  private $toName;
  
  private $subject;
  
  private $content;
  
  private $cc = false;
  
  private $apiKey;
  
  private $headers;
  
  private $contentType;
  
  private $attachments;
  
  public function __construct($options = array()) {
    $this->from = isset($options['from']) ? $options['from'] : ST_ThemeFunctions::getCredential(CredentialsList::NO_REPLY_EMAIL);
    $this->fromName = isset($options['fromName']) ? $options['fromName'] : null;
    $this->to = isset($options['to']) ? $options['to'] : null;
    $this->toName = isset($options['toName']) ? $options['toName'] : null;
    $this->subject = isset($options['subject']) ? $options['subject'] : null;
    $this->content = isset($options['content']) ? $options['content'] : null;
    $this->apiKey = get_field('sendgrid_api_key', 'options') && get_field('sendgrid_api_key', 'options') !== '' ? get_field('sendgrid_api_key', 'options') : SENDGRID_KEY;
    $this->headers = isset($options['headers']) ? $options['headers'] : array();
    $this->contentType = isset($options['contentType']) ? $options['contentType'] : 'text/html';
    $this->attachments = isset($options['attachments']) ? $options['attachments'] : array();
  }
  
  private function checkParameters() {
    if (!$this->to) {
      throw new \Exception('`to` parameter is missing!');
    }
    
    if (!$this->subject) {
      throw new \Exception('`subject` parameter is missing!');
    }
    
    if (!$this->content) {
      throw new \Exception('`content` parameter is missing!');
    }
  }
  
  private function addHeadersToMail(Mail $mail) {
    if (!empty($this->headers)) {
      foreach ($this->headers as $key => $value) {
        $mail->addHeader($key, $value);
      }
    }
  }
  
  private function setAttachment(Mail $mail) {
    if (!empty($this->attachments)) {
      foreach ($this->attachments as $file) {
        $attachment = new Attachment();
        $attachment->setContent(base64_encode($file['string']));
        $attachment->setType($file['fileType']);
        $attachment->setFilename($file['fileName']);
        $attachment->setDisposition("attachment");
        $mail->addAttachment($attachment);
      }
    }
  }
  
  private function addCC(Personalization $personalization) {
    if (!$this->cc) {
      return;
    }
    if (is_array($this->cc) && !empty($this->cc)) {
      foreach ($this->cc as $item) {
        $email = new Email(isset($item['name']) ? $item['name'] : null, $item['email']);
        $personalization->addCc($email);
      }
    } else {
      $email = new Email($this->cc['name'], $this->cc['email']);
      $personalization->addCc($email);
    }
  }
  
  private function setTos(Mail $mail) {
    $personalization = new Personalization();
    if (is_array($this->to) && !empty($this->to)) {
      foreach ($this->to as $emailItem) {
        $email = new Email(isset($emailItem['name']) ? $emailItem['name'] : null, $emailItem['email']);
        $personalization->addTo($email);
      }
    } else {
      $email = new Email($this->toName, $this->to);
      $personalization->addTo($email);
    }
    
    
    $this->addCC($personalization);
    $mail->addPersonalization($personalization);
  }
  
  /**
   * @return mixed|string
   */
  public function getFrom() {
    return $this->from;
  }
  
  /**
   * @param mixed|string $from
   *
   * @return ST_Mail
   */
  public function setFrom($from) {
    $this->from = $from;
    
    return $this;
  }
  
  /**
   * @return mixed|null
   */
  public function getFromName() {
    return $this->fromName;
  }
  
  /**
   * @param mixed|null $fromName
   *
   * @return ST_Mail
   */
  public function setFromName($fromName) {
    $this->fromName = $fromName;
    
    return $this;
  }
  
  /**
   * @return mixed|null
   */
  public function getTo() {
    return $this->to;
  }
  
  /**
   * @param mixed|null $to
   *
   * @return ST_Mail
   */
  public function setTo($to) {
    $this->to = $to;
    
    return $this;
  }
  
  /**
   * @return mixed|null
   */
  public function getToName() {
    return $this->toName;
  }
  
  /**
   * @param mixed|null $toName
   *
   * @return ST_Mail
   */
  public function setToName($toName) {
    $this->toName = $toName;
    
    return $this;
  }
  
  /**
   * @return mixed|null
   */
  public function getSubject() {
    return $this->subject;
  }
  
  /**
   * @param mixed|null $subject
   *
   * @return ST_Mail
   */
  public function setSubject($subject) {
    $this->subject = $subject;
    
    return $this;
  }
  
  /**
   * @return mixed|null
   */
  public function getContent() {
    return $this->content;
  }
  
  /**
   * @param mixed|null $content
   *
   * @return ST_Mail
   */
  public function setContent($content) {
    $this->content = $content;
    
    return $this;
  }
  
  /**
   * @return array|mixed
   */
  public function getHeaders() {
    return $this->headers;
  }
  
  /**
   * @param array|mixed $headers
   *
   * @return ST_Mail
   */
  public function setHeaders($headers) {
    $this->headers = $headers;
    
    return $this;
  }
  
  public function setHeader($headerName, $headerValue) {
    $this->headers[$headerName] = $headerValue;
    
    return $this;
  }
  
  /**
   * @return mixed|string
   */
  public function getContentType() {
    return $this->contentType;
  }
  
  /**
   * @param mixed|string $contentType
   *
   * @return ST_Mail
   */
  public function setContentType($contentType) {
    $this->contentType = $contentType;
    
    return $this;
  }
  
  /**
   * @return mixed|string
   */
  public function getAttachments() {
    return $this->attachments;
  }
  
  /**
   * @param mixed|string $attachment
   *
   * @return ST_Mail
   */
  public function addAttachments($attachment) {
    $this->attachments[] = $attachment;
    
    return $this;
  }
  
  /**
   * @return mixed
   */
  public function getCc() {
    return $this->cc;
  }
  
  /**
   * @param mixed $cc
   *
   * @return ST_Mail
   */
  public function setCc($cc) {
    $this->cc = $cc;
    
    return $this;
  }
  
  public function sendEmail() {
    $this->checkParameters();
    
    $from = new Email($this->fromName, $this->from);
    $content = new Content($this->contentType, $this->content);
    
    $mail = new Mail();
    $mail->setSubject($this->subject);
    $mail->setFrom($from);
    $mail->addContent($content);
    $this->setTos($mail);
    $this->addHeadersToMail($mail);
    $this->setAttachment($mail);
    
    $sendgrid = new \SendGrid($this->apiKey);
    
    try {
      $response = $sendgrid->client->mail()
                                   ->send()
                                   ->post($mail);
      
      return array(
        'status'  => $response->statusCode(),
        'message' => $response->body(),
        'headers' => $response->headers()
      );
      
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }
}
