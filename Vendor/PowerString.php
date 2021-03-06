<?php

class PowerString extends String {
	

/**
 * Insert some values into a template string based on placeholder rules.
 * @param unknown_type $tpl
 * @param unknown_type $data
 * @param unknown_type $clearer
 * @param unknown_type $options
 * 
 * echo PowerString::tpl( 'name: {name}, surname: {surname}', array(
 *     'name' => 'Mark',
 *     'surname' => 'Sheepkeeper'
 * ));
 * 
 * -> 'name: Mark, surname: Sheepkeeper'
 * 
 */
	public static function tpl( $tpl, $data = array(), $clearer = array(), $options = array() ) {
		
		$defaults = array( 'clear'=>true, 'clean'=>false, 'before'=>'{', 'after'=>'}' );
		$options += $defaults;
		
		// Clar "all" will remove the string if all placeholders are empty.
		if ( $clearer == 'all' ) $clearer = array( self::stripPlaceholders($tpl) );
		
		// Auto set for remove unused placeholder.
		if ( !isset($options['clean']) ) $options['clean'] = true;
		
		$str = self::insert( $tpl, $data, $options );
		
		if ( $options['clear'] ) $str = self::stripPlaceholders($str,$options);
		
		return self::clr( $str, $clearer, $options );
		
	}
	

/**	
 * Apply a cleaner paths to a string.
 * @param string $str
 * @param array $values
 * @param array $options
 * 
 * echo PowerString::clr('my **label** is **val**',array(
 *     '**label**',
 *     '**name**' => '!!empty!!'
 * ));
 * 
 * result:
 * -> my  is !!empty!!
 * 
 * You can pass a list of sub-strings to be removed from the first arguments.
 * Each item can define a custom replace string as associative value.
 * 
 *  You can set a custom replace value for all items in the $options argument:
 *  $options['replace'] = 'whatever you want!'
 * 
 */
	public static function clr( $str = '', $values = array(), $options = array() ) {
		
		$defaults = array( 'replace'=>'' );
		$options += $defaults;
		
		
		if ( empty($values) || !is_array($values) ) $values = array();
		
		
		foreach ( $values as $key=>$val ) {
			
			if ( is_numeric($key) ) {
				$key = $val;
				$val = $options['replace'];
			}
			
			$str = str_replace( $key, $val, $str );
			
		}
		
		return $str;
		
	}
	
	
	
	
	
	
	
/**	
 * Strip unused placeholders from a string based in placeholder delimiters.
 * @param string $str
 * @param array $options
 * 
 * echo PowerString::stripPlaceholders( 'name: {name}' );
 * -> 'name: '
 * 
 * $options[]:
 * before:  placeholder left delimiter
 * after:   placeholder right delimiter
 * replace: a string to use in place of
 */
	public static function stripPlaceholders( $str, $options = array() ) {
	
		$defaults = array( 'before'=>'{', 'after'=>'}', 'replace'=>'' );
		$options += $defaults;
	
		preg_match_all("|" . $options['before'] . "(.*)" . $options['after'] . "|U", $str, $matches);
		for ($i=0; $i< count($matches[0]); $i++) {
	
			$str = str_replace( $matches[0][$i], $options['replace'], $str );
	
		}
	
		return $str;
	
	}
	
	
	
/**	
 * Parse a string as placeholder information.
 * 
 * A placeholder may be dropped in a string as:
 * {name:type?a=foo1&b=foo2}
 * 
 * A mechanism extraxts content in brakets and pass to this method:
 * $str = "name:type?a=foo1&b=foo"
 * 
 * The output is an associative array with parsed informations:
 * return array(
 *     'name' => 'name',
 *     'type' => 'type',
 *     'info' => array( 'a'=>'foo1', 'b'=>'foo2' )
 * );
 */
	public static function parsePlaceholder( $str, $options = array() ) {
		
		$options += array( 'defaultType'=>'text' );
		
		$return = array(
			'name' 	=> '',
			'type'	=> '',
			'info'	=> array()
		);
		
		// Ensure the presence of booth var name and options tokens
		if ( strpos($str,'?') === false ) $str.= '?';
		list( $p1, $p2 ) = explode( '?', $str );
		
		// Parse var name and type
		if ( strpos($p1,':') === false ) $p1.= ':';
		list( $return['name'], $return['type'] ) = explode( ':', $p1 );
		
		// Parse url info (the querystring token)
		if ( !empty($p2) ) parse_str( $p2, $return['info'] );
		
		// Set defaults.
		if ( empty($return['type']) ) $return['type'] = $options['defaultType'];
		
		if ( empty($return['name']) ) return false;
		return $return;
		
	}
	
	
	
	
	public static function getFirstTrunk( $str, $sep = '.' ) {
		
		$tokens = PowerString::tokenize( $str, $sep );
		if ( !is_array($tokens) || !count($tokens) ) return false;
		
		return $tokens[0];
	
	}
	
	public static function getLastTrunk( $str, $sep = '.' ) {
		
		$tokens = PowerString::tokenize( $str, $sep );
		if ( !is_array($tokens) || !count($tokens) ) return false;
		
		$tokens = array_reverse($tokens);
		return $tokens[0];
	
	}
	
	
	
	
}