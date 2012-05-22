<?php
/**
 * PowerHtmlHelper
 * Extends CakePHP core's HtmlHelper adding usefull features and behaviors.
 */

App::import( 'View/Helper', 'HtmlHelper' );


// Less Support Libraries.
App::uses('Folder',		'Utility');
App::uses('File',		'Utility');
App::uses('Component',	'Controller');
App::uses('lessc',		'CakePower.Vendor');
// Used to store less temporary files.
define( 'CACHE_LESS', CACHE . 'less' . DS );




class PowerHtmlHelper extends HtmlHelper {
	
	
	
	
	
	public function __construct(View $View, $settings = array()) {
		
		// Adds tags to the form helper.
		$this->_tags['thead'] = '<thead%s>%s</thead>';
		$this->_tags['tbody'] = '<tbody%s>%s</tbody>';
		
		parent::__construct($View, $settings);
		
		// Setup Less parsing folders.
		$this->lessFolder 	= new Folder(WWW_ROOT.'less', true, 0755 );
		$this->lessCache	= new Folder(CACHE_LESS, true, 0755 );
		$this->cssFolder 	= new Folder(WWW_ROOT.'css', true, 0755 );
		
	}
	
	
	
	
	
	
	
	
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
				if ( isset($opt['rel']) ) {
					$_rel = $opt['rel'];
					unset($opt['rel']);
				}
				
				$out .= "\n\t" . $this->css($i, $_rel, PowerSet::merge($options,$opt) );			
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
			
			/** @@CakePOWER@@ **/
			// Compile less source to the css output file.
			// If debug > 0 
			$source = $this->lessFolder->path.DS.$path.'.less';
			$target = $this->cssFolder->path.DS.$path.'.css';
			if ( ( !file_exists($target) || Configure::read('debug') ) && file_exists($source) ) $this->auto_compile_less($source, $target);
			/** --CakePOWER-- **/
			
			
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
	
	
	
	
	
	
	
/**
 * tag()
 * ------------------------------------
 * add possibility to nest multiple tags inside $text property.
 * 
 * // The CakePHP way:
 * $text = 'This is a ';
 * $text.= $this->Html->link( 'link', 'http://cakepower.org' );
 * echo $this->Html->tag( 'p', $text );
 * 
 * // The CakePower way:
 * echo $this->Html->tag( 'p', array(
 *     'This is a ',
 *     $this->Html->link( 'link', 'http://cakepower.org' )
 * ));
 * 
 * You can nest how many declarations as you wish/need containing code verbosity of
 * declarate many temporary vars!
 * 
 * 
 * @param unknown_type $name
 * @param unknown_type $text
 * @param unknown_type $options
 */	
	public function tag($name, $text = null, $options = array()) {
		
		if ( is_array($text) ) {
			
			$_text = $text;
			
			$text = '';
			
			foreach ( $_text as $item ) {
				
				$text.= $item;
			
			}
			
		}
		
		// Use the CakePHP's parent method to output the HTML source.
		return parent::tag( $name, $text, $options );
	
	}
	
	
	
	
	
/**	
 * Interface to an authorization layer.
 * Test an if an url can be accessed. 
 * 
 * Return values:
 * true: 	the url can be accessed
 * false: 	the url is denied
 * null: 	it is no possibile to check the url (external urls...)
 */
	public function allowUrl( $url = '' ) { return true; }
	
	
	
	
	
	
	
	
	
	
	

	
/**	
 * Actions Link
 * these methods expose some actions that user may use in the view AS CONCEPTS.
 * 
 * Each method generates a simple link that implement a class.
 * It is a UI assets stuff to apply css rules and behaviors to that class!
 * 
 * Some UI kit like Twitter Bootstrap supplies some action driven components (aka buttons)
 */
	
	
	public function action( $url = array(), $options = array() ) {
		
		if ( is_string($options) ) $options = array( 'text'=>$options );
		
		$options += array( 'text'=>'', 'class'=>'ui-action' );
		
		if ( strpos($options['class'],'ui-action') === false ) $options['class'] = 'ui-action ' . $options['class'];
		
		// data-icon
		if ( isset($options['icon']) ) {
			$options['data-icon'] = $options['icon'];
			unset($options['icon']);
		}
		
		// data-confirm-msg
		if ( isset($options['confirm']) ) {
			if ( !is_array($options['confirm']) ) $options['confirm'] = array( 'msg'=>$options['confirm'] );
			$options['data-confirm-msg'] = $options['confirm']['msg'];
			unset($options['confirm']);
		}
		
		$text = $options['text'];
		unset($options['text']);
		
		return $this->link( $text, $url, $options );
		
	}
	
	public function editAction( $url = array(), $options = array() ) {
		
		if ( is_string($options) ) $options = array( 'text'=>$options );
		
		$options += array( 'text'=>'edit', 'class'=>'' );
		
		if ( strpos($options['class'],'ui-action-edit') === false ) {
			$options['class'] = 'ui-action-edit ' . $options['class'];	
		}
		
		return $this->action( $url, $options );
		
	}
	
	public function deleteAction( $url = array(), $options = array() ) {
		
		if ( is_string($options) ) $options = array( 'text'=>$options );
		
		$options += array( 'text'=>'delete', 'class'=>'' );
		
		if ( strpos($options['class'],'ui-action-delete') === false ) {
			$options['class'] = 'ui-action-delete ' . $options['class'];
		}
		
		return $this->action( $url, $options );
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
/**	
 * Less Integration methods
 * It parse a LESS file into a CSS.
 */
	protected function auto_compile_less($lessFilename, $cssFilename) {
		
		// Check if cache & output folders are writable and the less file exists.
		if (!is_writable(CACHE.'less')) {
			trigger_error(__d('cake_dev', '"%s" directory is NOT writable.', CACHE.'less'), E_USER_NOTICE);
			return;
		}
		
		if (file_exists($lessFilename) == false) {
			trigger_error(__d('cake_dev', 'File: "%s" not found.', $lessFilename), E_USER_NOTICE);
			return;
		}

		// Cache location
		$cacheFilename = CACHE.'less'.DS.str_replace('/', '_', str_replace($this->lessFolder->path, '', $lessFilename).".cache");

		// Load the cache
		if (file_exists($cacheFilename)) {
			$cache = unserialize(file_get_contents($cacheFilename));
		} else {
			$cache = $lessFilename;
		}

		$new_cache = lessc::cexecute($cache);
		if (!is_array($cache) || $new_cache['updated'] > $cache['updated'] || file_exists($cssFilename) === false) {
			$cssFile = new File($cssFilename, true);
			if ($cssFile->write($new_cache['compiled']) === false) {
				if (!is_writable(dirname($cssFilename))) {
					trigger_error(__d('cake_dev', '"%s" directory is NOT writable.', dirname($cssFilename)), E_USER_NOTICE);
				}
				trigger_error(__d('cake_dev', 'Failed to write "%s"', $cssFilename), E_USER_NOTICE);
			}

			$cacheFile = new File($cacheFilename, true);
			$cacheFile->write(serialize($new_cache));
		}
		
	}
	
	
	
	
	
	

}