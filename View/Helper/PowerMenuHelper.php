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
		
		return parent::generate( $tree, $config );
		
	}

}