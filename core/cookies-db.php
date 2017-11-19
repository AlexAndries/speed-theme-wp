<?php
namespace SpeedTheme\Core;

class ST_CookiesDb {
  
  private $acceptCookie = true;
  
  private $userIP;
  
  public function __construct() {
    $this->getUserIP();
    $this->checkIfUserAcceptCookies();
    
    add_action('wp_ajax_user_blocking_cookies_check', array($this, 'userBlockingCookieAjaxCheck'));
    
    add_action('wp_ajax_nopriv_user_blocking_cookies_check', array($this, 'userBlockingCookieAjaxCheck'));
  }
  
  public static function createCookiesTable() {
    global $wpdb;
    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cookies(
			id int PRIMARY KEY AUTO_INCREMENT,
			ip VARCHAR(30),
			cookieName VARCHAR(255),
			cookieValue VARCHAR(255),
			expire TIMESTAMP
		)");
  }
  
  private function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $this->userIP = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $this->userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
      $this->userIP = $_SERVER['REMOTE_ADDR'];
    } else {
      $this->userIP = false;
    }
  }
  
  private function checkIfUserAcceptCookies() {
    global $wpdb;
    $result = $wpdb->get_var("SELECT cookieValue FROM {$wpdb->prefix}cookies WHERE ip='{$this->userIP}' AND cookieName='accept-cookie'");
    
    if ($result != null) {
      $this->acceptCookie = false;
    }
  }
  
  private function getCookieDB($cookieName) {
    global $wpdb;
    $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cookies WHERE ip='{$this->userIP}' AND cookieName='{$cookieName}'");
    if ($result) {
      $currentTime = new \DateTime(current_time('mysql'));
      $cookieExpire = new \DateTime($result->expire);
      if ($currentTime < $cookieExpire) {
        return $result->cookieValue;
      } else {
        $this->deleteCookieDB($cookieName);
      }
    }
    
    return false;
  }
  
  private function deleteCookieDB($cookieName) {
    global $wpdb;
    $wpdb->delete($wpdb->prefix . 'cookies', array('cookieName' => $cookieName, 'ip' => $this->userIP));
  }
  
  private function deleteUserAsCookieBlocking() {
    if (!$this->acceptCookie) {
      $this->deleteCookieDB('accept-cookie');
      $this->acceptCookie = true;
    }
  }
  
  private function registerUserAsCookieBlocking() {
    if ($this->acceptCookie) {
      $this->insertCookieDB('accept-cookie', 'false', time() + (3600 * 24));
    }
  }
  
  private function insertCookieDB($cookieName, $cookieValue, $expire) {
    global $wpdb;
    $wpdb->insert($wpdb->prefix . 'cookies', array(
      'ip'          => $this->userIP,
      'cookieName'  => $cookieName,
      'cookieValue' => $cookieValue,
      'expire'      => date('Y-m-d G:i:s', $expire)
    ));
  }
  
  private function checkifCookieExists($cookieName) {
    global $wpdb;
    
    return $wpdb->get_var("SELECT expire FROM {$wpdb->prefix}cookies WHERE ip='{$this->userIP}' AND cookieName='{$cookieName}'");
  }
  
  private function updateCookieDB($cookieName, $cookieValue, $expire) {
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'cookies', array(
      'cookieValue' => $cookieValue,
      'expire'      => date('Y-m-d G:i:s', $expire)
    ), array(
      'ip'         => $this->userIP,
      'cookieName' => $cookieName
    ));
  }
  
  public function getCookie($cookieName) {
    if ($this->acceptCookie) {
      if (isset($_COOKIE[$cookieName])) {
        return $_COOKIE[$cookieName];
      }
      
      return false;
    } else {
      return $this->getCookieDB($cookieName);
    }
  }
  
  public function removeCookie($cookieName) {
    if ($this->acceptCookie) {
      if (isset($_COOKIE[$cookieName])) {
        unset ($_COOKIE[$cookieName]);
      }
    } else {
      $this->deleteCookieDB($cookieName);
    }
  }
  
  public function userBlockingCookieAjaxCheck() {
    if (isset($_POST['cookie_accept'])) {
      $this->deleteUserAsCookieBlocking();
    } else {
      $this->registerUserAsCookieBlocking();
    }
    $this->setCookie('loading', ST_Core::getVersionNumber(), time() + (3600 * 24 * 365));
    echo time() + (3600 * 24 * 365);
    wp_die();
  }
  
  public function setCookie($cookieName, $cookieValue, $expire) {
    if ($this->acceptCookie) {
      setcookie($cookieName, $cookieValue, $expire, '/');
      $_COOKIE[$cookieName] = $cookieValue;
    } else {
      if ($cookieExpire = $this->checkifCookieExists($cookieName)) {
        $this->updateCookieDB($cookieName, $cookieValue, $expire);
      } else {
        $this->insertCookieDB($cookieName, $cookieValue, $expire);
      };
    }
  }
}
