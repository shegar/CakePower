<?php
/**
 * CakePOWER :: PowerSessionComponent
 *
 * It extends basic SessionComponent adding shortcuts for flashing messages and other utilities.
 *
 * How to use this component in a controller class (AppController??):
 *
 * public $components = array(
 *     'Session' => array( 'className'=>'PowerSession' )
 * );
 */


App::import('Controller/Component', 'SessionComponent');



class PowerSessionComponent extends SessionComponent {

	protected $Controller;
	
	public function initialize(Controller $controller) {
		parent::initialize($controller);
		$this->Controller = $controller;
	}
	
	

	
	
	
	
	
/**
 * Shortcuts to the PowerSession flashing methods
 * These methods handle AJAX automagically.
 * 
 * If a request is sent throught ajax then the response will be a JSON
 * object created with "ajaxOk", "ajaxKo", "ajaxMsg" methods.
 * 
 * ->ok( 'success' );
 * sets up the session notifications or handle ajax object response
 * 
 * ->ok( 'success', array( 'ajax'=>false ));
 * force to skyp ajax check
 * 
 * ->ok( 'success', array( 'redirect'=>array() ));
 * redirects only if not ajax.
 * if ajax skip the redirect property
 * [normally used when editing existing records]
 * 
 * ->ok( 'success', array( 'redirect'=>array(...), 'ajaxRedirect'=>true ));
 * sets up the session notification and redirect.
 * if ajax, sends the json object with a Router::url() parsed "redirect" property.
 * javascript must handle this property to redirect.
 * [normally used when creating new records]
 * 
 */
	
	public function msg( $str, $options = array() ) { 
		
		$ajax_check 	= true;
		$ajax_options	= array();
		$ajax_redirect	= false;
		$redirect		= false;
		
		if ( isset($options['redirect']) ) $redirect = $options['redirect'];
		unset($options['redirect']);
		
		if ( isset($options['ajax']) ) $ajax_check = $options['ajax'];
		unset($options['ajax']);
		
		if ( isset($options['ajaxRedirect']) ) $ajax_redirect = $options['ajaxRedirect'];
		unset($options['ajaxRedirect']);
		
		// Sets up AJAX based response.
		if ( $ajax_check ) {
			if ( $ajax_redirect ) $ajax_options = array_merge($options,array( 'redirect'=>$redirect ));
			$this->ajaxMsg( $str, $ajax_options );	
		}
		
		// Sets up session and internal redirect.
		//$this->Session->msg( $str, $options );
		$this->setFlash( $str );
		if ( $redirect ) $this->Controller->redirect( $redirect );
	
	}
	
	public function alrt( $str, $options = array() ) { 
		
		$ajax_check 	= true;
		$ajax_options	= array();
		$ajax_redirect	= false;
		$redirect		= false;
		
		if ( isset($options['redirect']) ) $redirect = $options['redirect'];
		unset($options['redirect']);
		
		if ( isset($options['ajax']) ) $ajax_check = $options['ajax'];
		unset($options['ajax']);
		
		if ( isset($options['ajaxRedirect']) ) $ajax_redirect = $options['ajaxRedirect'];
		unset($options['ajaxRedirect']);
		
		// Sets up AJAX based response.
		if ( $ajax_check ) {
			if ( $ajax_redirect ) $ajax_options = array_merge($options,array( 'redirect'=>$redirect ));
			$this->ajaxAlrt( $str, $ajax_options );	
		}
		
		// Sets up session and internal redirect.
		//$this->Session->msg( $str, $options );
		$this->setFlash( $str, 'default', $options, 'alert' );
		if ( $redirect ) $this->Controller->redirect( $redirect );
	
	}
	
	public function ok( $str, $options = array() ) {
		
		$ajax_check 	= true;
		$ajax_options	= array();
		$ajax_redirect	= false;
		$redirect		= false;
		
		if ( isset($options['redirect']) ) $redirect = $options['redirect'];
		unset($options['redirect']);
		
		if ( isset($options['ajax']) ) $ajax_check = $options['ajax'];
		unset($options['ajax']);
		
		if ( isset($options['ajaxRedirect']) ) $ajax_redirect = $options['ajaxRedirect'];
		unset($options['ajaxRedirect']);
		
		// Sets up AJAX based response.
		if ( $ajax_check ) {
			if ( $ajax_redirect ) $ajax_options = array_merge($options,array( 'redirect'=>$redirect ));
			$this->ajaxOk( $str, $ajax_options );	
		}
		
		// Sets up session and internal redirect.
		//$this->Session->ok( $str, $options );
		$this->setFlash( $str, 'default', $options, 'ok' );
		if ( $redirect ) $this->Controller->redirect( $redirect );
		 
	}
	
	public function ko( $str, $options = array() ) {
		
		$ajax_check 	= true;
		$ajax_options	= array();
		$ajax_redirect	= false;
		$redirect		= false;
		
		if ( is_string($options) ) {
			$tmp = array( 'title'=>$str );
			$str = $options;
			$options = $tmp; 
		}
		
		if ( isset($options['redirect']) ) $redirect = $options['redirect'];
		unset($options['redirect']);
		
		if ( isset($options['ajax']) ) $ajax_check = $options['ajax'];
		unset($options['ajax']);
		
		if ( isset($options['ajaxRedirect']) ) $ajax_redirect = $options['ajaxRedirect'];
		unset($options['ajaxRedirect']);
		
		// Sets up AJAX based response.
		if ( $ajax_check ) {
			if ( $ajax_redirect ) $ajax_options = array_merge($options,array( 'redirect'=>$redirect ));
			$this->ajaxKo( $str, $ajax_options );	
		}
		
		// Sets up session and internal redirect.
		//$this->Session->ko( $str, $options );
		$this->setFlash( $str, 'default', $options, 'ko' );
		if ( $redirect ) $this->Controller->redirect( $redirect );
	}
	
	
	
	
	

	
	
/**
 * Ajax Related Notifications
 * 
 * intercept an ajax callback and send to the client a json object with
 * a status information, a message and even some custom data
 */
	
	public function ajaxMsg( $message, $options = array() ) {
		
		if ( !$this->Controller->request->is('ajax') ) return;
		
		$data = array( 'status'=>'msg', 'message'=>$message, 'data'=>$options, 'CakePower'=>'1.0' );
		
		// Adds controller's validation errors to the data response.
		if ( empty($options['validationErrors']) ) {
			$options['validationErrors'] = $this->ajaxErrors();
		}
		
		// Export validation errors to the data array.
		if ( isset($options['validationErrors']) ) {
			$data['validationErrors'] = $options['validationErrors'];
			unset($options['validationErrors']);
			$data['options'] = $options;
		}
		
		// Export redirect informations to the data array
		if ( isset($options['redirect']) ) {
			if ( $options['redirect'] ) $data['redirect'] = Router::url($options['redirect']);
			unset($options['redirect']);
			$data['data'] = $options;
		}
		
		echo json_encode($data);
		exit;
	
	}
	
	public function ajaxAlrt( $message, $options = array() ) {
		
		if ( !$this->Controller->request->is('ajax') ) return;
		
		$data = array( 'status'=>'alert', 'message'=>$message, 'data'=>$options, 'CakePower'=>'1.0' );
		
		// Adds controller's validation errors to the data response.
		if ( empty($options['validationErrors']) ) {
			$options['validationErrors'] = $this->ajaxErrors();
		}
		
		// Export validation errors to the data array.
		if ( isset($options['validationErrors']) ) {
			$data['validationErrors'] = $options['validationErrors'];
			unset($options['validationErrors']);
			$data['options'] = $options;
		}
		
		// Export redirect informations to the data array
		if ( isset($options['redirect']) ) {
			if ( $options['redirect'] ) $data['redirect'] = Router::url($options['redirect']);
			unset($options['redirect']);
			$data['data'] = $options;
		}
		
		echo json_encode($data);
		exit;
	
	}
	
	public function ajaxOk( $message, $options = array() ) {
		
		if ( !$this->Controller->request->is('ajax') ) return;
		
		$data = array( 'status'=>'ok', 'message'=>$message, 'data'=>$options, 'CakePower'=>'1.0' );
		
		// Adds controller's validation errors to the data response.
		if ( empty($options['validationErrors']) ) {
			$options['validationErrors'] = $this->ajaxErrors();
		}
		
		// Export validation errors to the data array.
		if ( isset($options['validationErrors']) ) {
			$data['validationErrors'] = $options['validationErrors'];
			unset($options['validationErrors']);
			$data['options'] = $options;
		}
		
		// Export redirect informations to the data array
		if ( isset($options['redirect']) ) {
			if ( $options['redirect'] ) $data['redirect'] = Router::url($options['redirect']);
			unset($options['redirect']);
			$data['data'] = $options;
		}
		
		echo json_encode($data);
		exit;
	
	}
	
	public function ajaxKo( $message, $options = array() ) {
		
		if ( !$this->Controller->request->is('ajax') ) return;
		
		$data = array( 'status'=>'ko', 'message'=>$message, 'data'=>$options, 'CakePower'=>'1.0' );
		
		// Adds controller's validation errors to the data response.
		if ( empty($options['validationErrors']) ) {
			$options['validationErrors'] = $this->ajaxErrors();
		}
		
		// Export validation errors to the data array.
		if ( isset($options['validationErrors']) ) {
			$data['validationErrors'] = $options['validationErrors'];
			unset($options['validationErrors']);
			$data['options'] = $options;
		}
		
		// Export redirect informations to the data array
		if ( isset($options['redirect']) ) {
			if ( $options['redirect'] ) $data['redirect'] = Router::url($options['redirect']);
			unset($options['redirect']);
			$data['data'] = $options;
		}
		
		
		echo json_encode($data);
		exit;
	
	}
	
	public function ajaxErrors() {
		
		$errors = array( 'models'=>array(), 'fields'=>array() );
		
		foreach ( $this->Controller->uses as $modelName ) {
			
			// Handle models from plugins with dotted notation.
			if ( strpos($modelName,'.') !== false ) list( $plugin, $modelName ) = explode('.',$modelName);
			
			$errors['models'][$modelName] = $this->Controller->{$modelName}->validationErrors;
			
			foreach ( $this->Controller->{$modelName}->validationErrors as $fieldName=>$msgs ) {
				
				$errors['fields'][$modelName.Inflector::camelize($fieldName)] = $msgs[0];
				
			}
		}

		return $errors;
		
	}
	
	
	
	
	
	
	
	
	
/**	
 * Shortcuts with message and redirect url.
 * They listen to the REST method to activate or deactivate the AJAX REDIRECT option!
 */
	
	protected function ajaxRestRedirect( $redirect ) {
		
		$_redirect = ( $this->Controller->request->is('POST') || $this->Controller->request->is('DELETE') ) ? true : false;
		if ( isset($_GET['_redirect']) ) 	$_redirect = ( $_GET['_redirect'] );
		if ( isset($_POST['_redirect']) ) 	$_redirect = ( $_POST['_redirect'] );
		
		return $_redirect;
		
	}
	
	public function confirm( $str, $redirect = array() ) {
		
		$this->ok( $str, array(
			'redirect' 		=> $redirect,
			'ajaxRedirect' 	=> $this->ajaxRestRedirect($redirect)
		));
		
	}
	
	public function error( $str, $redirect = array() ) {
		
		$this->ko( $str, array(
			'redirect' 		=> $redirect,
			'ajaxRedirect' 	=> $this->ajaxRestRedirect($redirect)
		));
		
	}
	
	public function alert( $str, $redirect = array() ) {
		
		$this->alrt( $str, array(
			'redirect' 		=> $redirect,
			'ajaxRedirect' 	=> $this->ajaxRestRedirect($redirect)
		));
		
	}
	
	public function message( $str, $redirect = array() ) {
		
		$this->msg( $str, array(
			'redirect' 		=> $redirect,
			'ajaxRedirect' 	=> $this->ajaxRestRedirect($redirect)
		));
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
}