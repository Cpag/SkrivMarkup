<?php

namespace Skriv\Markup;
use WikiRenderer\Config;

/**
 * Main renderer object for processing of SkrivMarkup-enabled text.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivMarkup
 * @see		WikiRenderer
 */
class Renderer {
	/** @var Config */
	private $_config = null;
	/** The WikiRenderer object. */
	private $_wikiRenderer = null;

	/**
	 * Factory method. Creates a renderer object of the given type.
	 * @param	string	$type	(optional) Type of rendering object. Available values: 'html' (by default), 'plain-text'.
	 * @param	array	$params	(optional) Hash of parameters. The accepted parameters depends of the chosen rendering type.
	 * @return	\Skriv\Markup\Renderer	A rendering object.
	 * @throws	\Exception	If something goes wrong.
	 */
	static public function factory($type = 'html', array $params = null) {
		if (!isset($type))
			$type = 'html';
		switch ($type) {
			case 'html':
				return (new Renderer(new Html\Config(new Html\RenderContext($params))));
			case 'plain-text':
				return (new Renderer(new PlainText\Config(new PlainText\RenderContext($params))));
			default:
				throw new \Exception("Unknown Skriv rendering type '$type'.");
		}
	}

	/**
	 * Constructor.
	 * @param	$params array See RenderContext::__construct()
	 */
	public function __construct(Config $config) {
		$this->_config = $config;
		$this->_wikiRenderer = new \WikiRenderer\Renderer($config);
	}
	/**
	 * Parses a Skriv text and generates a converted text.
	 * @param	string	$text	The text to parse.
	 * @return	string	The generated string.
	 */
	public function render($text) {
//		$this->_config->resetBeforeRender();
		return ($this->_wikiRenderer->render($text));
	}
	/**
	 * Returns the TOC content. By default, the rendered string is returned, but the
	 * raw TOC tree is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw TOC tree. False by default.
	 * @return	string|array	The TOC rendered string or the TOC tree.
	 */
	public function getToc($raw=false) {
		return ($this->_config->getToc($raw));
	}
	/**
	 * Returns the footnotes content. By default, the rendered string is returned, but the
	 * raw list of footnotes is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw list of footnotes.
	 *				False by default.
	 * @return	string|array	The footnotes' rendered string or the list of footnotes.
	 */
	public function getFootnotes($raw=false) {
		return ($this->_config->getFootnotes($raw));
	}
	/**
	 * Returns the lines which contain an error.
	 * @return	array	List of lines.
	 */
	public function getErrors() {
		return ($this->_wikiRenderer->errors);
	}
}
