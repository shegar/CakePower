<?php
/**
 * CakePOWER.org
 * Utility library
 * 
 * @author:			Marco Pegoraro
 * @mail:			marco(dot)pegoraro(at)gmail(dot)com
 * @web:			www.pegoraromarco.info
 * 
 */



class UTH {
	
	
	###############################################################################################
	### CONVERSIONS AND CASTING                                                                 ###
	###############################################################################################
	
	/**
	 * Transform a dotted convention string into an array with paths.
	 *
	 * @param string $path	Dotted string
	 * @return array
	 */
	function dots2array( $path ) {
		if (is_string($path)) {
			if (strpos($path, ".")) {
				return explode(".", $path);
			}
			return array($path);
		}
		return $path;
	} // EndOf: "dots2array()" ####################################################################
	
	/**
	 * Transform a querystring like input into ad array.
	 *
	 * @param unknown_type $str
	 * @return unknown
	 */
	function queryString2array( $str ) {
		$ret = array();
		
		foreach ( explode('&',$str) as $trunk ) {
			list( $key, $val ) = explode( '=', $trunk );
			$ret[$key] = $val;
		}
		
		return $ret;
	} // EndOf: "queryString2array()" #############################################################
	
	
	
	
	
	
	
	
	
	###############################################################################################
	### STRING                                                                                  ###
	###############################################################################################
	
	/**
	 * It repeat a string "$repeat" time.
	 * It allow to insert some marker into the string.
	 * 
	 * {first}: replaced with $config['first'] for the first element of the list. Replaced with empty value for other elements.
	 * {last}: replaced with $config['last'] for the last element of the list. Replaced with empty value for other elements.
	 * {i}: replaced with an index value starting from 1.
	 * {tot}: replaced with total request repetitions.
	 * 
	 * @return string 
	 * @param string $str[optional] string to repeat
	 * @param int $repeat[optional] how many time to repeat string=
	 */
	function strRepeat( $str = '', $repeat = 0, $config = array() ) {
		
		// Configurazione automatica degli indici di primo e ultimo elemento.                     #
		if ( empty($config['first']) ) 	$config['first'] 	= 'first';
		if ( empty($config['last']) ) 	$config['last'] 	= 'last';
		
		$ret = '';
		for ( $i=0; $i<$repeat; $i++ ) {
			
			$item = $str;
			
			// Sostituzione dell'identificatore di prima posizione.                               #
			if ( $i==0 ) $item = str_replace( '{first}', $config['first'], $item );
			// Sostituzione dell'identificatore di ultima posizione.                              #
			if ( $i==($repeat-1) ) $item = str_replace( '{last}', $config['last'], $item );
			
			// Sostituzione dei dati base della ripetizione.                                      # 
			$item = str_replace(array(
				'{i}',
				'{tot}'
			),array(
				$i+1,
				$repeat,
			),$item);
			
			// Queque item to the complete string.                                                #
			$ret = $ret . $item;
		}
		
		return $ret;
		
	} // EndOf:"strRepeat()" ######################################################################
	
	/**
	 * Aggiunge le slash di url "/" all'inizio ed alla fine di una stringa.
	 *
	 * @param string $str
	 * @return string
	 */
	function slash( $str ) {
		return UTH::removeDoubleChars( '/', "/$str/");
	} // EndOf: "slash()" #########################################################################

	/**
	 * Remove duplicated istances of a char (or string) in a string.
	 *
	 * @param string $char		Character to search and deduplicate.
	 * @param string $str		String to deduplicate.
	 * @return string
	 */
	function removeDoubleChars( $char = '', $str = '' ) {
		
		$str = str_replace( $char.$char.$char.$char, $char, $str );
		$str = str_replace( $char.$char.$char, $char, $str );
		$str = str_replace( $char.$char, $char, $str );
		
		return $str;
		
	} // EndOf: "removeDoubleChars()" #############################################################
	
	function getLastTrunk( $str = '', $sep = '.' ) {
		
		if ( empty($str) ) return $str;
		
		$str = strrev($str);
		$str = substr( $str, 0, strpos( $str, $sep ) );
		$str = strrev($str);
		
		return $str;
		
	} // EndOf: "getLastTrunk()" ##################################################################
	
	function removeLastTrunk( $str = '', $sep = '.' ) {
		
		if ( empty($str) ) return $str;
		
		$str = strrev($str);
		$str = substr( $str, strpos( $str, $sep )+1, strlen($str) );
		$str = strrev($str);
		
		return $str;
		
	} // EndOf: "removeLastTrunk()" ###############################################################
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}


?>