<?php
App::uses('Model', 'Model');

class PowerModel extends Model {

	
	
	
	
	public function loadModel( $model = '' ) {
		
		if ( ClassRegistry::isKeySet($model) ) {
			$object = ClassRegistry::getObject($model);
			
		} else {
			$object = ClassRegistry::init($model, true);
			
		}
		
		$this->{$model} = $object;
		
		return $object;
		
	}
	
	
	
	
	
}