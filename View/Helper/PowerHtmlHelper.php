<?php
/**
 * PowerHtmlHelper
 * Extends CakePHP core's HtmlHelper adding usefull features and behaviors.
 */

App::import( 'View/Helper', 'HtmlHelper' );

class PowerHtmlHelper extends HtmlHelper {
	
	
/**	
 * css()
 * ------------------------------------
 * add possibility to 
 * - handle conditional CSS inclusions
 * - define per-item options
 * 
 * INLINE CSS:
 * echo $this->Html->css( 'ie-style', null, array( 'if'=>'IE', 'media'=>'screen' ));
 * 
 * INCLUDE A LIST OF CSS TO THE VIEW'S "css" BLOCK:
 * $this->Html->css(array(
 *     'all',
 *     'print'   => array( 'media'=>'print' ),
 *     'mobile'  => array( 'media'=>'all and (max-width:500px)' ),
 *     'ie'      => array( 'if'=>'ie' ), 
 *     'old-ie'  => 'lte IE 8'
 * ),null,array( 'inline'=>false ));
 * 
 * 
 */
	public function css($path, $rel = null, $options = array()) {
		
		$options += array('block' => null, 'inline' => true);
		
		if (!$options['inline'] && empty($options['block'])) {
			$options['block'] = __FUNCTION__;
		}
		unset($options['inline']);
		

		if (is_array($path)) {
			$out = '';
			
			
			/** @@CakePOWER@@ **/
			/*
			foreach ($path as $i) {
				$out .= "\n\t" . $this->css($i, $rel, $options);
			}
			*/
			/**
			 * DOC:
			 * each css file in an array list may be defined as a simple file name ("style.css" or "style")
			 * or can be defined as an associative array with the file name as key and an options array as
			 * detailed options to be used for this item only.
			 * 
			 * array(
			 *     'css1',
			 *     'css2',
			 *     'ie-css' => array( 'if'=>'IE' ),
			 *     'ie7-css' => array( 'if'=>'IE 7' ),
			 *     'old-ie' => 'lte IE 8' // "if" is the default option listen if a scalar value is given
			 * )
			 * 
			 * for conditional expression documentation read this:
			 * http://www.quirksmode.org/css/condcom.html
			 * 
			 */
			foreach ($path as $i=>$opt) {
				
				// Default scalar value for associative declarations.
				if ( !is_array($opt) && !is_numeric($i) ) $opt = array( 'if'=>$opt );
				
				// Css file only declarations.
				if ( !is_array($opt) ) {
					$i 		= $opt;
					$opt 	= array();
				}
				
				// Allow to per-item $rel param configuration
				$_rel = $rel;
				if ( !isset($opt['rel']) ) {
					$_rel = $opt['rel'];
					unset($opt['rel']);
				}
				
				$out .= "\n\t" . $this->css($i, $rel, PowerSet::merge($options,$opt) );			
			}
			/** --CakePOWER-- **/
			
			
			
			
			if (empty($options['block'])) {
				return $out . "\n";
			}
			return;
		}

		if (strpos($path, '//') !== false) {
			$url = $path;
		} else {
			$url = $this->assetUrl($path, $options + array('pathPrefix' => CSS_URL, 'ext' => '.css'));

			if (Configure::read('Asset.filter.css')) {
				$pos = strpos($url, CSS_URL);
				if ($pos !== false) {
					$url = substr($url, 0, $pos) . 'ccss/' . substr($url, $pos + strlen(CSS_URL));
				}
			}
		}

		if ($rel == 'import') {
			
			/** @@CakePOWER@@ **/
			#$out = sprintf($this->_tags['style'], $this->_parseAttributes($options, array('inline', 'block'), '', ' '), '@import url(' . $url . ');');
			$out = sprintf($this->_tags['style'], $this->_parseAttributes($options, array('inline', 'block', 'if', 'debug' ), '', ' '), '@import url(' . $url . ');');
			/** --CakePOWER-- **/
			
		} else {
			if ($rel == null) {
				$rel = 'stylesheet';
			}
			
			/** @@CakePOWER@@ **/
			#$out = sprintf($this->_tags['css'], $rel, $url, $this->_parseAttributes($options, array('inline', 'block'), '', ' '));
			$out = sprintf($this->_tags['css'], $rel, $url, $this->_parseAttributes($options, array('inline', 'block', 'if', 'debug' ), '', ' '));
			/** --CakePOWER-- **/
			
		}
		
		
		/** @@CakePOWER@@ **/
		if ( !empty($options['if']) ) {
			$out = '<!--[if ' . $options['if'] . ']>' . $out . '<![endif]-->';
		}
		
		if ( !empty($options['debug']) ) {
			$out = "\r\n" . $out;	
		}
		/** --CakePOWER-- **/
		

		if (empty($options['block'])) {
			return $out;
		} else {
			$this->_View->append($options['block'], $out);
		}
		
	}
	

}