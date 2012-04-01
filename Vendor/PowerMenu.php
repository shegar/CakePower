<?php
/**
 * CakePOWER - PowerMenu
 * 
 * PowerMenu is a very simple yet powerful interface to builds menus for your application.
 * 
 * Menus are collections of links. Each menu item can store different kinds of properties depending
 * on it's rendering needs so made use of a non relational db is very useful.
 * 
 * PowerMenu stores a menu hierarchical structure using the PowerConfig interface so menus data can be
 * accessed from everywhere.
 * 
 * @author peg
 *
 */

class PowerMenu {
	
	private static $_basePath = 'PowerMenu';
	
	private static $_defaults = array(
		'show' 		=> '',
		'url'		=> array(),
		'params' 	=> array(),
		'active'	=> false
	);
	
	
	
	
/**	
 * 
 * Used by self::getTree() to build the menuItem data as expected by CakePHP's TreeHelper.
 * 
 * @var string
 */
	public static $displayModel 		= 'PowerMenu';

	
/**
 * 
 * Used to store item's childrenhood data and to output it by the "getTree()" method.
 * @var unknown_type
 */
	public static $children		= 'children';
	
	
	
	
	
	
	
	
	
/**	
 * 
 * It builds path's tree array of descendants.
 * Each node stores it's informations into the {$displayModel} properties
 * and it's descendants into {$children} property.
 * (in this example data structure we use default values)
 * 
 * array(
 * 	 0 => array(
 *       'PowerMenu' => array(
 *           'show' => 'Users',
 *           'url'  => array( 'controller'=>'users' ),
 *       ),
 *       'children' => array(
 *       	 0 => array(
 *               'show' => 'Add User',
 *               'url'  => array( 'controller'=>'users', 'action'=>'add' )
 *           ),
 *           1 => array(
 *               'show' => 'List Users',
 *               'url'  => array( 'controller'=>'users' ),
 *           )
 *       )
 *   ),
 *   1 => array(
 *       'PowerMenu' => array(
 *           'show' => 'Blog\'s Posts',
 *           'url'  => array( 'controller'=>'posts' ),
 *       ),
 *       'children' => array()
 *   ),
 *   2 => array( ... )
 * )
 *  
 *  
 * @param string $path
 */
	public static function getTree( $path = '' ) {
		
		$source = PowerConfig::get( self::_fullPath($path) );
		
		return self::_treeLevel( $source );
	
	}
	
	private static function _treeLevel( $arr = array() ) {
	
		$tree = array();
		
		if ( !PowerSet::is_assoc($arr) ) return false;
		
		foreach ( $arr as $key=>$val ) {
			
			if ( $key == '_info_' ) continue;
			if ( empty($val) ) continue;
			
			// Compose the menu item as expected by the CakePHP's TreeHelper.
			// Menu item's data is stored inside a model name:
			// 
			// $item => array(
			//     $displayModel => array(
			//          _name => 
			//          show =>
			//          url => 
			//     )
			// )
			$item = array(
				self::$displayModel => array_merge(array('_name'=>$key),$val['_info_']) 
			);
			$item[self::$children] = self::_treeLevel($val);
			
			$tree[] = $item;
		
		}
		
		return $tree;
	
	}
	

	
	
	

	
	
	
/**	
 * 
 * Add a menu item to an existing menu or create the $dest menu sructure if does not exists.
 * 
 * @param string $dest
 * @param string $itemName
 * @param array $itemData
 * 
 * PowerMenu::appendTo( 'admin.sidebar', 'users', array(
 *     'show' => 'Users Menu',
 *     'url'  => array( 'controller'=>'users' )
 * )); 
 * 
 */
	
	public static function appendTo( $dest, $itemName = '', $itemData = array() ) {
		
		$itemData = self::_itemData( $itemName, $itemData );
		
		$fullPath = self::_fullPath( $dest );
		
		// Extract children array from the item data.
		$children = $itemData[self::$children];
		unset($itemData[self::$children]);
		
		PowerConfig::extend( $fullPath, $itemData );
		
		// append childrens
		if ( !empty($children) ) {
			
			$itemName = array_keys($itemData);
			$itemName = $itemName[0];
			
			$dest = $dest . '.' . $itemName;
			
			foreach ( $children as $itemName=>$itemData ) self::appendTo( $dest, $itemName, $itemData );
		
		}
		
	}
	
	
	
	
/**	
 * 
 * Insert a new menu item just after the desired path.
 * It is a very useful method to alter existing menus.
 * 
 * @param string $dest
 * @param string $itemName
 * @param array $itemData
 * 
 * 'admin.sidebar' => array(
 *     'users' => array( ... ),
 *     'pages' => array( ... )
 * )
 * 
 * PowerMenu::after('admin.sidebar.users', 'groups', array(
 *     'show' => 'Groups',
 *     'url'  => array( 'controller'=>'groups' )
 * ));
 * 
 * --> 
 * 'admin.sidebar' => array(
 *     'users' => array( ... ),
 *     'groups'=> array( ... ),
 *     'pages' => array( ... )
 * )
 * 
 * 
 */
	
	public static function after( $dest, $itemName = '', $itemData = array() ) {
		
		$itemData = self::_itemData( $itemName, $itemData );
		
		$fullPath = self::_fullPath( $dest );
		
		// Extract children array from the item data.
		$children = $itemData[self::$children];
		unset($itemData[self::$children]);
		
		PowerConfig::after( $fullPath, $itemData );
		
		// append childrens
		if ( !empty($children) ) {
			
			$itemName = array_keys($itemData);
			$itemName = $itemName[0];
			
			$dest = PowerSet::dotsParent($dest) . '.' . $itemName;
			
			foreach ( $children as $itemName=>$itemData ) self::appendTo( $dest, $itemName, $itemData );
		
		}
	
	}
	
	public static function before( $dest, $itemName = '', $itemData = array() ) {
		
		$itemData = self::_itemData( $itemName, $itemData );
		
		$fullPath = self::_fullPath( $dest );
		
		// Extract children array from the item data.
		$children = $itemData[self::$children];
		unset($itemData[self::$children]);
		
		PowerConfig::before( $fullPath, $itemData );
		
		// append childrens
		if ( !empty($children) ) {
			
			$itemName = array_keys($itemData);
			$itemName = $itemName[0];
			
			$dest = PowerSet::dotsParent($dest) . '.' . $itemName;
			
			foreach ( $children as $itemName=>$itemData ) self::appendTo( $dest, $itemName, $itemData );
		
		}
	
	}
	
	
	
	
	
	
	
	
/**	
 * Activate or deactivate a menu item by setting it's "active" param.
 * 
 * @param unknown_type $path
 */
	
	public static function setActive( $path = '' ) {
		
		PowerConfig::set( self::_fullPath($path) . '._info_.active', true );
		
	}
	
	public static function setInactive( $path = '' ) {
		
		PowerConfig::set( self::_fullPath($path) . '._info_.active', false );
		
	}
	
	

	
	
	
	
	
	
	
	
	
	
/**	
 * Debugging utilities
 * 
 * @param unknown_type $path
 */
	public static function debug( $path = '' ) {
		
		debug( self::getTree($path) );
		
	}
	
	public static function ddebug( $path = '' ) {
		
		self::debug($path);
		
		exit;
		
	}
	
	
	
	
	
	
	
	
	
	
	protected static function _itemData( $itemName = '', $itemData = array() ) {
		
		// Allow to use two kind of call with or without 3rd param.
		if ( !empty($itemName) && is_string($itemName) ) {
			$itemData['name'] = $itemName;
			
		} else if ( !empty($itemName) && is_array($itemName) ) {
			$itemData = $itemName;
				
		}
		
		// Export the name property outside the item data.
		if ( Set::check($itemData,'name') ) {
			$itemName = $itemData['name'];
			unset($itemData['name']);
				
		} else {
			$itemName = uniqid();
			
		}
		
		if ( array_key_exists(self::$children,$itemData) ) {
			$children = $itemData[self::$children];
			unset($itemData[self::$children]);
			
		} else {
			$children = array();
		}
		
		$itemData = Set::merge(self::$_defaults,$itemData);
		
		// Set default values
		// empty(show) <- name
		// empty(name) <- show.stringify
		if ( empty($itemData['show']) ) $itemData['show'] = $itemName; 
		
		return array(
			$itemName 					=> array( '_info_'=>$itemData ),
			self::$children 	=> $children
		);
		
	}
	
	
	protected static function _fullPath( $path ) {
		
		// Full path for the menu root.
		if ( empty($path) || $path == '.' ) return self::$_basePath;
		
		$_path = array();
		
		foreach ( PowerSet::dots2array($path) as $_subPath ) {
			
			$_path[] = $_subPath;
			
			PowerConfig::def( self::$_basePath . '.' . PowerSet::array2dots($_path), array( '_info_'=>self::$_defaults ));
			
		}
		
		return self::$_basePath . '.' . $path;
		
	}
	
	
	
	
	
	
	
}