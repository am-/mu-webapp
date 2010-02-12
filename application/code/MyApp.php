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
 * Default application with very few features.
 * 
 * A layout is passed to the transfer-object which usually
 * renders the "templates/layout.phtml"-template. For rendering a
 * layout another template is necessary which contains the body
 * of the website. This template must be attached to the transfer-object
 * with the name "template".
 * 
 * @package    Biphrost
 * @copyright  2008-2009 Nordic Development
 * @license    http://license.nordic-dev.de/newbsd.txt (New-BSD license)
 * @author     Andre Moelle <andre.moelle@gmail.com>
 * @version    $Id: MyApp.php 26 2009-06-04 21:42:42Z andre.moelle $
 */
class MyApp extends Application
{
	/**
	 * Registers an autoloader.
	 * 
	 * @see Application::__construct()
	 */
	public function __construct ($base, Transfer $transfer) {
		parent::__construct($base, $transfer);
		set_include_path(get_include_path() . ':' . $this->getCodePath());
		spl_autoload_register(array($this, 'autoloader'));
	}
	
	/**
	 * Used for autoloading non-existent classes.
	 * 
	 * It uses the PEAR-schema for finding the path of a class.
	 * Thereafter it includes the file that fits to the class-name.
	 * 
	 * @param string $class class of the non-existent class
	 */
	protected function autoloader ($class) {
		include str_replace('_', '/', $class) . '.php';
	}
	
	/**
	 * @see Application::initialize()
	 */
	public function initialize (Transfer $transfer) {
		$template = new Template('');
		$template->baseUrl = dirname($transfer->server('PHP_SELF'));
		if($template->baseUrl != '/') $template->baseUrl = $template->baseUrl . '/';
		$transfer->baseTpl = $template;
		
		$transfer->layout = $template->derive($this->getTemplatesPath() . 'layout.phtml');
	}
	
	/**
	 * @see Application::shutdown()
	 */
	public function shutdown (Transfer $transfer) {
		if(isset($transfer->template)) {
			$tpl = $transfer->template;
			$tpl = $tpl->derive($this->getTemplatesPath() . $tpl->getTemplate());
			$transfer->layout->tpl = $tpl;
			$transfer->layout->render();
		}
	}
}
?>