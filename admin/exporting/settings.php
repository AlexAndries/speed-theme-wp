<?php

namespace SpeedTheme\Admin\Exporting;

class Settings {
  const OPTIONS = 'export/import-handler';
  
  private $options = array();
  
  public function __construct() {
    add_action('admin_menu', array($this, 'addSettingPage'));
    $this->loadExportImportPages();
  }
  
  private function loadExportImportPages() {
    $options = self::getStaticOptions();
    if ($options) {
      foreach ($options as $slug => $option) {
        if ($option['export']) {
          add_action('admin_menu', function () use ($slug) {
            if (isset($_POST['export-' . $slug . '-submit'])) {
              $handler = new Handler($slug);
              if (!$handler->export()) {
                add_action('admin_notices', array($this, 'errorExportSettings'));
              }
            }
            
            add_submenu_page('edit.php?post_type=' . $slug, 'Export', 'Export', 'manage_options', 'export-' . $slug . '-handler', array(
              $this,
              'exportSettingPage'
            ));
          });
        }
        
        if ($option['import']) {
          add_action('admin_menu', function () use ($slug) {
            if (isset($_POST['import-' . $slug . '-submit'])) {
              $handler = new Handler($slug);
              try {
                $handler->import($_FILES['file']);
                add_action('admin_notices', array($this, 'successImportSettings'));
              } catch (\Exception $e) {
                add_action('admin_notices', function () use ($e) {
                  ?>
                  <div id="message" class="notice notice-error is-dismissible">
                    <p><?php echo $e->getMessage(); ?></p>
                  </div>
                  <?php
                });
              }
            }
            
            add_submenu_page('edit.php?post_type=' . $slug, 'Import', 'Import', 'manage_options', 'import-' . $slug . '-handler', array(
              $this,
              'importSettingPage'
            ));
          });
        }
      }
    }
  }
  
  private function saveOptions() {
    update_option(self::OPTIONS, json_encode($this->options));
  }
  
  private function buildSaveData($rawData) {
    $data = array();
    
    $postTypes = self::getPostTypes();
    
    foreach ($postTypes as $postType) {
      $data[$postType->name] = array(
        'export' => $rawData['export'] ? in_array($postType->name, $rawData['export']) : false,
        'import' => $rawData['import'] ? in_array($postType->name, $rawData['import']) : false,
        'type'   => $postType->name
      );
    }
    
    $this->options = $data;
  }
  
  public function exportSettingPage() {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'export-settings-page.php');
  }
  
  public function importSettingPage() {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'import-settings-page.php');
  }
  
  public function addSettingPage() {
    if (isset($_POST['export-import-handler'])) {
      $this->options = $_POST;
      unset($this->options['export-import-handler']);
      
      $this->buildSaveData($_POST);
      $this->saveOptions();
      
      add_action('admin_notices', array($this, 'successUpdateSettings'));
    }
    
    add_submenu_page('themes.php', 'Export/Import', 'Export/Import', 'manage_options', 'export-import-handler', array(
      $this,
      'settingPage'
    ));
  }
  
  public function settingPage() {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . 'settings-page.php');
  }
  
  public static function getPostTypes() {
    $args = array(
      'public' => true
    );
    
    $postTypes = get_post_types($args, 'objects');
    
    unset($postTypes["attachment"]);
    
    return $postTypes;
  }
  
  public static function getStaticOptions() {
    return json_decode(get_option(self::OPTIONS), true);
  }
  
  public static function getOptions($name) {
    $options = self::getStaticOptions();
    
    if ($options) {
      $result = array_filter($options, function ($obj) use ($name) {
        return $obj['type'] === $name;
      });
      
      $keys = array_keys($result);
      
      return $result[$keys[0]];
    }
    
    return array();
  }
  
  public function successUpdateSettings() {
    ?>
    <div id="message" class="notice notice-success is-dismissible">
      <p>Options saved successfully. </p>
    </div>
    <?php
  }
  
  public function successImportSettings() {
    ?>
    <div id="message" class="notice notice-success is-dismissible">
      <p>Import Complete</p>
    </div>
    <?php
  }
  
  public function errorExportSettings() {
    ?>
    <div id="message" class="notice notice-error is-dismissible">
      <p>You don't have access or there is nothing to export, please add one item if you need the head table!</p>
    </div>
    <?php
  }
}
