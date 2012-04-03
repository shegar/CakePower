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
			
			foreach ( $this->__cakePower[$type] as $cmp ) {
				
				if ( empty($this->{$type}[$cmp]) ) $this->{$type}[$cmp] = array();
				if ( empty($this->{$type}[$cmp]['className']) ) $this->{$type}[$cmp]['className'] = 'CakePower.Power'.$cmp;
				
			}
			
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