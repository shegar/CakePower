<?php

class PowerSet extends Set {
	
	
	
	
	
	
	
	
	
	
	
	public static function is_vector( $arr = array() ) {
		
		return (0 !== array_reduce(
			array_keys($arr),
			array( 'PowerSet', '_is_vector_reduce' ),
			0
        ));
        
	}
	
	public static function is_assoc( $arr = array() ) {
		
		return !self::is_vector($arr);
		
	}
	
	protected static function _is_vector_reduce( $a, $b ) {
		
		return ($b === $a ? $a + 1 : 0);
		
	}

	
	
	
	
	
	
	
	
	
	
	
/**
 * Array path notation utilities.
 * 
 * dots2array() translates a dotted string to a path array:
 * 
 * path1.path2.pat3
 * ->
 * array(
 *   'path1',
 *   'path2',
 *   'path3'
 * )
 * 
 * array2dots() is the reverse logic.
 * 
 * dotsParent('path1.path2.path3')
 * -> path1.path2
 * 
 */
	public static function dots2array( $dots = '' ) {
		
		if (is_string($dots)) {
			if (strpos($dots, ".")) {
				return explode(".", $dots);
			}
			return array($dots);
		}
		
		return $dots;
		
	}
	
	public static function array2dots( $array = array() ) {
		
		if ( empty($array) || !is_array($array) ) return '';
		
		return array_reduce( $array, array( 'PowerSet', '_array2dots_walk' ) );
	
	}
	
	protected static function _array2dots_walk($a,$b) {
			
		if ( !empty($a) ) {
			$a.= '.'.$b;
					
		} else {
			$a.=$b;
			
		}
		
		return $a;
		 
	}
	
	public static function dotsParent( $dots = '' ) {
		
		$tmp = self::dots2array($dots);
		
		if ( count($tmp) < 1 ) return $dots;
		
		array_pop($tmp);
		
		return self::array2dots($tmp);
	
	}
	
	
	
	
/**
 * Supplies methods to insert items inside an associative array using a key name as insertion point.
 * 
 * 
 * $arr = array( 'foo1'=>'a', 'foo2'=>'b', 'foo3'=>'c' );
 * 
 * $arr = PowerSet::beforeAssoc( $arr, 'foo2', 'pre-foo2', 'value...' );
 * -> array( 'foo1'=>'a', 'pre-foo2'=>'value...', 'foo2'=>'b', 'foo3'=>'c' )
 * 
 * $arr = PowerSet::afterAssoc( $arr, 'foo2', array('foo2a'=>'value') );
 * -> array( 'foo1'=>'a', 'foo2'=>'b', 'foo2a'=>'value', 'foo3'=>'c' )
 * 
 * You can use both 3rd and 4th params to define new item's name and value or combine them into
 * an associative array as 3rd param.
 * 
 */
	
	public static function beforeAssoc( $set = array(), $matchKey = '', $newKey = '', $newVal = '' ) {
		
		if ( !self::_checkBeforeAfterAssoc($set,$matchKey,&$newKey,&$newVal) ) return false;
		
		$newSet = array();
		
		foreach ( $set as $_key=>$_val ) {
			
			if ( $_key == $matchKey ) $newSet[$newKey] = $newVal;
			
			$newSet[$_key] = $_val;
			
		}
		
		// You can pass $set by reference when calling this method!
		$set = $newSet;
		
		return $newSet;
		
	}
	
	public static function afterAssoc( $set = array(), $matchKey = '', $newKey = '', $newVal = '' ) {
		
		if ( !self::_checkBeforeAfterAssoc($set,$matchKey,&$newKey,&$newVal) ) return false;
		
		$newSet = array();
		
		foreach ( $set as $_key=>$_val ) {
			
			$newSet[$_key] = $_val;
			
			if ( $_key == $matchKey ) $newSet[$newKey] = $newVal;
			
		}
		
		// You can pass $set by reference when calling this method!
		$set = $newSet;
		
		return $newSet;
		
	}
	
	protected static function _checkBeforeAfterAssoc( $set = array(), $matchKey = '', $newKey = '', $newVal = '' ) {
		
		if ( empty($set) ) return false;
		
		if ( !Set::check($set,$matchKey) ) return false;
		
		if ( empty($newKey) ) return false;
		
		// It handle $newKey to contain an associative array with the value for the insertion.
		if ( is_array($newKey) ) {
			 
			$keys = array_keys($newKey);
			
			if ( count($keys) == 1 ) {
				
				$newVal = $newKey[$keys[0]];
				$newKey = $keys[0];
			
			}
			
		}
		
		if ( !is_string($newKey) ) return false;
		
		return true;
		
	}
	
	
	
	
	
	
	
	
/**
 * Supplies insert methods for a vector array.
 * 
 * $arr = array( 'red', 'white', 'green' );
 * 
 * PowerSet::beforeVector( $arr, 'white', 'blue' );
 * -> array( 'red', 'blue', 'white', 'green' )
 * 
 * PowerSet::afterVector( $arr, '{1}', 'blue' );
 * -> array( 'red', 'white', 'blue', 'green' )
 * 
 * You can use both item value or item index {i} to identify the intert key point.
 * 
 */
	
	public static function beforeVector( $set = array(), $key = '', $val = '' ) {
		
		if ( !self::_checkBeforeAfterVector($set,$key,$val) ) return false;
		
		$newSet = array();
			
		foreach ( $set as $i=>$_val ) {
			
			if ( $_val == $key || '{'.$i.'}' == $key ) $newSet[] = $val;
			
			$newSet[] = $_val;
		
		}
		
		// You can pass $set by reference when calling this method!
		$set = $newSet;
		
		return $newSet;
		
	}
	
	public static function afterVector( $set = array(), $key = '', $val = '' ) {
		
		if ( !self::_checkBeforeAfterVector($set,$key,$val) ) return false;
		
		$newSet = array();
			
		foreach ( $set as $i=>$_val ) {
			
			$newSet[] = $_val;
			
			if ( $_val == $key || '{'.$i.'}' == $key ) $newSet[] = $val;
		
		}
		
		// You can pass $set by reference when calling this method!
		$set = $newSet;
		
		return $newSet;
		
	}
	
	protected static function _checkBeforeAfterVector( $set = array(), $key = '', $val = '' ) {
		
		if ( !self::is_vector($set) ) return false;
		
		if ( empty($key) ) return false;
		
		return true;
		
	} 
	
	
	
	

	
/**
 * shortcut methods with data type inspection.
 */
	public static function before( $set = array(), $matchKey = '', $newKey = '', $newVal = '' ) {
		
		if ( self::is_vector($set) ) return self::beforeVector( $set, $matchKey, $newKey );
		
		return self::beforeAssoc( $set, $matchKey, $newKey, $val );
		
	}
	
	public static function after( $set = array(), $matchKey = '', $newKey = '', $newVal = '' ) {
		
		if ( self::is_vector($set) ) return self::afterVector( $set, $matchKey, $newKey );
		
		return self::afterAssoc( $set, $matchKey, $newKey, $val );
		
	}
	
	

}