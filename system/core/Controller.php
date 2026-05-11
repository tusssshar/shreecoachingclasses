<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {

	/**
	 * Reference to the CI singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * Benchmark object
	 *
	 * @var	object
	 */
	public $benchmark;

	/**
	 * Hooks object
	 *
	 * @var	object
	 */
	public $hooks;

	/**
	 * Config object
	 *
	 * @var	object
	 */
	public $config;

	/**
	 * Log object
	 *
	 * @var	object
	 */
	public $log;

	/**
	 * UTF8 object
	 *
	 * @var	object
	 */
	public $utf8;

	/**
	 * URI object
	 *
	 * @var	object
	 */
	public $uri;

	/**
	 * Exceptions object
	 *
	 * @var	object
	 */
	public $exceptions;

	/**
	 * Router object
	 *
	 * @var	object
	 */
	public $router;

	/**
	 * Output object
	 *
	 * @var	object
	 */
	public $output;

	/**
	 * Security object
	 *
	 * @var	object
	 */
	public $security;

	/**
	 * Input object
	 *
	 * @var	object
	 */
	public $input;

	/**
	 * Lang object
	 *
	 * @var	object
	 */
	public $lang;

	/**
	 * Load object
	 *
	 * @var	object
	 */
	public $load;

	/**
	 * Database object
	 *
	 * @var	object
	 */
	public $db;

	/**
	 * Session object
	 *
	 * @var	object
	 */
	public $session;

	/**
	 * Pagination object
	 *
	 * @var	object
	 */
	public $pagination;

	/**
	 * XMLRPC object
	 *
	 * @var	object
	 */
	public $xmlrpc;

	/**
	 * Form Validation object
	 *
	 * @var	object
	 */
	public $form_validation;

	/**
	 * Email object
	 *
	 * @var	object
	 */
	public $email;

	/**
	 * Upload object
	 *
	 * @var	object
	 */
	public $upload;

	/**
	 * Paypal object
	 *
	 * @var	object
	 */
	public $paypal;

	/**
	 * Email Model object
	 *
	 * @var	object
	 */
	public $email_model;

	/**
	 * CRUD Model object
	 *
	 * @var	object
	 */
	public $crud_model;

	/**
	 * SMS Model object
	 *
	 * @var	object
	 */
	public $sms_model;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		self::$instance =& $this;

		// Assign all the class objects that were instantiated by the
		// bootstrap file (CodeIgniter.php) to local class variables
		// so that CI can run as one big super object.
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();
		log_message('info', 'Controller Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Get the CI singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}

}
