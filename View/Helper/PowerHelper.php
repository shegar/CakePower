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
	
}