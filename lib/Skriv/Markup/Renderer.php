<?php

namespace Skriv\Markup;

/**
 * Main renderer object for processing of SkrivMarkup-enabled text.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivMarkup
 * @see		WikiRenderer
 */
class Renderer {
	/** The configuration object. */
	protected $_config = null;
	/** The WikiRenderer object. */
	protected $_wikiRenderer = null;

	/**
	 * Factory method. Creates a renderer object of the given type.
	 * @param	string	$type	(optional) Type of rendering object. "html" by default.
	 * @param	array	$params	(optional) Hash of parameters. The accepted parameters depends of the chosen rendering type.
	 * @return	\Skriv\Markup\Renderer	A rendering object.
	 * @throws	\Exception	If something goes wrong.
	 */
	static public function factory($type='html', array $params=null) {
		if (!isset($type) || !strcasecmp($type, 'html'))
			return (new Renderer($params));
		throw new \Exception("Unknown Skriv rendering type '$type'.");
	}

	/**
	 * Constructor.
	 * @param	$param array	(optional) Contains the specific configuration parameters:
	 * <ul>
	 * 	<li><strong>charset</strong> <em>string</em>	The charset. (default: 'UTF-8')</li>
	 * 	<li><strong>shortenLongUrl</strong> <em>bool</em>	Specifies if we must shorten URLs longer than 40 characters. (default: true)</li>
	 * 	<li><strong>convertSmileys</strong> <em>bool</em>		Specifies if we must convert smileys. (default: true)
	 * 	<li><strong>convertSymbols</strong> <em>bool</em>		Specifies if we must convert symbols. (default: true)
	 * 	<li><strong>urlProcessFunction</strong> <em>Closure</em>	URLs processing function. (default: null)
	 * 	<li><strong>preParseFunction</strong> <em>Closure</em>	Function for pre-parse process. (default: null)
	 * 	<li><strong>postParseFunction</strong> <em>Closure</em>	Function for post-parse process. (default: null)
	 * 	<li><strong>titleToIdFunction</strong> <em>Closure</em>	Function that converts title strings into HTML identifiers. (default: null)
	 * 	<li><strong>markupIdsPrefix</strong> <em>string</em>		Prefix of footnotes' identifiers. (default: "skriv-" + random value)
	 * 	<li><strong>anchorsPrefix</strong> <em>string</em>		Prefix of anchors' identifiers. (default: "")
	 * 	<li><strong>footnotesPrefix</strong> <em>string</em>		Prefix of footnotes' identifiers. (default: "note-")
	 * 	<li><strong>codeSyntaxHighlight</strong> <em>bool</em>	Activate code highlighting. (default: true)
	 * 	<li><strong>codeLineNumbers</strong> <em>bool</em>		Line numbers in code blocks. (default: true)
	 * 	<li><strong>firstTitleLevel</strong> <em>int</em>		Offset of first level titles. (default: 1)
	 * 	<li><strong>targetBlank</strong> <em>bool</em>		Add "target='_blank'" to every links.
	 * 	<li><strong>nofollow</strong> <em>bool</em>		Add "rel='nofollow'" to every links.
	 * 	<li><strong>addFootnotes</strong> <em>bool</em>		Add footnotes' content at the end of the page.
	 * 	<li><strong>codeInlineStyles</strong> <em>bool</em>	Activate inline styles in code blocks. (default: false)
	 * </ul>
	 */
	public function __construct(array $params=null) {
		$this->_config = new Html\Config(new Html\RenderContext($params));
		$this->_wikiRenderer = new \WikiRenderer\Renderer($this->_config);
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
