<?php

namespace SpeedTheme\WpJson;

use SpeedTheme\Core\CredentialsList;
use SpeedTheme\Core\ST_Mail;
use SpeedTheme\Core\ST_SwiftMailerWrapper;
use SpeedTheme\Core\ST_ThemeFunctions;

class Wrapper {
  protected $response;
  
  /**
   * @param \WP_REST_Request $request
   *
   * @return array
   */
  private function getSort($request) {
    $sort = array();
    $items = explode(',', $request->get_param('sort'));
    $fields = $this->getArgs();
    if (!empty($items)) {
      foreach ($items as $item) {
        $order = substr($item, 0, 1);
        if ($order == '-') {
          $field = substr($item, 1);
          if (isset($fields[$field]) && isset($fields[$field]['use']) && in_array('sort', $fields[$field]['use'])) {
            $sort[$field] = 'DESC';
          }
        } else {
          if (isset($fields[$item]) && isset($fields[$item]['use']) && in_array('sort', $fields[$item]['use'])) {
            $sort[$item] = 'ASC';
          }
        }
      }
    }
    
    return $sort;
  }
  
  /**
   * @param \WP_REST_Request $request
   *
   * @return array
   */
  private function getPage($request) {
    $paging = array(
      'offset' => $request->get_param('offset'),
      'limit'  => $request->get_param('limit')
    );
    
    return $paging;
  }
  
  /**
   * @param \WP_REST_Request $request
   *
   * @return array
   */
  private function getFilter($request) {
    $filter = array();
    $fields = $this->getArgs();
    if (!empty($fields)) {
      foreach ($fields as $fieldName => $fieldOptions) {
        if (isset($fieldOptions['use']) && in_array('filter', $fieldOptions['use'])) {
          $filterField = $request->get_param($fieldName);
          $type = substr($filterField, 0, 1);
          
          switch ($type) {
            case '<':
            case '>':
              if (substr($filterField, 1, 1) == '=') {
                $filter[$fieldName] = array(
                  'value'   => substr($filterField, 2),
                  'compare' => $type . '='
                );
              } else {
                $filter[$fieldName] = array(
                  'value'   => substr($filterField, 1),
                  'compare' => $type
                );
              }
              break;
            case '!':
              $filter[$fieldName] = array(
                'value'   => substr($filterField, 1),
                'compare' => $type . '='
              );
              break;
            default:
              if (substr($filterField, 0, 4) == 'LIKE') {
                $filter[$fieldName] = array(
                  'value'   => substr($filterField, 4),
                  'compare' => 'LIKE'
                );
              } elseif (substr($filterField, 0, 8) == 'NOT LIKE') {
                $filter[$fieldName] = array(
                  'value'   => substr($filterField, 8),
                  'compare' => 'NOT LIKE'
                );
              } elseif (substr($filterField, 0, 2) == 'IN' && json_decode(substr($filterField, 2))) {
                $filter[$fieldName] = array(
                  'value'   => json_decode(substr($filterField, 2)),
                  'compare' => 'IN'
                );
              } elseif (substr($filterField, 0, 6) == 'NOT IN' && json_decode(substr($filterField, 6))) {
                $filter[$fieldName] = array(
                  'value'   => json_decode(substr($filterField, 6)),
                  'compare' => 'NOT IN'
                );
              } elseif (substr($filterField, 0, 7) == 'BETWEEN' && json_decode(substr($filterField, 7)) && sizeof(json_decode(substr($filterField, 7))) == 2) {
                $filter[$fieldName] = array(
                  'value'   => json_decode(substr($filterField, 7)),
                  'compare' => 'BETWEEN'
                );
              } elseif (substr($filterField, 0, 11) == 'NOT BETWEEN' && json_decode(substr($filterField, 11)) && sizeof(json_decode(substr($filterField, 11))) == 2) {
                $filter[$fieldName] = array(
                  'value'   => json_decode(substr($filterField, 11)),
                  'compare' => 'NOT BETWEEN'
                );
              } elseif ($filterField == 'NOT EXISTS') {
                $filter[$fieldName] = array(
                  'compare' => 'NOT EXISTS'
                );
              } else {
                $filter[$fieldName] = array(
                  'value'   => $filterField,
                  'compare' => '='
                );
              }
              break;
          }
        }
      }
    }
    
    return $filter;
  }
  
  /**
   * @param \WP_REST_Request $request
   *
   * @return array
   */
  private function getFields($request) {
    $fields = array();
    $items = explode(',', $request->get_param('fields'));
    $args = $this->getArgs();
    if (!empty($items)) {
      foreach ($items as $item) {
        if (isset($args[$item]) && isset($args[$item]['use']) && in_array('field', $args[$item]['use'])) {
          $fields[] = $item;
        }
      }
    }
    
    return $fields;
  }
  
  protected function setRestResponse($status = 200, $response = false) {
    $responseClient = new \WP_REST_Response();
    $responseClient->set_data($response ? $response : $this->response);
    $responseClient->set_status($status);
    
    return $responseClient;
  }
  
  /**
   * @param \WP_REST_Request $request
   * @param null             $sort
   * @param null             $page
   * @param null             $filter
   * @param null             $fields
   */
  protected function processRequest($request, &$sort = null, &$page = null, &$filter = null, &$fields = null) {
    $sort = $this->getSort($request);
    $page = $this->getPage($request);
    $filter = $this->getFilter($request);
    $fields = $this->getFields($request);
  }
  
  protected function secureMe($front = true) {
    if (IS_IN_DEVELOPMENT) {
      return true;
    }
    
    if ($front) {
      if (strpos($_SERVER['HTTP_REFERER'], SITE_URL) !== 0 && strpos(SITE_URL, $_SERVER['HTTP_REFERER']) !== 0) {
        return $this->setRestResponse(\WP_Http::FORBIDDEN, array('message' => 'Access Forbidden!'));
      }
    } else {
      if (strpos($_SERVER['HTTP_REFERER'], SITE_URL . 'wp-admin/') !== 0 && strpos(SITE_URL . 'wp-admin/', $_SERVER['HTTP_REFERER']) !== 0) {
        return $this->setRestResponse(\WP_Http::FORBIDDEN, array('message' => 'Access Forbidden!'));
      }
    }
    
    return true;
  }
  
  public function getArgs() {
    /**
     * used to pull child args
     */
    return array();
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
  
  protected function getFromEmail() {
    return ST_ThemeFunctions::getCredential(CredentialsList::NO_REPLY_EMAIL);
  }
  
}
