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
 * Simple class for handling template-files written in native PHP.
 * 
 * By casting a template to a string you will get a string which
 * is equivalent to the rendered template. The method "render"
 * displays the rendered template. Basic operations over the
 * variables of a template are realized by using the magic methods
 * __get, __set, __isset and __unset. While rendering the template the
 * aforementioned variables are copied as local variables (via extract)
 * to avoid problems with magic methods.
 * 
 * @package    mu-webapp
 * @copyright  2008-2012 Andre Moelle
 * @license    http://www.opensource.org/licenses/BSD-2-Clause
 * @author     Andre Moelle <andre.moelle@gmail.com>
 */
class Template {
	/**
	 * Contains the path to the template.
	 * 
	 * @var string
	 */
	protected $template;
	
	/**
	 * Contains variables which are visible in the template.
	 * 
	 * Access is provided via __get, __set, __isset and __unset.
	 * 
	 * <code>
	 * $tpl instanceof Template; // true
	 * isset($tpl->foobar); // false
	 * $tpl->foobar = 'bar';
	 * isset($tpl->foobar); // true
	 * $tpl->foobar; // 'bar'
	 * unset($tpl->foobar);
	 * isset($tpl->foobar); // false
	 * </code>
	 * 
	 * @var array
	 */
	protected $variables = array();
	
	/**
	 * Sets the template-file and possibly the variable.
	 * 
	 * @param string $template path to the template which will be rendered
	 * @param array $variables variables visible to the template
	 */
	public function __construct ($template, array $variables = array())	{
		$this->template = $template;
		$this->variables = $variables;
	}
	
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
	public function getTemplate () { return $this->template; }
	
	/**
	 * Converts the template to a string.
	 * 
	 * This is achieved using output-buffering while rendering the
	 * template.
	 * 
	 * @return string
	 */
	public function __toString () {
		ob_start();
		$this->render();
		return ob_get_clean();
	}
	
	/**
	 * Creates a new template with the variables of the old one.
	 * 
	 * <code>
	 * $tpl instanceof Template; // true
	 * $tpl2 = $tpl->derive($tpl->getTemplate());
	 * // $tpl and $tpl2 are equal but not the same object
	 * </code>
	 * 
	 * @param string $template new template-file
	 * @return Template
	 */
	public function derive ($template) {
		return new Template($template, $this->variables);
	}
	
	/**
	 * Renders and outputs the rendered template.
	 * 
	 * All variables which were set in this template-object are
	 * exposed as normal variables. For this purpose the function
	 * extract is used.
	 * To reduce problems with __get you should use rather local
	 * variables than accessing these variables via __get.
	 * 
	 * @see http://de2.php.net/extract
	 */
	public function render () {
		extract($this->variables);
		include $this->getTemplate();
	}
}
?>
