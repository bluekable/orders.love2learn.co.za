<?php
class ControllerExtensionModulePavpopup extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/pavpopup');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('pavpopup', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			$id = $this->request->get['module_id'];
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/pavpopup', 'module_id='.$id.'&user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/pavpopup', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/pavpopup', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/pavpopup', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/pavpopup', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['load_module_id'])) {
			$data['load_module_id'] = $this->request->post['load_module_id'];
		} elseif (!empty($module_info)) {
			$data['load_module_id'] = $module_info['load_module_id'];
		} else {
			$data['load_module_id'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 600;
		}

		if (isset($this->request->post['time_close'])) {
			$data['time_close'] = $this->request->post['time_close'];
		} elseif (!empty($module_info)) {
			$data['time_close'] = $module_info['time_close'];
		} else {
			$data['time_close'] = 12;
		}

		if (isset($this->request->post['time_open'])) {
			$data['time_open'] = $this->request->post['time_open'];
		} elseif (!empty($module_info) && isset($module_info['time_open']) ) {
			$data['time_open'] = $module_info['time_open'];
		} else {
			$data['time_open'] = 0;
		}


		if (isset($this->request->post['display'])) {
			$data['display'] = $this->request->post['display'];
		} elseif (!empty($module_info)) {
			$data['display'] = $module_info['display'];
		} else {
			$data['display'] = '';
		}

		if (isset($this->request->post['module_description'])) {
			$data['module_description'] = $this->request->post['module_description'];
		} elseif (!empty($module_info)) {
			$data['module_description'] = $module_info['module_description'];
		} else {
			$data['module_description'] = array();
		}


 
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		 

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['displays'] = array(
			'alway_show_time'   => $this->language->get( 'text_alway_show_time' ),
			'alway_show_close'  => $this->language->get( 'alway_show_close' )
		);
 		$data['groups_extensions'] = $this->getExtensions(); 

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/pavpopup', $data));
	}

	protected function getExtensions(){
		$data = array();
		$this->load->model('setting/extension');
		$extensions = $this->model_setting_extension->getInstalled( 'module' );
		$groups = array() ;
		foreach ( $extensions as $key => $code ) {
			if ( $code === 'pavobuilder' ) continue;
			$this->load->language( 'extension/module/' . $code );
			$modules = $this->model_setting_module->getModulesByCode( $code );
			
			if ( $modules ) {
				$group = array(
						'name'		=> strip_tags( $this->language->get( 'heading_title' ) ),
						'slug'		=> $code
				);
				$gmodules = array();
				foreach ( $modules as $module ) {
					unset( $module['setting'] );
					$module['module_id'] = $code.'|'.$module['module_id'];
					$module['name']	 = strip_tags( $this->language->get( 'heading_title' ) );
					$module['code']		= $code;
					// $module['settings']			= $module['setting'];
					$gmodules[] 	= $module;
				}
				$group['modules'] = $gmodules;
				$groups[] = $group;
			}
		}

		return $groups;
		// echo '<pre>'.print_r( $groups, 1 );die;
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/pavpopup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		return !$this->error;
	}
}