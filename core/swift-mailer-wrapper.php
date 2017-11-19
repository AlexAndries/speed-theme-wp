<?php

namespace SpeedTheme\Core;

class ST_SwiftMailerWrapper {
  private $from;
  
  private $fromName;
  
  private $to;
  
  private $toName;
  
  private $subject;
  
  private $content;
  
  private $cc = false;
  
  private $headers;
  
  private $contentType;
  
  private $attachments;
  
  private $client;
  
  public function __construct($options = array(), array $access = array()) {
    if (!isset($access['host'])) {
      $access['host'] = get_field('smtp_host', 'options') ? get_field('smtp_host', 'options') : SMTP_HOST;
    }
    if (!isset($access['port'])) {
      $access['port'] = get_field('smtp_port', 'options') ? get_field('smtp_port', 'options') : SMTP_PORT;
    }
    if (!isset($access['user'])) {
      $access['user'] = get_field('smtp_user', 'options') ? get_field('smtp_user', 'options') : SMTP_USER;
    }
    if (!isset($access['password'])) {
      $access['password'] = get_field('smtp_password', 'options') ? get_field('smtp_password', 'options') : SMTP_PASSWORD;
    }
    if (!isset($access['security'])) {
      $access['security'] = get_field('smtp_security', 'options') ? get_field('smtp_security', 'options') : SMTP_SECURITY;
    }
    
    $transport = new \Swift_SmtpTransport($access['host'], $access['port'], $access['security']);
    $transport->setUsername($access['user'])
              ->setPassword($access['password']);
    
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'off') {
      $opt = array(
        'ssl' => array(
          'verify_peer'      => false,
          'verify_peer_name' => false
        )
      );
      
      $transport->setStreamOptions($opt);
    }
    
    $this->client = new \Swift_Mailer($transport);
    
    $this->from = isset($options['from']) ? trim($options['from']) : NO_REPLY_EMAIL;
    $this->fromName = isset($options['fromName']) ? trim($options['fromName']) : null;
    $this->to = isset($options['to']) ? trim($options['to']) : null;
    $this->toName = isset($options['toName']) ? trim($options['toName']) : null;
    $this->subject = isset($options['subject']) ? trim($options['subject']) : null;
    $this->content = isset($options['content']) ? $options['content'] : null;
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
  
  private function setAttachments(\Swift_Message $message) {
    if (!empty($this->attachments)) {
      foreach ($this->attachments as $file) {
        $attachment = new \Swift_Attachment();
        $attachment->setFilename($file['fileName'])
                   ->setContentType($file['fileType'])
                   ->setBody($file['string']);
        
        $message->attach($attachment);
      }
    }
  }
  
  private function setTos(\Swift_Message $message) {
    $tos = array();
    if (is_array($this->to) && !empty($this->to)) {
      foreach ($this->to as $emailItem) {
        if (isset($emailItem['name'])) {
          $tos[$emailItem['email']] = $emailItem['name'];
        } else {
          $tos[] = $emailItem['email'];
        }
      }
    } else {
      if (isset($this->toName) && $this->toName !== "") {
        $tos[$this->to] = $this->toName;
      } else {
        $tos = $this->to;
      }
    }
    
    $message->setTo($tos);
  }
  
  private function setCcs(\Swift_Message $message) {
    $ccs = array();
    if (is_array($this->cc) && !empty($this->cc)) {
      foreach ($this->cc as $emailItem) {
        if (isset($emailItem['name'])) {
          $ccs[$emailItem['email']] = $emailItem['name'];
        } else {
          $ccs[] = $emailItem['email'];
        }
      }
    } else {
      if ($this->cc) {
        if (isset($this->toName) && $this->toName !== "") {
          $ccs[$this->to] = $this->toName;
        } else {
          $ccs = $this->to;
        }
      }
    }
    
    $message->setCc($ccs);
  }
  
  /**
   * @param mixed|string $from
   *
   * @return ST_SwiftMailerWrapper
   */
  public function setFrom($from) {
    $this->from = $from;
    
    return $this;
  }
  
  /**
   * @param mixed|null $fromName
   *
   * @return ST_SwiftMailerWrapper
   */
  public function setFromName($fromName) {
    $this->fromName = $fromName;
    
    return $this;
  }
  
  /**
   * @param mixed|null $to
   *
   * @return ST_SwiftMailerWrapper
   */
  public function setTo($to) {
    $this->to = $to;
    
    return $this;
  }
  
  /**
   * @param mixed|null $toName
   *
   * @return ST_SwiftMailerWrapper
   */
  public function setToName($toName) {
    $this->toName = $toName;
    
    return $this;
  }
  
  /**
   * @param mixed|null $subject
   *
   * @return ST_SwiftMailerWrapper
   */
  public function setSubject($subject) {
    $this->subject = $subject;
    
    return $this;
  }
  
  /**
   * @param mixed|null $content
   *
   * @return ST_SwiftMailerWrapper
   */
  public function setContent($content) {
    $this->content = $content;
    
    return $this;
  }
  
  /**
   * @param bool|array $cc
   *
   * @return ST_SwiftMailerWrapper
   */
  public function setCc($cc) {
    $this->cc = $cc;
    
    return $this;
  }
  
  /**
   * @param $headerName
   * @param $headerValue
   *
   * @return $this
   */
  public function setHeader($headerName, $headerValue) {
    $this->headers[$headerName] = $headerValue;
    
    return $this;
  }
  
  /**
   * @param mixed|string $contentType
   *
   * @return ST_SwiftMailerWrapper
   */
  public function setContentType($contentType) {
    $this->contentType = $contentType;
    
    return $this;
  }
  
  /**
   * @param array|mixed $attachments
   *
   * @return ST_SwiftMailerWrapper
   */
  public function setAttachment($attachments) {
    $this->attachments[] = $attachments;
    
    return $this;
  }
  
  public function sendEmail() {
    $this->checkParameters();
    
    $message = new \Swift_Message();
    $message->setFrom(array($this->from => $this->fromName))
            ->setSubject($this->subject)
            ->setBody($this->content, $this->contentType);
    
    try {
      $this->setTos($message);
      $this->setCcs($message);
      $this->setAttachments($message);
      
      return $this->client->send($message);
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }
}
