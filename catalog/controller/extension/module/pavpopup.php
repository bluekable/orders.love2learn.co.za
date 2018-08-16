<?php
/******************************************************
 * @package Pavo Popup Module for Opencart 3.x
 * @version 1.0
 * @author http://www.pavothemes.com
 * @copyright	Copyright (C) Feb 2017 PavoThemes.com <@emai:pavothemes@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
*******************************************************/
/**
 * @class ControllerExtensionModulePavlayerednavigation
 * @version 1.0
 */
class ControllerExtensionModulePavpopup extends Controller {
	
 
	/**
	 *
	 */
	public function loadScript(){
		$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
  		$this->document->addScript('catalog/view/javascript/jquery/pavpopup.js' );
	}
	
	public function renderModule( $id ){
		$tmp = explode( "|", $id );
	 
		if ( count($tmp) == 2 ) {
			$this->load->model('setting/module');
			$setting_info = $this->model_setting_module->getModule( $tmp[1] );

			if ($setting_info && $setting_info['status']) { 
				$output = $this->load->controller('extension/module/'.$tmp[0] , $setting_info);
			 	return $output;
			}
		}
	}

	/**
	 * render content module
	 */
	public function index( $setting ) {   

		$this->load->language('extension/module/pavpopup');
		$this->loadScript();

		$language_id = $this->config->get('config_language_id'); 

		$data['heading_title'] = isset( $setting['module_description'] ) && isset( $setting['module_description'][$this->config->get('config_language_id')] ) && isset( $setting['module_description'][$this->config->get('config_language_id')]['title'] ) ? html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8') : '';
		$data['html'] = isset( $setting['module_description'] ) && isset( $setting['module_description'][$this->config->get('config_language_id')] ) && isset( $setting['module_description'][$this->config->get('config_language_id')]['description'] )  ? html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8') : '';
 		
 		$data['module'] 	 = isset( $setting['load_module_id'] ) ? $this->renderModule( $setting['load_module_id'] ) : '';
 		$data['display'] 	 = isset( $setting['display'] ) ? $setting['display'] : false;
 		$data['time_close']  = isset( $setting['time_close'] ) ? $setting['time_close'] : false;
 		$data['time_open']   = isset($setting['time_open'] ) ? $setting['time_open']: false;
 		$data['width'] 		 = isset( $setting[ 'width' ] ) ? $setting[ 'width' ] : false;
 
		return  $this->load->view( 'extension/module/pavpopup', $data );
	}
}
?>