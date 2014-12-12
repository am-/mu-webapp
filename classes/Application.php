<?php
/**
 * mu-webapp
 * 
 * LICENSE
 * 
 * The BSD 2-Clause License is applied on this source-file. For
 * further information please refer to
 * http://www.opensource.org/licenses/BSD-2-Clause or send an
 * email to andre.moelle@gmail.com.
 */
 
/**
 * This is the base-class for applications.
 * 
 * It manages paths and provides methods for creating dispatchers
 * and transfer-objects. It also runs the application, which means
 * that it uses the dispatcher and a transfer-object for executing
 * an entrance. The most important part is the initializing and
 * the shutdown which are described in the corresponding methods.
 * 
 * @package    mu-webapp
 * @copyright  2008-2015 Andre Moelle
 * @license    http://www.opensource.org/licenses/BSD-2-Clause
 * @author     Andre Moelle <andre.moelle@gmail.com>
 */
abstract class Application {
	/**
	 * Contains the base-path of the application.
	 * 
	 * Normally, the directories "code", "entrances" and "templates"
	 * are contained in the directory.
	 * 
	 * @var string
	 */
	protected $base;
	
	/**
	 * Stores the dispatcher.
	 * 
	 * @var Dispatcher
	 */
	protected $dispatcher;
	
	/**
	 * Stores the transfer-object.
	 * 
	 * @var Transfer
	 */
	protected $transfer;
	
	/**
	 * Makes the application ready.
	 * 
	 * @param string $base base-path for the application
	 * @param Transfer $transfer transfer-object holding variables from the outside-world
	 */
	public function __construct ($base, Transfer $transfer) {
		$this->base = $base;
		$this->transfer = $transfer;
		$this->setDispatcher(new Dispatcher($this->getEntrancesPath()));
	}
	
	public function getBasePath () { return $this->base; }
	public function getCodePath () { return $this->getBasePath() . 'code/'; }
	public function getEntrancesPath () { return $this->getBasePath() . 'entrances/'; }
	public function getTemplatesPath () { return $this->getBasePath() . 'templates/'; }
	public function getTransfer () { return $this->transfer; }
	
	public function getDispatcher () { return $this->dispatcher; }
	public function setDispatcher (Dispatcher $obj) { $this->dispatcher = $obj; return $this; }
	
	/**
	 * Runs the application with the given URI.
	 * 
	 * At first the 
	 * 
	 * @param string $uri URI which should be interpreted
	 * @return Transfer transfer-object after running the application
	 */
	public function run ($uri) {
		$transfer = clone $this->getTransfer();
		$this->initialize($transfer);
		$transfer = $this->getDispatcher()->dispatch($uri, $transfer);
		$this->shutdown($transfer);
		return $transfer;
	}
	
	/**
	 * Initializes the request.
	 * 
	 * Usually it runs some configuration code which is necessary for
	 * running the application, such as configuring the database and
	 * attach an object to the transfer-object. Another case could be
	 * the configuration of PHPs error-handling.
	 * 
	 * @param Transfer $transfer transfer-object used as prototype for the request
	 */
	abstract public function initialize (Transfer $transfer);
	
	/**
	 * Completes the request.
	 * 
	 * The aim of this method is evaluating the returned transfer-object.
	 * Depending on the transfer-object multiple action can be done,
	 * such as rendering a template, write session-data and so on.
	 * 
	 * @param Transfer $transfer transfer-object which was returned
	 */
	abstract public function shutdown (Transfer $transfer);
}