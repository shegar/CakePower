<?php

App::import( 'View/Helper', 'PaginatorHelper' );

class PowerPaginatorHelper extends PaginatorHelper {
	
	
	
	
	
	
/**	
 * Add the "currentLink" option (boolean) to force che Paginator helper to use
 * a link even for the current item.
 * 
 * Default behavior does not use a link.
 */
	public function numbers($options = array()) {
		if ($options === true) {
			$options = array(
				'before' => ' | ', 'after' => ' | ', 'first' => 'first', 'last' => 'last'
			);
		}

		$defaults = array(
			'tag' => 'span', 'before' => null, 'after' => null, 'model' => $this->defaultModel(), 'class' => null,
			'modulus' => '8', 'separator' => ' | ', 'first' => null, 'last' => null, 'ellipsis' => '...', 'currentClass' => 'current'
		);
		$options += $defaults;

		$params = (array)$this->params($options['model']) + array('page' => 1);
		unset($options['model']);

		if ($params['pageCount'] <= 1) {
			return false;
		}
		
		/** @@CakePower@@
		 * remove the "currentClass" options from the list.
		 * "currentClass" is used to implement link even for the link to the current page.
		 */
		/*
		extract($options);
		unset($options['tag'], $options['before'], $options['after'], $options['model'],
			$options['modulus'], $options['separator'], $options['first'], $options['last'],
			$options['ellipsis'], $options['class'], $options['currentClass']
		);
		*/
		extract($options);
		unset($options['tag'], $options['before'], $options['after'], $options['model'],
			$options['modulus'], $options['separator'], $options['first'], $options['last'],
			$options['ellipsis'], $options['class'], $options['currentClass'], $options['currentLink']
		);

		$out = '';

		if ($modulus && $params['pageCount'] > $modulus) {
			$half = intval($modulus / 2);
			$end = $params['page'] + $half;

			if ($end > $params['pageCount']) {
				$end = $params['pageCount'];
			}
			$start = $params['page'] - ($modulus - ($end - $params['page']));
			if ($start <= 1) {
				$start = 1;
				$end = $params['page'] + ($modulus - $params['page']) + 1;
			}

			if ($first && $start > 1) {
				$offset = ($start <= (int)$first) ? $start - 1 : $first;
				if ($offset < $start - 1) {
					$out .= $this->first($offset, compact('tag', 'separator', 'ellipsis', 'class'));
				} else {
					$out .= $this->first($offset, compact('tag', 'separator', 'class') + array('after' => $separator));
				}
			}

			$out .= $before;

			for ($i = $start; $i < $params['page']; $i++) {
				$out .= $this->Html->tag($tag, $this->link($i, array('page' => $i), $options), compact('class')) . $separator;
			}

			if ($class) {
				$currentClass .= ' ' . $class;
			}
			$out .= $this->Html->tag($tag, $params['page'], array('class' => $currentClass));
			if ($i != $params['pageCount']) {
				$out .= $separator;
			}

			$start = $params['page'] + 1;
			for ($i = $start; $i < $end; $i++) {
				$out .= $this->Html->tag($tag, $this->link($i, array('page' => $i), $options), compact('class')) . $separator;
			}

			if ($end != $params['page']) {
				$out .= $this->Html->tag($tag, $this->link($i, array('page' => $end), $options), compact('class'));
			}

			$out .= $after;

			if ($last && $end < $params['pageCount']) {
				$offset = ($params['pageCount'] < $end + (int)$last) ? $params['pageCount'] - $end : $last;
				if ($offset <= $last && $params['pageCount'] - $end > $offset) {
					$out .= $this->last($offset, compact('tag', 'separator', 'ellipsis', 'class'));
				} else {
					$out .= $this->last($offset, compact('tag', 'separator', 'class') + array('before' => $separator));
				}
			}

		} else {
			$out .= $before;

			for ($i = 1; $i <= $params['pageCount']; $i++) {
				if ($i == $params['page']) {
					if ($class) {
						$currentClass .= ' ' . $class;
					}
					
					/** @@CakePower @@ **/
					#$out .= $this->Html->tag($tag, $i, array('class' => $currentClass));
					if ( $currentClass ) {
						$out .= $this->Html->tag($tag, $this->link($i, array('page' => $i), array('class'=>'selected')), array( 'class'=>$currentClass ) );
					} else {
						$out .= $this->Html->tag($tag, $i, array('class' => $currentClass));
					}
					
					
				} else {
					$out .= $this->Html->tag($tag, $this->link($i, array('page' => $i), $options), compact('class'));
				}
				if ($i != $params['pageCount']) {
					$out .= $separator;
				}
			}

			$out .= $after;
		}

		return $out;
	}
	
	
	
	
	
}