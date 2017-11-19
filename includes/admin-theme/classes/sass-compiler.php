<?php

use Leafo\ScssPhp\Compiler;

class SassCompiler {
	
	private $scss_folder;
	
	private $css_folder;
	
	
	private $files = array();
	
	public function __construct($scss_folder = 'scss/', $css_folder = 'css/'){
		$this->scss_folder = $scss_folder;
		$this->css_folder = $css_folder;
	}
	
	public function compile(){
		$this->compiler();
	}
	
	private function compiler(){
		$compiler = new Compiler();
		$compiler->addImportPath(function($path) {
			if (!file_exists(THEME_ADMIN_DIR.$this->scss_folder.$path)) return null;
			return THEME_ADMIN_DIR.$this->scss_folder.$path;
		});
		$compiler->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
		
		try{
			$this->files = glob(THEME_ADMIN_DIR . $this->scss_folder . "*.scss");
			if(!empty($this->files)){
				$this->clearCssDir();
				foreach($this->files as $file){
					$cssFile = $this->createCssFile($file);
					$scssString = file_get_contents($file);
					
					$cssString = $compiler->compile($scssString);
					
					$fp = fopen(THEME_ADMIN_DIR .$this->css_folder.$cssFile.'.css', 'w');
					fwrite($fp, $cssString);
					fclose($fp);
					chmod(THEME_ADMIN_DIR .$this->css_folder.$cssFile.'.css', 0666);
				}
			}
		} catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
	private function clearCssDir(){
		if(file_exists(THEME_ADMIN_DIR . $this->css_folder)){
			array_map('unlink', glob(THEME_ADMIN_DIR . $this->css_folder . '*'));
		} elseif(!mkdir(THEME_ADMIN_DIR . $this->css_folder, 0777, true)){
			die('Failed to create folders...');
		}
	}
	
	private function createCssFile($filename){
		return basename($filename, '.scss');
	}
}