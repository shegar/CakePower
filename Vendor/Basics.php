<?php

function ddebug( $var = false, $showHtml = null, $showFrom = true ) {
	
	debug( $var, $showHtml, $showFrom );

	exit;
	
}





function powerTime( $print = true, $die = true ) {
	
	$time 	= round(microtime() - POWER_START, 4);
	$stime	= $time . 's.';
	
	if ( $print === 2 && Configure::read('debug') ) { debug($stime); }
	elseif ( $print ) { echo $stime; }
	
	if ( $die ) die();
	
	return $time;
	
} // EndOf: "powerTime()" #########################################################################

function averageTime( $print = true, $die = true ) {
	
	$time = powerTime(false,false);
	
	$_logPath = TMP . 'averageTime.txt';
	
	//if ( $reset === true ) @unlink($_logPath);
	
	if ( file_exists($_logPath) ) {
		$log = unserialize(file_get_contents($_logPath));
		if ( !is_array($log) ) unset($log);
	}
	
	if ( empty($log) ) {
		$log = array(
			'sum' 	=> 0,
			'tot'	=> 0,
			'avg'	=> 0,
			'time' 	=> array(),
		);
		
	}
	
	$log['time'][] = $time;
	$log['sum'] += $time;
	$log['tot'] += 1;
	$log['avg'] = round( $log['sum'] / $log['tot'], 4 );
	
	@file_put_contents( $_logPath, serialize($log) );
	
	if ( $print === 3 && Configure::read('debug') ) { debug($log); }
	elseif ( $print === 3 ) { print_r($log); }
	elseif ( $print === 2 && Configure::read('debug') ) { debug($log['avg'].' s.'); }
	elseif ( $print ) { echo $log['avg'].' s.'; }
	
	if ( $die ) die();
	
	return $log;
	
} // EndOf: "averageTime()" #######################################################################

function resetAverageTime() {
	
	@unlink( TMP . 'averageTime.txt' );
	
} // EndOf: "resetAverageTime()" ##################################################################