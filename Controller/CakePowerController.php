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
 */
	protected $__cakePower = array(
		'version' 			=> '1.0',
		'components' 		=> array( 'Session', 'Auth' ),
		'helpers' 			=> array()
	);
	
	
/**
 * CakePower Contructor
 *
 * It fill some configuration properties to use implemented CakePHP classes.
 */
	public function __construct($request = null, $response = null) {
		
		// Fill the components array with aliasing informations to use CakePower's classes
		foreach ( $this->__cakePower['components'] as $cmp ) {
			
			if ( empty($this->components[$cmp]) ) $this->components[$cmp] = array();
			if ( empty($this->components[$cmp]['className']) ) $this->components[$cmp]['className'] = 'CakePower.Power'.$cmp;
			
		}
		
		parent::__construct( $request, $response );
	
	}
	
	
/**
 * Shortcuts to the PowerSession flashing methods
 */
	public function msg( $str ) { $this->Session->msg( $str ); }
	public function ok( $str, $options = array() ) { $this->Session->ok( $str, $options ); }
	public function ko( $str, $options = array() ) { $this->Session->ko( $str, $options ); }
	

}