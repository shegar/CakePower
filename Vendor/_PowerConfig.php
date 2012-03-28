<?php
/**
 * CakePower.org
 * Configuration Class
 * PowerConfig solves configuration problem with a globally accessible key-related db.
 * 
 * @author:			Marco Pegoraro
 * @mail:			marco(dot)pegoraro(at)gmail(dot)com
 * @web:			www.pegoraromarco.info
 * 
 */


class PowerConfig {
	
	var $db = array();
	
	/**
	 * Return singletone istance of JC class.
	 *
	 */
	function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] =& new PowerConfig();
		}
		return $instance[0];
	}
	
	
	
	
	###############################################################################################
	### Public Methods                                                                          ###
	###############################################################################################
	
	/**
	 * Check for an entry to exists.
	 *
	 * @param string $path	Entry's path.
	 * @return boolean
	 */
	function exists( $path = '' ) {
		
		$_this =& PowerConfig::getInstance();
		
		$path = $_this->__dbStr($path);
		
		$code = 'if ( isset('.$path.') ) { return true; } else {return false;}';
		
		return eval($code);
		
	} // EndOf: "exists()" ########################################################################
	
	/**
	 * Read an entry value and autput it.
	 *
	 * @param string $path	Entry's path.
	 * @return mixed		Entry's value can be string, boolean, array, ecc...
	 */
	function read( $path = '', $elseValue = '' ) {
		$_this =& PowerConfig::getInstance();
		
		if ( $_this->exists( $path ) ) {
			return $_this->__get( $path );
		}
		return $elseValue;
		
	} // EndOf: "read()" ##########################################################################
	
	
	/**
	 * Avvia la ricerca di una path contestualizzata alla richiesta in corso cercandola
	 * nelle contestualizzazioni per controller/metodo, etc.
	 * 
	 * E' possibile specificare un valore di default che viene utilizzato in caso di valore nullo
	 * del risultato ricercato.
	 * 
	 * @return 
	 * @param object $path
	 * @param object $defaultValue[optional]
	 */
	function app( $path, $defaultValue = '' ) {
		$_this =& PowerConfig::getInstance();
		
		$params = $_this->read('request.params');
		if ( empty($params) ) return;
		
		// PowerConfig prefix to the contestualized database.                                             #
		$prefix = 'request.app.';
		
		// Contenitore del risultato dell'interrogazione.                                         #
		$outputValue = '';
		
		// controller.action
		$tmp = $prefix . $params['controller'] . '.' .$params['action'] . '.' . $path;
		if ( empty($outputValue) && $_this->exists($tmp) ) $outputValue = $_this->read( $tmp );
		
		// controller
		$tmp = $prefix . $params['controller'] . '.' . $path;
		if ( empty($outputValue) && $_this->exists($tmp) ) $outputValue = $_this->read( $tmp );
		
		// *.action
		$tmp = $prefix . '*.' . $params['action'] . '.' . $path;
		if ( empty($outputValue) && $_this->exists($tmp) ) $outputValue = $_this->read( $tmp );
		
		// app
		$tmp = 'app.' . $path;
		if ( empty($outputValue) && $_this->exists($tmp) ) $outputValue = $_this->read( $tmp );
		
		// Set default value.
		if ( empty($outputValue) ) $outputValue = $defaultValue;
		
		return $outputValue;
		
	} // EndOf: "app()" ###########################################################################
	
	
	function debug( $path = '', $showHtml = true, $showFrom = true ) {
		
		$_this =& PowerConfig::getInstance();
		debug( $_this->read($path), $showHtml, $showFrom );
		
	} // EndOf: "debug()" #########################################################################
	
	
	function ddebug( $path = '', $showHtml = true, $showFrom = true ) {
		
		$_this =& PowerConfig::getInstance();
		debug( $_this->read($path), $showHtml, $showFrom );
		exit;
		
	} // EndOf: "ddebug()" ########################################################################
	
	function on( $path = '' ) {
		
		$_this =& PowerConfig::getInstance();
		
		$value = $_this->read( $path );
		
		return strtoupper($value) == 'ON';
		
	} // EndOf: "on()" ############################################################################
	
	function off( $path = '' ) {
		
		$_this =& PowerConfig::getInstance();
		
		$value = $_this->read( $path );
		
		return strtoupper($value) == 'OFF';
		
	} // EndOf: "off()" ###########################################################################
	
	function isEmpty( $path = '' ) {
		$_this =& PowerConfig::getInstance();
		
		$value = $_this->read( $path );
		
		return empty($value);
		
	} // EndOf: "isEmpty()" #######################################################################
	
	function notEmpty( $path = '' ) {
		$_this =& PowerConfig::getInstance();
		
		$value = $_this->read( $path );
		
		return !empty($value);
		
	} // EndOf: "notEmpty()" ######################################################################
	
	function isArray( $path = '' ) {
		$_this =& PowerConfig::getInstance();
		
		$value = $_this->read( $path );
		
		return is_array($value);
		
	} // EndOf: "isArray()" #######################################################################
	
	function notArray( $path = '' ) {
		$_this =& PowerConfig::getInstance();
		
		$value = $_this->read( $path );
		
		return !is_array($value);
		
	} // EndOf: "notArray()" ######################################################################
	
	/**
	 * Write or overwrite a path with a new value.
	 *
	 * @param string $path	Entry's path.
	 * @param mixed $value
	 */
	function write( $path = '', $value = '' ) {
		$_this =& PowerConfig::getInstance();
		
		$_this->__set( $path, $value );
		#PowerConfig::__set( $path, $value );
		
	} // EndOf: "write()" #########################################################################
	
	/**
	 * Delete an entry.
	 *
	 * @param string $path	Entry's path.
	 * @return boolean
	 */
	function delete( $path = '' ) {
		$_this =& PowerConfig::getInstance();
		
		return eval( 'if ( isset('.$_this->__dbStr($path).') ) unset('.$_this->__dbStr($path).');' );
	} // EndOf: "delete()" ########################################################################
	
	/**
	 * This method extend an existing path (or create one) with new values.
	 * It override string or numeric values and extends arrays with "Set".
	 *
	 * @param string $path	Entry's path.
	 * @param mixed $value	Entry's extension.
	 */
	function extend( $path = '', $value = '' ) {
		
		// If desired path doesn't exists will be created with new value without extend anything. #
		if ( !PowerConfig::exists($path) ) return PowerConfig::write( $path, $value);
		
		// Ottengo l'array di configurazione attuale per la chiave desiderata.                    #
		// Questo è l'array di configurazione da estendere.                                       #
		$path_array = PowerConfig::read($path);
		
		// Se il valore di default è scalare e la chiave è vuota viene inserita in modo diretto.  #
		if ( !is_array($value) ) {
			$_this =& PowerConfig::getInstance();
			$_this->__set( $path, $value );
			return;
		}
		
		// I use "Set" core library to extend desired path with new values.                       #
		PowerConfig::write( $path, Set::merge( $path_array, $value ) );
		
	} // EndOf: "extend()" ########################################################################
	
	/**
	 * This method is similar to "extend" but might be used with array data structures.
	 *
	 * @param unknown_type $path
	 * @param unknown_type $value
	 */
	function append( $path = '', $value = '' ) {
		
		// Init a non-array path.                                                                 #
		if ( !PowerConfig::isArray($path) ) PowerConfig::write( $path, array() );
		
		// Append values to existing array.                                                       #
		$array = PowerConfig::read($path);
		$array[] = $value;
		
		// Save data to Configuration.                                                            #
		PowerConfig::write( $path, $array );
		
	} // EndOf: "append()" ########################################################################
	
	/**
	 * This method set default values for a configuration array structure.
	 */
	function def( $path = '', $value = '' ) {
		
		$_this =& PowerConfig::getInstance();
		
		// Nel caso il valore di default sia scalare ma la chiave è esistente essa non può        #
		// essere sovrascritta in quanto già settata anche con valore nullo.                      #
		// NOTA: Il valore di default viene settato unicamente per chiavi non esistenti!          #
		if ( !is_array($value) && PowerConfig::exists( $path) ) return;
		
		// Ottengo l'array di configurazione attuale per la chiave desiderata.                    #
		// Questo è l'array di configurazione da modificare con i valori di default.              #
		$path_array = $_this->__get( $path );
		
		// Se il valore di default è scalare e la chiave è vuota viene inserita in modo diretto.  #
		if ( empty($path_array) && !is_array($value) ) {
			$_this->__set( $path, $value );
			return;
		}
		
		// Se il valore di default è un array ma la chiave è vuota viene scritta in modo diretto. #
		if ( is_array($value) && empty($path_array) ) {
			$_this->__set( $path, $value );
			return;
		}
		
		// Imposto le differenze dell'array utilizzando l'apposito metodo di Set.                 #
		$_this->__set( $path, Set::pushDiff( $path_array, $value ) );
		
	} // EndOf: "def()" ###########################################################################
	
	
	
	
	
	
	
	###############################################################################################
	### GETTER & SETTER                                                                         ###
	###############################################################################################
	
	function get() {
		
		$_this =& PowerConfig::getInstance();
		return $_this->db;
		
	} // EndOf: "get()" ###########################################################################
	
	function set( $db ) {
		
		$_this =& PowerConfig::getInstance();
		$_this->db = $db;
		
	} // EndOf: "set()" ###########################################################################
	
	
	
	
	
	
	
	
	###############################################################################################
	### SERIALIZATION                                                                           ###
	###############################################################################################
	
	function serialize( $path = '' ) {
		
		return serialize(PowerConfig::read($path));
		
	} // EndOf: "serialize()" #####################################################################
	
	function unserialize( $source, $path = '' ) {
		
		PowerConfig::write( $path, unserialize($source) );
		
	} // EndOf: "unserialize()" ###################################################################
	
	
	
	
	
	
	
	
	
	
	
	
	###############################################################################################
	### INTERNAL METHODS                                                                        ###
	###############################################################################################
	
	function __dbStr( $path ) {
		$str = '';
		foreach ( UTH::dots2array($path) as $el ) if ( !empty($el) ) $str.= '[\''.$el.'\']';
		
		return '$_this->db'.$str; 
	}
	
	function __get( $path ) {
		$_this =& PowerConfig::getInstance();
		
		eval ( '$value = @'.$_this->__dbStr( $path ).';' );
		return $value;
		
	}
	
	function __set( $path, $value ) {
		$_this =& PowerConfig::getInstance();
		
		// ?? $_this->__makeArray( $path );
		eval ( $_this->__dbStr( $path ).' = $_this->__valueCleaner($value);');
	}
	
	function __valueCleaner( $value ) {
		
		if ( is_bool($value) ) 		return $value;
		if ( is_numeric($value) ) 	return $value;
		if ( is_array($value) ) 	return $value;
		return str_replace( '\'', '\\\'', $value );
		
	}
	
	/**
	 * Dovrebbe risolvere il problema descritto in:
	 * http://informationideas.com/news/2006/06/14/fatal-error-cannot-use-string-offset-as-an-array-in/
	 * 
	 *
	 * @param unknown_type $path
	 */
	function __makeArray( $path ) {
		
		if ( count(explode('.',$path)) > 1 ) {
			
			$sub_path = UTH::removeLastTrunk($path);
			
			if ( PowerConfig::exists($sub_path) ) {
				if ( !PowerConfig::isArray($sub_path) ) PowerConfig::write($sub_path,array());
			} else {
				
				//PowerConfig::__makeArray($sub_path);
				
				PowerConfig::write($sub_path,array() );
				
				
			}
			
		}
		
	}	
	
}
