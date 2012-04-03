<?php
/**
 * CakePower bootstrap.
 *
 * Allow plugins load dinamically.
 */



// Used to calculate execution time.
define( 'POWER_START', microtime() );



/**
 * Import Libraries.
 */

App::import( 'Vendor', 'CakePower.Basics' );
App::import( 'Vendor', 'CakePower.Uth' );
App::import( 'Vendor', 'CakePower.PowerSet' );
App::import( 'Vendor', 'CakePower.PowerConfig' );
App::import( 'Vendor', 'CakePower.PowerMenu' );
App::import( 'Vendor', 'CakePower.PowerApp' );

App::uses( 'CakePowerController', 'CakePower.Controller' );














/**
 * PowerConfiguration
 * this class works like CakePhp's Configure but allow to perform a lot of operation
 * to a tree data.
 */
PowerConfig::set(array(
	
	// store info about running application.
	'app' => array(
	
		// CakePlugin::loaded() list after this bootstrap
		'plugins' => array(),
	
	),
	
	
	
	// store info about the actual request.
	'request' => array(),
	
	
	// store info about plugin's configuration.
	// for each plugin is sored it's path, loading order, load configuration
	//
	// PowerConfig::get('plugin.MyPlugin.path');
	// PowerConfig::get('plugin.MyPlugin.order');
	// PowerConfig::get('plugin.MyPlugin.load.bootstrap');
	//
	// There is a key "config" where each plugin can store internal configurations.
	'plugin' => array(
		
		// Internal configuration for CakePower
		'CakePower' => array(
			
			// This key will contain all menus defined in the sistem and handler by the PanelMenu class and the PanelMenu Helper
			'menu' => array(
				
				// Container for admin menus
				'admin' => array(),
				
				// Container for public menus
				'public' => array()
				
			)
	
		)
	
	),

));
















/**
 * Plugins automagically management.
 *
 * plugins are listed like in a CakePlugin::loadAll() action but here we look for
 * 3 Config files existance to configure the CakePlugin::load() method.
 *
 * bootrap.php and routes.php
 * if these files exists a plugin load configuration is automagically given
 * to the CakePlugin::load() method.
 *
 * plugin.php
 * if this file exits it is included into the flow.
 * 
 * This file may define a $config = array(); to be merged with the $plugin info.
 * Here is the place to manually alter the loading order of the plugin.
 */

$plugins = array();

foreach ( App::objects('plugins') as $plugin ) {
	
	foreach (App::path('plugins') as $path) {
		
		if ( is_dir( $path . $plugin ) ) {
			
			// Collect plugin's informations.
			$pluginConfig = array(
				'_info' => array(
					'name' 		=> $plugin,
					'base' 		=> $path,
					'path'		=> $path . $plugin . DS,
					'load' 		=> array(),
				),
				'order' => 5000,
			);
			
			
			// CakePower is not being loaded but it's info is quequed as other plugins!
			if ( $plugin == 'CakePower' ) {		
				PowerConfig::set( 'plugin.CakePower', $pluginConfig);
			
			// Try to auto configure login loading settings.
			} else {
			
				// Look for bootstrap and routes existance to configure CakePlugin::load()
				if ( file_exists($pluginConfig['_info']['path'] . 'Config' . DS . 'bootstrap.php' )) 	$pluginConfig['_info']['load']['bootstrap'] 	= true;
				if ( file_exists($pluginConfig['_info']['path'] . 'Config' . DS . 'routes.php' )) 		$pluginConfig['_info']['load']['routes'] 		= true;
			
				// Look for a plugin.php configuration file to extend plugin informations.
				// This file may define a $config array to be merged with the actual plugin's informations.
				if ( file_exists($pluginConfig['_info']['path'] . 'Config' . DS . 'plugin.php' )) {
					
					$plugin = array();
					require_once($pluginConfig['_info']['path'] . 'Config' . DS . 'plugin.php');
					
					// Extend the load key configuration.
					if ( array_key_exists('load', $plugin) ) {
						$pluginConfig['_info']['load'] = array_merge($pluginConfig['_info']['load'],$plugin['load']);
						unset($plugin['load']); 
					}
					
					// Extend plugin's configurations.
					$pluginConfig = array_merge($pluginConfig,$plugin);
					
				}
			
				$plugins[] = $pluginConfig;
			
			}
			
		}
		
	}
	
}


// Sort Plugins to match configuration order.
$plugins = Set::sort( $plugins, '{n}.order', 'asc' );




// Load Plugins and write PowerConfig database.
foreach ( $plugins as $plugin ) {
	
	if ( $plugin == 'CakePower' ) continue;
	
	CakePlugin::load( $plugin['_info']['name'], $plugin['_info']['load'] );
	
	// Check if plugin has been loaded and queque it into the PowerConfig data.
	if ( !CakePlugin::loaded($plugin['_info']['name']) ) continue;
	PowerConfig::set( 'plugin.'.$plugin['_info']['name'], $plugin );
	
}

// Set up loaded plugins info to the app configuration key.
PowerConfig::set( 'app.plugins', CakePlugin::loaded() );




#PowerConfig::debug();
#powerTime();
