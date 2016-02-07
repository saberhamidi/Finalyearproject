<?php
App::uses('Controller', 'Controller');

class AppController extends Controller {

	/**
	 * Components loaded for this controller.
	 *
	 * @var array
	 */
	public $components = array(
		'Flash', 'RequestHandler',

		'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'fields' => array(
						'username' => 'restaurant_id',
						'password' => 'password'
					),
					'passwordHasher' => 'Blowfish',
				),
			),
			'loginRedirect' => '/campaigns',
			'logoutRedirect' => '/',
		),
	);

	/**
	 * Helpers loaded for this controller.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Form',
		'Html',
		'Flash',
	);

	/**
	 * Models loaded for this controller.
	 *
	 * @var array
	 */
	public $uses = array();

	/**
	 * This function is executed before every action in the controller. It’s a
	 * handy place to check for an active session or inspect user permissions.
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->loginAction = array( 'controller' => '/');
		$this->Auth->allow('uesrs','login');
	}

	/**
	 * Called after controller action logic, but before the view is rendered.
	 *
	 * @return void
	 */
	public function beforeRender() {
		parent::beforeRender();
	}

	/**
	 * Called after every controller action, and after rendering is complete.
	 * This is the last controller method to run.
	 *
	 * @return void
	 */
	public function afterFilter() {
		parent::afterFilter();
	}
}
