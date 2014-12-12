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
 * Objects of this class act as transfer-objects.
 * 
 * Therefore it has following responsibilites and features:
 * - provide access to outside-variables (GET/POST/COOKIE/SESSION/SERVER)
 * - provide access to additional parameters (depending on the URI)
 * - manage variables between the subsystems
 * 
 * These variables can be modified at each subsystem which makes it
 * a powerful tool for many occasions.
 * 
 * There are only a few subsystems:
 * 1.) The outside-world, usually the web.
 * 2.) The core classes.
 * 3.) The application itself.
 * 
 * @package    mu-webapp
 * @copyright  2008-2015 Andre Moelle
 * @license    http://www.opensource.org/licenses/BSD-2-Clause
 * @author     Andre Moelle <andre.moelle@gmail.com>
 */
class Transfer {
	/**
	 * Contains the arguments.
	 * 
	 * These elements correspond to the components of the
	 * URI which do not belong to an entrance.
	 * 
	 * By default they are non-named, which means that the array
	 * has only numeric indices. In case of using the method
	 * "nameArguments" you can give them names.
	 * 
	 * Access is possible via *Arguments and argument.
	 * 
	 * @var array
	 */
	protected $arguments = array();
	
	/**
	 * Contains the cookies.
	 * 
	 * Access is possible via *Cookies and cookie.
	 * For deleting old cookies or creating new cookies the function
	 * setcookie should be used (see the php-manual for further information).
	 * 
	 * @var array
	 */
	protected $cookies = array();
	
	/**
	 * Holds the dispatch-flag.
	 * 
	 * On true, this transfer-object was used for dispatching
	 * a request. Otherwise it was not.
	 * 
	 * @var boolean
	 */
	protected $dispatch = false;
	
	/**
	 * Contains the GET-variables.
	 * 
	 * Access is possible via *Get and get.
	 * 
	 * @var array
	 */
	protected $get = array();
	
	/**
	 * Contains the POST-variables.
	 * 
	 * Access is possible via *Post and post.
	 * 
	 * @var array
	 */
	protected $post = array();
	
	/**
	 * Contains the server-variables.
	 * 
	 * Access is possible via *Server and server.
	 * 
	 * @var array
	 */
	protected $server = array();
	
	/**
	 * Contains the session-variables.
	 * 
	 * Access is possible via *Session and session.
	 * Creating new session variable can be achieved via "setSessionVar".
	 * Usually, they are updated at the end of the bootstrap-file.
	 * 
	 * @var array
	 */
	protected $session = array();
	
	/**
	 * Contains all variables which were assigned.
	 * 
	 * The use of this variable is for transfering arguments which
	 * do not necessarily correlate with values from the outside-world,
	 * which includes GET, POST, SESSION, COOKIE and the other arguments.
	 * 
	 * They are rather used for giving the entrances the required
	 * possibilities such as providing a database-interface or an ACL-system.
	 * Also redirects should be implemented by this facility.
	 * 
	 * Access is provided via __get, __set, __isset and __unset.
	 * 
	 * @var array
	 */
	protected $variables = array();
	
	/**
	 * Implementation of an ifsetor.
	 * 
	 * @see http://wiki.php.net/rfc/ifsetor
	 * @param mixed $var variable (perhaps non-existing)
	 * @param mixed $def default-value, if $var does not exist
	 * @return mixed either $var or $def
	 */
	protected function ifsetor (&$var, $def = null) { return (isset($var) ? $var : $def); }
	
	public function __get ($name) {	return $this->ifsetor($this->variables[$name]); }
	public function __set ($name, $value) { $this->variables[$name] = $value; }
	public function __isset ($name) { return isset($this->variables[$name]); }
	public function __unset ($name) { if(isset($this->variables[$name])) unset($this->variables[$name]); }
	
	public function argument ($name) { return $this->ifsetor($this->arguments[$name]); }
	public function cookie ($name) { return $this->ifsetor($this->cookies[$name]); }
	public function get ($name) { return $this->ifsetor($this->get[$name]); }
	public function post ($name) { return $this->ifsetor($this->post[$name]); }
	public function server ($name) { return $this->ifsetor($this->server[$name]); }
	public function session ($name) { return $this->ifsetor($this->session[$name]); }
	
	public function getArguments () { return $this->arguments; }
	public function getCookies () { return $this->cookies; }
	public function getGet () {	return $this->get; }
	public function getPost () { return $this->post; }
	public function getServer () { return $this->server; }
	public function getSession () { return $this->session; }
	public function getVariables () { return $this->variables; }
	
	public function setArguments (array $array) { $this->arguments = $array; return $this; }
	public function setCookies (array $array) { $this->cookies = $array; return $this; }
	public function setGet (array $array) { $this->get = $array; return $this; }
	public function setPost (array $array) { $this->post = $array; return $this; }
	public function setServer (array $array) { $this->server = $array; return $this; }
	public function setSession (array $array) { $this->session = $array; return $this; }
	public function setSessionVar ($name, $value) { $this->session[$name] = $value; return $this; }
	public function setVariables (array $array) { $this->variables = $array; return $this; }
	
	public function dispatch () { $this->dispatch = true; return $this; }
	public function wasDispatched () { return $this->dispatch; }
	
	/**
	 * Names the unnamed arguments.
	 * 
	 * The array has to have numeric keys since the keys assign
	 * the unnamed arguments a name. E.g.:
	 * 
	 * <code>
	 * $transfer instanceof Transfer; // true
	 * $transfer->getArguments() == array('12', '34'); // true
	 * $transfer->nameArguments(array('foo', 'bar')); // 2
	 * $transfer->getArguments() == array('12', '34', 'foo' => '12', 'bar' => '34'); // true
	 * </code>
	 * 
	 * Invalid keys (e.g.: the passed array is too long) are ignored.
	 * The number of assigned names is returned for verifying purposes.
	 * 
	 * @param array $names names for the unnamed values
	 * @return integer
	 * @throws Exception
	 */
	public function nameArguments (array $names) {
		$array = array();
		$result = 0;
		
		foreach($names as $i => $name) {
			if(isset($this->arguments[$i])) {
				$array[$name] = $this->arguments[$i];
				$result++;
			}
		}
		
		$this->setArguments(array_merge($this->getArguments(), $array));
		return $result;
	}
}