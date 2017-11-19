<?php

namespace SpeedTheme\Core;

class ST_ThemeFunctions {
  
  public static function getCredential($name) {
    switch ($name) {
      case CredentialsList::GOOGLE_MAPS_API:
        return get_field('google_maps_api', 'options') ? get_field('google_maps_api', 'options') : GOOGLE_MAPS_API;
        break;
      case CredentialsList::NO_REPLY_EMAIL:
        return get_field('email_no_reply_email', 'options') ? get_field('email_no_reply_email', 'options') : NO_REPLY_EMAIL;
        break;
      case CredentialsList::MAILCHIMP_API_KEY:
        return get_field('mailchimp_api_key', 'options') ? get_field('mailchimp_api_key', 'options') : MAILCHIMP_API_KEY;
        break;
      case CredentialsList::MAILCHIMP_LIST_ID:
        return get_field('mailchimp_list_id', 'options') ? get_field('mailchimp_list_id', 'options') : MAILCHIMP_LIST_ID;
        break;
      case CredentialsList::SHOPIFY_API_KEY:
        return get_field('shopify_api_key', 'options') ? get_field('shopify_api_key', 'options') : SHOPIFY_API_KEY;
        break;
      case CredentialsList::SHOPIFY_API_PASSWORD:
        return get_field('shopify_api_password', 'options') ? get_field('shopify_api_password', 'options') : SHOPIFY_API_PASSWORD;
        break;
      case CredentialsList::SHOPIFY_HOST_NAME:
        return get_field('shopify_host_name', 'options') ? get_field('shopify_host_name', 'options') : SHOPIFY_HOST_NAME;
        break;
      case CredentialsList::SHOPIFY_APP_ID:
        return get_field('shopify_app_id', 'options') ? get_field('shopify_app_id', 'options') : SHOPIFY_APP_ID;
        break;
      case CredentialsList::SHOPIFY_FRONT_KEY:
        return get_field('shopify_front_key', 'options') ? get_field('shopify_front_key', 'options') : SHOPIFY_FRONT_KEY;
        break;
    }
  }
  
  public static function getFeaturedImage($postId, $size = 'medium') {
    $image = wp_get_attachment_image_src(get_post_thumbnail_id($postId), $size);
    
    if (is_array($image)) {
      $image[4] = self::getAltForImage($postId);
    }
    
    return $image;
  }
  
  public static function getPageUrl($slug) {
    return get_permalink(get_page_by_path($slug));
  }
  
  public static function addThemeSupport($item) {
    add_theme_support($item);
  }
  
  public static function ajaxSecurity($front = false) {
    if ($front) {
      if (strpos($_SERVER['HTTP_REFERER'], SITE_URL) !== 0 && strpos(SITE_URL, $_SERVER['HTTP_REFERER']) !== 0) {
        self::printResult(array(
          'error'   => true,
          'message' => 'Access Denied!'
        ));
      }
    } else {
      if (strpos($_SERVER['HTTP_REFERER'], SITE_URL . 'wp-admin/') !== 0 && strpos(SITE_URL . 'wp-admin/', $_SERVER['HTTP_REFERER']) !== 0) {
        self::printResult(array(
          'error'   => true,
          'message' => 'Access Denied!'
        ));
      }
    }
  }
  
  public static function printResult($array, $return = false) {
    if ($return) {
      return $array;
    }
    print_r(json_encode($array));
    
    wp_die();
  }
  
  public static function getRequest($method = 'GET') {
    switch ($method) {
      case 'GET':
        return $_GET;
        break;
      case 'POST':
        $request = fopen("php://input", "r");
        $putData = '';
        while ($data = fread($request, 1024)) {
          $putData .= $data;
        }
        
        fclose($request);
        
        return json_decode($putData, true);
        break;
      case 'PUT':
      case 'DELETE':
        parse_str(file_get_contents("php://input"), $post_vars);
        
        return $post_vars;
        
        break;
    }
    
    return array();
  }
  
  public static function displayBackgroundImage($target, $fieldName = false) {
    $imageUrl = false;
    if ($fieldName && get_field($fieldName, $target)) {
      $imageUrl = get_field($fieldName, $target);
    } else {
      $image = self::getFeaturedImage($target, 'full');
      if (is_array($image)) {
        $imageUrl = $image[0];
      }
    }
    
    if ($imageUrl) {
      echo 'style="background-image: url(' . $imageUrl . ')"';
    }
  }
  
  public static function getAltForImage($postID = false) {
    if (!$postID) {
      return false;
    }
    
    $imageID = get_post_thumbnail_id($postID);
    
    if (get_post_meta($imageID, '_wp_attachment_image_alt', true)) {
      return get_post_meta($imageID, '_wp_attachment_image_alt', true);
    }
    
    $attach = get_post($imageID);
    
    return $attach->post_title;
  }
  
  public static function getRelatedArticles($postTypes = array(), $postId, $tagTaxonomy = false) {
    $tags = array();
    $args = array(
      'post_type'      => $postTypes,
      'posts_per_page' => 2,
      'post__not_in'   => array($postId)
    );
    
    if ($tagTaxonomy) {
      $tags = wp_get_post_terms($postId, $tagTaxonomy);
    }
    
    if (!empty($tags)) {
      $tagsArray = array();
      
      foreach ($tags as $tag) {
        $tagsArray[] = $tag->term_id;
      }
      
      $args['tax_query'] = array(
        array(
          'taxonomy' => $tagTaxonomy,
          'field'    => 'term_id',
          'terms'    => $tagsArray,
        )
      );
    }
    
    $posts = new \WP_Query($args);
    $postsID = array();
    
    while ($posts->have_posts()) {
      $posts->the_post();
      $postsID[] = get_the_ID();
    }
    
    wp_reset_postdata();
    
    if (!empty($postsID)) {
      return $postsID;
    }
    
    return self::getRelatedArticles($postTypes, $postsID);
  }
  
  public static function returnEmailLink($email, $label = false) {
    if (!$label) {
      $label = $email;
    }
    
    return '<a href="mailto:' . $email . '">' . $label . '</a>';
  }
  
  public static function returnPhoneLink($phone) {
    $phone = str_replace(' ', '', $phone);
    $phone = str_replace(')', '', $phone);
    $phone = str_replace('(', '', $phone);
    $phone = str_replace('-', '', $phone);
    $phone = str_replace('.', '', $phone);
    
    return str_replace('/', '', $phone);
  }
  
  public static function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
      return 'n-a';
    }
    
    return $text;
  }
  
  public static function getCopyright($string) {
    $string = str_replace('{copy-sign}', '&copy;', $string);
    
    return str_replace('{year}', date('Y'), $string);
  }
  
  public static function generateID($size = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $size; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
  }
  
  public static function isSafari() {
    return stripos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false;
  }
  
  public static function getImageURL($imageURL) {
    if (defined('CDN_ACTIVE') && CDN_ACTIVE) {
      return str_replace(home_url('/'), CDN_CNAME, $imageURL);
    }
    
    return $imageURL;
  }
  
  public static function acfImage($image, $size = false, $classes = '') {
    if (empty($image)) {
      return;
    }
    
    if ($size) { ?>
      <img src="<?php echo $image['sizes'][$size]; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['sizes'][$size . '-width']; ?>"
           height="<?php echo $image['sizes'][$size . '-height']; ?>" class="img-responsive <?php echo $classes ?>">
    <?php } else { ?>
      <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $image['width']; ?>"
           height="<?php echo $image['height']; ?>" class="img-responsive <?php echo $classes ?>">
      <?php
    }
  }
}
