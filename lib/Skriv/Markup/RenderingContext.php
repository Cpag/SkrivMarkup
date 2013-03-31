<?php
namespace Skriv\Markup;

/**
 * SkrivMarkup rendering context object.
 *
 * @author	Cpag
 * @copyright	© 2012-2013, Amaury Bouchard
 * @package	SkrivMarkup
 * @see		WikiRenderer
 */

abstract class RenderingContext {

	/* ************ SKRIV MARKUP SPECIFIC ATTRIBUTES ************* */
	/** Hash containing the configuration parameters. */
	private $_params = null;

	/* ******************** CONSTRUCTION ****************** */
	/**
	 * Constructor.
	 */
	public function __construct(array $params, array $defParams) {
		$this->_params = array();
		foreach ($defParams as $name => $arr) {
			if (isset($params[$name])) {
				$val = $params[$name];
				switch ($arr[0]) {
					case 'bool':
						if (!is_bool($val))
							throw new \Exception('Bad boolean value for parameter "' . $name . '": ' . $val);
						break;
					case 'string':
						if (!is_string($val))
							throw new \Exception('Bad string value for parameter "' . $name . '": ' . $val);
						break;
					case 'int':
						if (!is_int($val))
							throw new \Exception('Bad integer value for parameter "' . $name . '": ' . $val);
						break;
					case 'closure':
						if (isset($val) && !is_a($val, '\Closure'))
							throw new \Exception('Bad closure value for parameter "' . $name . '"');
						break;
					default:
						throw new \Exception('Unknown parameter\'s type: "' . $arr[0] . '"');
				}
				$this->_params[$name] = $val;
			} else
				$this->_params[$name] = $arr[1];
		}
	}

	/* *************** PARAMETERS MANAGEMENT ************* */
	/**
	 * Returns a specific configuration parameter. If a parent configuration object exists, the parameter is asked to it.
	 * @param	string	$param	Parameter's name.
	 * @return	mixed	Value of the configuration parameter.
	 */
	public function getParam($param) {
		return (isset($this->_params[$param]) ? $this->_params[$param] : null);
	}

	/* ******************** TOC MANAGEMENT *************** */
	/**
	 * Add a TOC entry.
	 * @param	int	$depth		Depth in the tree.
	 * @param	string	$title		Name of the new entry.
	 * @param	string	$identifier	Identifier of the new entry.
	 */
	abstract public function addTocEntry($depth, $title, $identifier);
	/**
	 * Returns the TOC content. By default, the rendered HTML is returned, but the
	 * raw TOC tree is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw TOC tree. False by default.
	 * @return	string|array	The TOC rendered HTML or the TOC tree.
	 */
	abstract public function getToc($raw = false);

	/* ******************** FOOTNOTES MANAGEMENT **************** */
	/**
	 * Add a footnote.
	 * @param	string	$text	Footnote's text.
	 * @param	string	$label	(optionnel) Footnote's label. If not given, an auto-incremented
	 *				number will be used.
	 * @return	array	Hash with 'id' and 'index' keys.
	 */
	abstract public function addFootnote($text, $label = null);
	/**
	 * Returns the footnotes content. By default, the rendered HTML is returned, but the
	 * raw list of footnotes is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw list of footnotes.
	 *				False by default.
	 * @return	string|array	The footnotes' rendered HTML or the list of footnotes.
	 */
	abstract public function getFootnotes($raw = false);
}
