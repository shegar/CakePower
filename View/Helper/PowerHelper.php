<?php
/**
 * CakePower :: Layer Helper 
 */

App::uses('Helper', 'View');

class PowerHelper extends Helper {
	

	
	
	/**
	 * This method was cloned from the CakePHP's FormHelper
	 * This is a convenient way to lazy loads models into the helper.
	 */
	protected function _getModel($model) {
		
		$object = null;
		
		if (!$model || $model === 'Model') {
			return $object;
		}
		
		// This row was added because _models propery may not be defines!
		if ( empty($this->_models) ) $this->_models = array();

		if (array_key_exists($model, $this->_models)) {
			return $this->_models[$model];
		}

		if (ClassRegistry::isKeySet($model)) {
			$object = ClassRegistry::getObject($model);
			
		} elseif (isset($this->request->params['models'][$model])) {
			$plugin = $this->request->params['models'][$model]['plugin'];
			$plugin .= ($plugin) ? '.' : null;
			$object = ClassRegistry::init(array(
				'class' => $plugin . $this->request->params['models'][$model]['className'],
				'alias' => $model
			));
			
		} else {
			$object = ClassRegistry::init($model, true);
			
		}

		$this->_models[$model] = $object;
		
		if (!$object) {
			return null;
		}

		$this->fieldset[$model] = array('fields' => null, 'key' => $object->primaryKey, 'validates' => null);
		return $object;
	}
	
	
	
	
	/**
	 * Loada Model object into the Helper instance.
	 * Enter description here ...
	 * @param unknown_type $model
	 * @param unknown_type $set
	 */
	public function loadModel( $model, $set = true ) {
		
		$obj = $this->_getModel( $model );
		
		if ( $obj === null ) return false;
		
		if ( $set !== false ) $this->{$model} = $obj;
		
		return $obj;
	
	}
	
	
	
	
	
	
	
	
	

	
	
/**	
 * Utility Method
 * take an asset path as used for the HtmlHelper::css() (or similar) and output the 
 * full file path (if exists) or false if does not.
 * 
 * @param unknown_type $path
 * @param unknown_type $options
 */
	public function assetPath( $path, $options = array() ) {
		
		$options += array( 'exists'=>true );
		
		// Absolute url path
		if (strpos($path, '://') !== false) return $path;

		if (!array_key_exists('plugin', $options) || $options['plugin'] !== false) {
			list($plugin, $path) = $this->_View->pluginSplit($path, false);
		}
		
		// Find the base path for the requested file.
		// It may use plugin notation (Plugin.asset) to refer to an asset hosted into a plugin package.
		// 
		// NOTE: an asset hosted in a plugin package may exists internally to the plugin or may
		// exists copied to the webroot in a plugin's named folder (for optimization)
		// The "base" points to the optimized folder
		// The "fallback" points to the plugin package folder
		$base = $fallback = WWW_ROOT;
		if ( !empty($plugin) && PowerConfig::exists('plugin.'.$plugin.'._info.path') ) {
			$base 		= $base . strtolower($plugin) . DS;
			$fallback 	= PowerConfig::get('plugin.'.$plugin.'._info.path') . 'webroot' . DS;
		}
		
		// Add the path prefix to the asset request.
		if (!empty($options['pathPrefix']) && $path[0] !== '/') {
			$base 		.= $options['pathPrefix'];
			$fallback 	.= $options['pathPrefix'];
		}
		
		// Add the extension to the file path if required.
		if (
			!empty($options['ext']) &&
			strpos($path, '?') === false &&
			substr($path, -strlen($options['ext'])) !== $options['ext']
		) {
			$path .= $options['ext'];
		}
		
		// Check for files existance and return correct path.
		if ( file_exists($base.$path) ) 		return $base.$path;
		if ( file_exists($fallback.$path) ) 	return $fallback.$path;
		
		if ( $options['exists'] ) return false;
		return $fallback.$path;
	
	}
	
	
	
	
	
}