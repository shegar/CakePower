<?php

class PowerConfig {

	protected static $_data = array();
	
	private static function _checkInit() {
		
		if ( empty(self::$_data) || !is_array(self::$_data) ) self::$_data = array();
		
	}
	
	
	
/**
 * Tests for a path existance.
 * It does not test for path's content! 
 * Key may only exists even with a null value to return true
 */	
	public static function exists( $key = null ) {
		
		if ( $key === null ) return false;
		
		self::_checkInit();
		
		$tmp =& self::$_data;
		
		foreach ( UTH::dots2array($key) as $i=>$key ) {
			
			if ( !is_array($tmp) ) return false;
			
			if ( !array_key_exists($key,$tmp) ) return false;
			
			$tmp =& $tmp[$key];
			
		}
		
		return true;
		
	}
	
	
/**
 * Set a value into a path overwriting preexistance contentents.
 * You can override all data by passing an array as first param.
 */
	public static function set( $key = null, $val = null ) {
		
		self::_checkInit();
		
		if ( $key === null ) return;
		
		// Set the entire data with value.
		if ( is_array($key) && $val === null ) {
			
			self::$_data = $key;
			
			return true;
			
		}
		
		// Setting a key with no value will clear that key with a null value!
		if ( $val === null ) return self::clear( $key );
		
		// Set value to the root level.
		if ( empty($key) ) {
			
			self::$_data = $val;
			
			return true;
			
		}
		
		
		
		// Build the tree and set the value to the last key path.
		
		$tmp =& self::$_data;
		
		foreach ( UTH::dots2array($key) as $key ) {
			
			if ( !is_array($tmp) ) $tmp = array();
			
			if ( !array_key_exists($key,$tmp) ) $tmp[$key] = array();
			
			$tmp =& $tmp[$key];
			
		}
		
		$tmp = $val;
		
		return true;
		
	}
	

/**
 * Set up a value for the key only if key does not exists or 
 * actual value is empty.
 */
	public static function def( $key = null, $val = null ) {
		
		$actualVal = self::get($key);
		
		if ( empty($actualVal) || $val == false ) {
			
			return self::set( $key, $val );
			
		}
		
		return false;
		
	}
	
	
/**
 * Read data for a path.
 * 
 * return the path value if path exists. Even if it is an empty value.
 * return "false" if path does not exists.
 *
 * $default: you can set a default value to be returned if path does not
 *           exists or if path refer to an empty value.
 *
 * Example:
 * PowerConfig::get('User.name','user\'s name');
 *
 * -> it return the user's name if exits or the string "user's name" 
 *    if empty or 'User.name' does not exists in the data array.
 * 
 */
	public static function get( $key = null, $default = null ) {
		
		self::_checkInit();
		
		if ( $key === null || empty($key) ) return self::$_data;
		
		$tmp =& self::$_data;
		
		foreach ( UTH::dots2array($key) as $key ) {
			
			if ( !is_array($tmp) ) return ( $default !== null ) ? $default : false;
			
			if ( !array_key_exists($key,$tmp) ) return ( $default !== null ) ? $default : false;
			
			$tmp =& $tmp[$key];
			
		}
		
		if ( empty($tmp) && $default !== null ) return $default;
		
		return $tmp;
		
	}
	
	
/**
 * Debug Utilities.
 */
	public static function debug( $key = null ) {	debug( self::get($key) ); }
	public static function ddebug( $key = null ) {	ddebug( self::get($key) ); }
	
	
	
/**
 * It extend a key value with other data.
 * 
 * - if both $key data and new value are arrays the will be merged.
 * - if $key does not exists it will be created with $val (like set)
 * - if $val is not an array it will override $key data.
 *
 */
	public static function extend( $key = null, $val = null ) {
		
		if ( $key === null && $val === null ) return false;
		
		if ( is_array($key) ) {
			
			$val = $key;
			
			$key = '';
			
		}
		
		// If desired path doesn't exists will be created with new value without extend anything. #
		if ( !empty($key) && !self::exists($key) ) return self::set( $key, $val );
		
		// Fetch the $key data who needs to be extended.
		$data = PowerConfig::get($key);
		
		// If new value is not an array it will overwrite actual data.
		if ( !is_array($val) ) return self::set( $key, $val );
		
		// I use "Set" core library to extend desired path with new values.
		self::set( $key, PowerSet::merge( $data, $val ) );
		
	}
	
	
/**
 * Extend a key data but does not alter exising values.
 * Uses PowerSet::pushDiff()
 */
	public static function pushDiff( $key = null, $val = null ) {
		
		if ( $key === null && $val === null ) return false;
		
		if ( is_array($key) ) {
			
			$val = $key;
			
			$key = '';
			
		}
		
		// If desired path doesn't exists will be created with new value without extend anything. #
		if ( !empty($key) && !self::exists($key) ) return self::set( $key, $val );
		
		// Use PowerSet::pushDiff() to merge data
		return PowerConfig::set( $key, Set::pushDiff(PowerConfig::get($key),$val) );
		
	}
	
	
/**
 * Append values at the end of an array
 * It can take lot of values quequed to the func_args:
 *
 * PowerConfig::set('a', array( 'v1', 'v2' ));
 * CakePower::append('a','v3','v4');
 * -> array( 'v1', 'v2', 'v3, 'v4' )
 *
 */
	public static function append( $key = null, $val = null ) {
		
		$data = self::get($key);
		
		if ( !is_array($data) ) return false;
		
		$args = func_get_args();
		array_shift($args);
		
		foreach ( $args as $arg ) $data[] = $arg;
		
		return self::set( $key, $data );
	
	}
	

/**
 * Prepend values at the beginning of an array.
 * It can take lot of values quequed to the func args.
 *
 * The firs value arg will be the first in the resulting array!
 * See example below!
 *
 * PowerConfig::set( 'letters', array( 'd', 'e', 'f' ));
 * PowerConfig::prepend( 'letters', 'a', 'b', 'c' ));
 * -> array( 'a', 'b', 'c', 'd', 'e', 'f' )
 */
	public static function prepend( $key = null, $val = null ) {
		
		$data = self::get($key);
		
		if ( !is_array($data) ) return false;
		
		$args = func_get_args();
		array_shift($args);
		
		foreach ( array_reverse($args) as $arg ) array_unshift( $data, $arg );
		
		return self::set( $key, $data );
	
	}
	

	
	
/**
 * Inject data into an associative array contained into the configuration data.
 * 
 * PowerConfig::set( 'test', array(
 *   'foo1' => 'a',
 *   'foo2' => 'a'
 * ));
 * 
 * PowerConfig::before( 'test.foo2', 'fooBefore2' , 'value' );
 * 
 * array(
 *   'foo1' => 'a',
 *   'fooBefore2' => 'value',
 *   'foo2' => 'a'
 * )
 * 
 * PowerConfig::after( 'test.foo1', array('foo1b'=>'pippo'));
 * 
 * array(
 *   'foo1' => 'a',
 *   'foo1b' => 'pippo',
 *   'fooBefore2' => 'value',
 *   'foo2' => 'a'
 * ) 
 * 
 * ---------
 * 
 * PowerConfig::set( 'test1', array( 'a', 'b', 'c' ));
 * 
 * PowerConfig::after( 'test1.a', 'a1' );
 * -> array( 'a', 'a1', 'b', 'c' );
 * 
 * PowerConfig::before( '{0}', 'colors' );
 * -> array( 'colors', 'a', 'a1', 'b', 'c' );
 * 
 */
	public static function before( $key = null, $name = '', $val = null ) {
		
		// Get method components
		$lastKey 	= self::thisPath($key,false);
		$parentKey 	= self::parentPath($key,false);
		
		if ( !self::exists($key) ) {
			
			// Check for scalar vectors requests
			$arr = self::get($parentKey);
			
			if ( $arr !== false && PowerSet::is_vector($arr) ) {
				
				$result = PowerSet::beforeVector( $arr, $lastKey, $name );
				
				self::set( $parentKey, $result );
				return true;
			
			}
			
			return false;
			
		}
		
		// Associative array request.
		
		$result = PowerSet::beforeAssoc( self::get($parentKey), $lastKey, $name, $val );
		if ( $result === false ) return false;
		
		self::set( $parentKey, $result );
		return true;
		
	}
	
	public static function after( $key = null, $name = '', $val = null ) {
		
		// Get method components
		$lastKey 	= self::thisPath($key,false);
		$parentKey 	= self::parentPath($key,false);
		
		
		if ( !self::exists($key) ) {
			
			// Check for scalar vectors requests
			$arr = self::get($parentKey);
			
			if ( $arr !== false && PowerSet::is_vector($arr) ) {
				
				$result = PowerSet::afterVector( $arr, $lastKey, $name );
				
				self::set( $parentKey, $result );
				return true;
			
			}
			
			return false;
			
		}
		
		// Associative array request.
		
		$result = PowerSet::afterAssoc( self::get($parentKey), $lastKey, $name, $val );
		if ( $result === false ) return false;
		
		self::set( $parentKey, $result );
		
	}
	
	
	
/**	
 * supplies the last path trunk of a given path:
 * 
 * path1.path2.path3 -> "path3"
 * 
 */
	public static function thisPath( $key = null, $exists = true ) {
		
		if ( !self::exists($key) && $exists ) return false;
		
		$key = PowerSet::dots2array($key);
		
		return array_pop($key);
	
	}
	
	
/**
 * supplies the parent path of an existing path.
 * return false if path does not exists.
 */
	public static function parentPath( $key = null, $exists = true ) {
		
		if ( !self::exists($key) && $exists ) return false;
		
		return PowerSet::dotsParent($key);
	
	}
	
	
/**
 * supplies the parent data array of a path.
 * return false if path does not exists.
 */
	public static function parent( $key = null ) {
		
		$parentKey = self::parentPath( $key );
		
		if ( $parentKey === false ) return false;
		
		return self::get($parentKey);
	
	}
	
	
/**
 * Remove the key path from the data:
 * 
 * array(
 *   key1 => array(
 *      key2 => 'test value'
 *   )
 * )
 *
 * PowerConfig::del('key1.key2');
 *
 * array(
 *   key1 => array()
 * )
 *
 */
	public static function del( $key = null ) {
		
		if ( $key === null ) return false;
		
		self::_checkInit();
		
		$tmp =& self::$_data;
		
		$keys = UTH::dots2array($key);
		
		foreach ( $keys as $i=>$key ) {
			
			if ( !is_array($tmp) ) return false;
			
			if ( !array_key_exists($key,$tmp) ) return false;
			
			if ( $i >= count($keys)-1 ) {
				unset($tmp[$key]);
				return true;
			}
			
			$tmp =& $tmp[$key];
			
		}
		
		return false;
		
	}
	
	
/**
 * Remove the key path from the data:
 * 
 * array(
 *   key1 => array(
 *      key2 => 'test value'
 *   )
 * )
 *
 * PowerConfig::clear('key1.key2');
 *
 * array(
 *   key1 => array(
 *      key2 => null
 *   )
 * )
 *
 */
	public static function clear( $key = null ) {
		
		if ( $key === null ) return false;
		
		self::_checkInit();
		
		$tmp =& self::$_data;
		
		$keys = UTH::dots2array($key);
		
		foreach ( $keys as $i=>$key ) {
			
			if ( !is_array($tmp) ) return false;
			
			if ( !array_key_exists($key,$tmp) ) return false;
			
			if ( $i >= count($keys)-1 ) {
				$tmp[$key] = null;
				return true;
			}
			
			$tmp =& $tmp[$key];
			
		}
		
		return false;
	
	}
	
	
	public static function reset() {
		
		self::set(array());
	
	}
	
}