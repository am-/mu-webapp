<?php
/**
 * Biphrost
 * 
 * LICENSE
 * 
 * The new BSD license is applied on this source-file. For further
 * information please visit http://license.nordic-dev.de/newbsd.txt
 * or send an email to andre.moelle@gmail.com.
 */

/**
 * The dispatcher dispatches requests.
 * 
 * At detail, it maps an URI to an entrance and adds non-consumed
 * components as arguments to the given prototype-transfer-object.
 * Thereafter it executes the entrance and returns a new transfer-object
 * with some modifications.
 * 
 * @package    Biphrost
 * @copyright  2008-2009 Nordic Development
 * @license    http://license.nordic-dev.de/newbsd.txt (New-BSD license)
 * @author     Andre Moelle <andre.moelle@gmail.com>
 * @version    $Id: Dispatcher.php 29 2009-06-05 14:40:57Z andre.moelle $
 */
class Dispatcher
{
	/**
	 * The base-path stores the directory where the entrances reside.
	 * 
	 * The last character of the string is a slash.
	 * 
	 * @var string
	 */
	protected $base;
	
	/**
	 * Passes the base-path and stores it.
	 * 
	 * @param string $base base-path
	 */
	public function __construct ($base) { $this->base = $base; }
	
	/**
	 * Creates a new scope where the entrance is executed.
	 * 
	 * @param string $entrance path to the entrance
	 * @param Transfer $transfer transfer-object
	 * @return Transfer
	 */
	protected function execute ($entrance, Transfer $transfer) {
		include $entrance;
		return $transfer;
	}
	
	/**
	 * Dispatches an URI.
	 * 
	 * At first the entrance and its arguments are identified.
	 * Thereafter the passed transfer-object acts as prototype and
	 * the arguments are attached to a new transfer-object. This
	 * transfer-object is used for executing the entrance and
	 * modifications are made on that object. Lastly the
	 * modified transfer-object is returned.
	 * 
	 * @param string $uri URI that should be dispatched
	 * @param Transfer $transfer prototype for a new transfer-object
	 * @return Transfer
	 */
	public function dispatch ($uri, Transfer $transfer) {
		$transfer = clone $transfer;
		if(($res = $this->findEntrance($uri)) === false) {
			return $transfer;
		}
		
		return $this->execute($res[0], $transfer->setArguments($res[1]))->dispatch();
	}
	
	/**
	 * Finds an entrance and its arguments.
	 * 
	 * Prepares the URI, i.e. the URI is decomposed in its components.
	 * Empty components are removed instantly.
	 * Normally the result is an array with two elements, where
	 * the first corresponds to the entrance-file and the second
	 * to the unnamed arguments.
	 * If no entrance could be found, false is returned instead.
	 * 
	 * Some examples for returns:
	 * <code>
	 * array('index.php', array('show', '42'))
	 * array('blog/show.php', array('42'))
	 * array('index.php', array())
	 * false
	 * </code>
	 * 
	 * By ignoring side-effects on the file-system (e.g.: removed files)
	 * the following attribute of this method holds for each variable $var
	 * if $x is this dispatcher.
	 * <code>
	 * $a = $x->findEntrance('/' . $var . '/');
	 * $b = $x->findEntrance('/' . $var);
	 * $c = $x->findEntrance($var);
	 * $a == $b == $c;
	 * </code>
	 * This holds even if strpos($var, '/') !== false.
	 * 
	 * @param string $uri
	 * @return array|false
	 */
	public function findEntrance ($uri) {
		$components = array_values(array_filter(explode('/', $uri)));
		
		if(in_array('..', $components)) {
			return false;
		}
		
		$n = count($components);
		$dir = $this->base;
		$result = false;
		
		for($i = 0; file_exists($dir) && is_dir($dir) && $i < $n; $i++) {
			$component = strtolower($components[$i]);
			$file = $dir . $component . '.php';
			$dir .= $component . '/';
			
			if(file_exists($file)) {
				$result = array($file, array_slice($components, $i+1));
			}
		}
		
		return $result;
	}
}
?>