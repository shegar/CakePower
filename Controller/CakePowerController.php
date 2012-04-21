<?php
/**
 * CakePOWER AppController
 *
 * This controller 
 */
 
App::uses('Controller', 'Controller');

class CakePowerController extends Controller {
	

/**
 * CakePower Info
 * store some static informations about CakePower
 * 
 * components and helpers can be setted to "false" to do not force to load
 * the library. In this case parent class must explicitly include the class into its configuration. 
 */
	protected $__cakePower = array(
		'version' 			=> '1.0',
		'components' 		=> array( 'Session', 'Auth'=>false ),
		'helpers' 			=> array( 'Html', 'Paginator' ),
	);
	
	
/**
 * CakePower Contructor
 *
 * It fill some configuration properties to use implemented CakePHP classes.
 */
	public function __construct($request = null, $response = null) {
		
		
		// Loads extended core classes setting an alias to use them with the normal app names.
		foreach( array('components','helpers') as $type ) {
			
			foreach ( $this->__cakePower[$type] as $key=>$val ) {
				
				if ( is_numeric($key) ) {
					$cmp 	= $val;
					$load 	= true;
				} else {
					$tmp 	= $key;
					$load 	= $val;
				}
				
				if ( empty($this->{$type}[$cmp]) && $load === true ) $this->{$type}[$cmp] = array();
				if ( isset($this->{$type}[$cmp]) && empty($this->{$type}[$cmp]['className']) ) $this->{$type}[$cmp]['className'] = 'CakePower.Power'.$cmp;
				
			}
			
		}
		
		parent::__construct( $request, $response );
		
		
		// TMP: here will be listed controller's services...
		foreach ( $this->methods as $i=>$val ) {
			if ( substr($val,0,2) == '__' ) $this->methods[$i] = substr($val,2,100);
		}
	
	}
	
	
	
	
	

	
	
	
	
	public function invokeAction(CakeRequest $request) {
		
		try {
			$method = new ReflectionMethod($this, $request->params['action']);

			if ($this->_isPrivateAction($method, $request)) {
				throw new PrivateActionException(array(
					'controller' => $this->name . "Controller",
					'action' => $request->params['action']
				));
			}
			return $method->invokeArgs($this, $request->params['pass']);

		} catch (ReflectionException $e) {
			
			/** @@CakePOWER@@ **/
			// Services... //
			$private_method = '__' . $request->params['action'];
			try {
				$method = new ReflectionMethod($this, $private_method);
				return $method->invokeArgs($this, $request->params['pass']);
			} catch (ReflectionException $e) {}
			/** ##CakePOWER## **/
			
			
			if ($this->scaffold !== false) {
				return $this->_getScaffold($request);
			}
			throw new MissingActionException(array(
				'controller' => $this->name . "Controller",
				'action' => $request->params['action']
			));
		}
	}

}