<?php
/**
 * CakePOWER :: PowerAuthComponent
 *
 * It extends basic AuthComponent allowing to configure an $accessDeniedRedirect property
 * to strict redirect denied actions to a certain url in place of to redirect to referral url.
 *
 * How to use this component in a controller class (AppController??):
 *
 * public $components = array(
 *     'Auth' => array( 
 *         'className' => 'PowerAuth', 
 *          'accessDeinedRedirect' => array( 'controller'=>'users', 'action'=>'login' ),
 *          [other Auth properties] )
 * );
 */


App::import('Controller/Component', 'AuthComponent');



class PowerAuthComponent extends AuthComponent {
	
	
	/**
	 * Static Access Denied Page
	 * You can set an CakePHP url string or array to redirect when a deny exception happen.
	 *
	 * Leaving this property to "false" does not change the default AuthComponent behavior
	 * of redirecting to the referral url of the denied request.
	 */
	public $accessDeniedRedirect = false;
	
	
	public function startup(Controller $controller) {
		if ($controller->name == 'CakeError') {
			return true;
		}

		$methods = array_flip(array_map('strtolower', $controller->methods));
		$action = strtolower($controller->request->params['action']);

		$isMissingAction = (
			$controller->scaffold === false &&
			!isset($methods[$action])
		);

		if ($isMissingAction) {
			return true;
		}

		if (!$this->_setDefaults()) {
			return false;
		}
		$request = $controller->request;

		$url = '';

		if (isset($request->url)) {
			$url = $request->url;
		}
		$url = Router::normalize($url);
		$loginAction = Router::normalize($this->loginAction);

		$allowedActions = $this->allowedActions;
		$isAllowed = (
			$this->allowedActions == array('*') ||
			in_array($action, array_map('strtolower', $allowedActions))
		);

		if ($loginAction != $url && $isAllowed) {
			return true;
		}

		if ($loginAction == $url) {
			if (empty($request->data)) {
				if (!$this->Session->check('Auth.redirect') && !$this->loginRedirect && env('HTTP_REFERER')) {
					$this->Session->write('Auth.redirect', $controller->referer(null, true));
				}
			}
			return true;
		} else {
			if (!$this->_getUser()) {
				if (!$request->is('ajax')) {
					$this->flash($this->authError);
					$this->Session->write('Auth.redirect', $request->here());
					$controller->redirect($loginAction);
					return false;
				} elseif (!empty($this->ajaxLogin)) {
					$controller->viewPath = 'Elements';
					echo $controller->render($this->ajaxLogin, $this->RequestHandler->ajaxLayout);
					$this->_stop();
					return false;
				} else {
					$controller->redirect(null, 403);
				}
			}
		}
		if (empty($this->authorize) || $this->isAuthorized($this->user())) {
			return true;
		}
		

		$this->flash($this->authError);
		
		
		/** @@CakePOWER@@ **/
		#$controller->redirect($controller->referer('/'), null, true);
		$accessDeniedRedirect = $this->accessDeniedRedirect;
		if ( !$accessDeniedRedirect ) $accessDeniedRedirect = $controller->referer('/');
		$controller->redirect($accessDeniedRedirect, null, true);
		
		
		return false;
	}

}