<?php

namespace SpeedTheme\Admin\Exporting;

use SpeedTheme\Core\ST_ThemeFunctions;

class Handler {
  private $postType;
  
  public function __construct($postType) {
    $this->postType = $postType;
  }
  
  private function prepareExportData(&$data = array()) {
    $query = array(
      'post_type'      => $this->postType,
      'posts_per_page' => -1
    );
    
    $posts = new \WP_Query($query);
    
    while ($posts->have_posts()) {
      $posts->the_post();
      $info = array(
        'ID'      => get_the_ID(),
        'title'   => get_the_title(get_the_ID()),
        'content' => get_the_content(get_the_ID()),
        'status'  => get_post_status(get_the_ID())
      );
      
      if ($image = ST_ThemeFunctions::getFeaturedImage(get_the_ID(), 'full')) {
        $info['featuredImage'] = $image[0];
      }
      
      $metas = get_post_meta(get_the_ID());
      
      if (!empty($metas)) {
        foreach ($metas as $key => $meta) {
          if (strpos($key, '_') === false && !get_field_object($key, get_the_ID())) {
            $info[$key] = $meta[0];
          }
        }
      }
      
      $fieldsGroup = acf_get_field_groups(array('post_id' => get_the_ID()));
      if (!empty($fieldsGroup)) {
        foreach ($fieldsGroup as $fieldGroup) {
          $fields = acf_get_fields($fieldGroup['key']);
          
          if (!empty($fields)) {
            foreach ($fields as $field) {
              $fieldPost = get_field_object($field['name'], get_the_ID());
              $info[$field['name']] = $this->exportAcfFieldsHandler($fieldPost);
              
              if (is_array($info[$field['name']])) {
                $info[$field['name']] = json_encode($info[$field['name']]);
              }
            }
          }
        }
      }
      
      $data[] = $info;
    }
    
    wp_reset_postdata();
    wp_reset_query();
  }
  
  private function exportAcfFieldsHandler($field) {
    switch ($field['type']) {
      case 'image':
      case 'file':
        if ($field['value']) {
          return $field['value']['url'];
        }
        break;
      case 'gallery':
        $values = array();
        if (!empty($field['value'])) {
          foreach ($field['value'] as $image) {
            $values[] = $image['url'];
          }
        }
        
        return implode(',', $values);
        break;
      
      case 'post_object':
        if ($field['value']) {
          return $field['value']->ID;
        }
        break;
      case 'relationship':
        $values = array();
        if (!empty($field['value'])) {
          foreach ($field['value'] as $item) {
            $values[] = $item->ID;
          }
        }
        
        return implode(',', $values);
        break;
      case 'taxonomy':
        $values = array();
        if (!empty($field['value'])) {
          foreach ($field['value'] as $item) {
            $values[] = $item->term_id;
          }
        }
        
        return implode(',', $values);
        break;
      case 'user':
        if ($field['value']) {
          return $field['value']['user_email'];
        }
        break;
      case 'checkbox':
      case 'select':
        if (is_array($field['value'])) {
          return implode(',', $field['value']);
        }
        
        return $field['value'];
        break;
      case 'true_false':
        return $field['value'] ? 'Yes' : 'No';
        break;
      case 'google_map':
        return $field['value'];
        break;
      case 'repeater':
        $values = array();
        $subFields = $field['sub_fields'];
        if (!empty($field['value'])) {
          foreach ($field['value'] as $items) {
            if (!empty($items)) {
              $i = 0;
              foreach ($items as $index => $item) {
                $subFields[$i]['value'] = $item;
                $values[$subFields[$i]['label']] = $this->exportAcfFieldsHandler($subFields[$i]);
                $i++;
              }
            }
          }
        }
        
        return array($values);
        break;
      
      default:
        return $field['value'];
        break;
    }
  }
  
  private function getImage($postId, $url) {
    $destination = ABSPATH . 'wp-content/uploads/import/';
    
    if (!file_exists($destination)) {
      mkdir($destination);
    }
    
    $ext = explode('.', $url);
    $ext = $ext[sizeof($ext) - 1];
    $title = ST_ThemeFunctions::slugify(get_the_title($postId)) . '.' . $ext;
    
    file_put_contents($destination . $title, file_get_contents($url));
    $siteUrl = get_option('siteurl');
    $file_info = getimagesize($destination . $title);
    
    $data = array(
      'post_author'       => 1,
      'post_date'         => current_time('mysql'),
      'post_date_gmt'     => current_time('mysql'),
      'post_title'        => $title,
      'post_status'       => 'inherit',
      'comment_status'    => 'closed',
      'ping_status'       => 'closed',
      'post_name'         => get_the_title($postId),
      'post_modified'     => current_time('mysql'),
      'post_modified_gmt' => current_time('mysql'),
      'post_parent'       => $postId,
      'post_type'         => 'attachment',
      'guid'              => $siteUrl . '/wp-content/uploads/feeds/' . $title,
      'post_mime_type'    => $file_info['mime'],
      'post_excerpt'      => '',
      'post_content'      => ''
    );
    
    $attachId = wp_insert_attachment($data, $destination . $title, $postId);
    
    if ($attachData = wp_generate_attachment_metadata($attachId, $destination . $title)) {
      wp_update_attachment_metadata($attachId, $attachData);
    }
    
    return $attachId;
  }
  
  private function parseCSV($file) {
    if (($fileHandler = fopen($file, 'r')) === false) {
      throw new \Exception('File reader error');
    }
    
    $data = array();
    $headers = array();
    while ($row = fgetcsv($fileHandler, 10000000)) {
      if (sizeof($headers) === 0) {
        $headers = $row;
      } else {
        $info = array();
        for ($i = 0; $i < sizeof($row); $i++) {
          $info[$headers[$i]] = $row[$i];
          
          if ($json = json_decode($info[$headers[$i]], true)) {
            $info[$headers[$i]] = $json;
          }
        }
        
        $data[] = $info;
      }
    }
    fclose($fileHandler);
    
    return $data;
  }
  
  private function getFileByUrl($url) {
    
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url));
    
    return $attachment[0];
  }
  
  private function importData($data) {
    if (empty($data)) {
      throw new \Exception('No data found in csv file, please make sure you use the exported template');
    }
    
    foreach ($data as $item) {
      $itemData = $item;
      $args = array(
        'post_content' => @$itemData['content'],
        'post_title'   => @$itemData['title'],
        'post_type'    => $this->postType,
        'post_status'  => $itemData['status']
      );
      
      unset($itemData['content']);
      unset($itemData['title']);
      unset($itemData['status']);
      
      if ($item['ID'] && get_post($item['ID'])) {
        $args['ID'] = $itemData['ID'];
      }
      
      unset($itemData['ID']);
      $postId = wp_insert_post($args);
      
      if (!$postId) {
        throw new \Exception($postId->get_error_message());
      }
      
      if ($itemData['featuredImage']) {
        $featuredImage = $this->getFileByUrl($itemData['featuredImage']);
        if ($featuredImage) {
          set_post_thumbnail($postId, $featuredImage);
        } else {
          set_post_thumbnail($postId, $this->getImage($postId, $itemData['featuredImage']));
        }
        unset($itemData['featuredImage']);
      }
      
      $fieldsGroup = acf_get_field_groups(array('post_id' => $postId));
      if (!empty($fieldsGroup)) {
        foreach ($fieldsGroup as $fieldGroup) {
          $fields = acf_get_fields($fieldGroup['key']);
          
          if (!empty($fields)) {
            foreach ($fields as $field) {
              $value = $this->importAcfFieldsHandler($field, $itemData[$field['name']], $postId);
              
              update_field($field['name'], $value, $postId);
              
              unset($itemData[$field['name']]);
            }
          }
        }
      }
      
      if (!empty($itemData)) {
        foreach ($itemData as $key => $value) {
          update_post_meta($postId, $key, $value);
        }
      }
    }
  }
  
  private function importAcfFieldsHandler($field, $value, $postId) {
    switch ($field['type']) {
      case 'true_false':
        return $value === 'Yes' ? 1 : 0;
        break;
      case 'image':
      case 'file':
        $file = $this->getFileByUrl($value);
        if ($file) {
          return $file;
        } else {
          return $this->getImage($postId, $value);
        }
        break;
      case 'gallery':
        $files = explode(',', $value);
        if (!empty($files)) {
          $ids = array();
          foreach ($files as $file) {
            $fileId = $this->getFileByUrl($file);
            if ($fileId) {
              $ids[] = $fileId;
            } else {
              $ids[] = $this->getImage($postId, $file);
            }
          }
          
          return $ids;
        }
        break;
      case 'select':
      case 'checkbox':
        return explode(',', $value);
        break;
      case 'relationship':
      case 'taxonomy':
        $items = explode(',', $value);
        if (!empty($items)) {
          return $items;
        }
        break;
      case 'user':
        $user = get_user_by('email', $value);
        if ($user) {
          return $user->ID;
        }
        break;
      case 'repeater':
        $values = array();
        $subFields = $field['sub_fields'];
        if (!empty($value)) {
          foreach ($value as $items) {
            if (!empty($items)) {
              $i = 0;
              foreach ($items as $index => $item) {
                $subFields[$i]['value'] = $item;
                $values[$subFields[$i]['name']] = $this->importAcfFieldsHandler($subFields[$i], $item, $postId);
                $i++;
              }
            }
          }
        }
        
        return $values;
        break;
      default:
        return $value;
        break;
    }
  }
  
  public function export() {
    set_time_limit(0);
    ignore_user_abort(true);
    
    $this->prepareExportData($data);
    
    if (!empty($data)) {
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment; filename=export-' . $this->postType . '-' . date('mdY') . '.csv');
      
      $output = fopen('php://output', 'w');
      $keys = array_keys($data[0]);
      fputcsv($output, $keys);
      
      foreach ($data as $row) {
        fputcsv($output, $row);
      }
      
      fclose($output);
      exit();
    }
    
    return false;
  }
  
  public function import($file) {
    if (!$file || !$file['name']) {
      throw new \Exception('File is missing!');
    }
    set_time_limit(0);
    ignore_user_abort(true);
    
    $fileName = strtotime(date(DATE_ISO8601)) . '-' . $file['name'];
    move_uploaded_file($file["tmp_name"], __DIR__ . "/import-files/" . $fileName);
    
    $data = $this->parseCSV(__DIR__ . "/import-files/" . $fileName);
    $this->importData($data);
    
    unlink(__DIR__ . "/import-files/" . $fileName);
  }
}
