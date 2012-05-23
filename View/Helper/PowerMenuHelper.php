<?php
/**
 * CakePower - PowerMenu (Helper)
 * -------------------------------------------
 * 
 * Extends PowerTreeHelper to creates lists or another kind of data from
 * a PowerMenu path.
 * 
 */

App::import( 'Helper', 'CakePower.PowerTree' );


class PowerMenuHelper extends PowerTreeHelper {

	public function generate( $path, $config = array() ) {
		
		$tree = PowerMenu::getTree($path);
		
		$config += array( 'callable' => new PowerMenuHelper__TreeHelperExtension );
		
		return parent::generate( $tree, $config );
		
	}

}

class PowerMenuHelper__TreeHelperExtension extends TreeHelperExtension {
	
	function displayLogic( $node ) {
		
		return $this->subject()->Html->link( $node['PowerMenu']['show'], $node['PowerMenu']['url'] );
		
	}

}