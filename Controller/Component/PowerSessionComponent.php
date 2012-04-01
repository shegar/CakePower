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
	
	
	
	
	
	
/**
 * Session::setFlash() shortcuts and utilities.
 *
 * These methods are utilities to flashing customized flash messages!
 */
	function msg( $str ) {
		
		$this->setFlash( $str );
		
	}
	
	function ok( $str, $options = array() ) {
		
		if ( !array_key_exists('title',$options) ) $options['title'] = __('Ok!');
		
		$this->setFlash( $str, 'default', $options, 'ok' );
		
	}
	
	function ko( $str, $options = array() ) {
		
		if ( !array_key_exists('title',$options) ) $options['title'] = __('Error!');
		
		$this->setFlash( $str, 'default', $options, 'ko' );
		
	}
	
	function alert( $str, $options = array() ) {
		
		if ( !array_key_exists('title',$options) ) $options['title'] = __('Warning!');
		
		$this->setFlash( $str, 'default', $options, 'alert' );
		
	}
	
	
}