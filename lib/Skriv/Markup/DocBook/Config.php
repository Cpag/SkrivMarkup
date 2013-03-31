<?php

namespace Skriv\Markup\DocBook;

/**
 * SkrivMarkup configuration object.
 * This object is based on the WikiRenderer project created by Laurent Jouanneau.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2013, Amaury Bouchard
 * @package	SkrivMarkup
 * @see		WikiRenderer
 */
class Config extends \WikiRenderer\Config  {
	/** ??? */
	public $defaultTextLineContainer = '\WikiRenderer\HtmlTextLine';
	/** List of inline markups. */
	public $textLineContainers = array(
		'\WikiRenderer\HtmlTextLine' => array(
			'\Skriv\Markup\DocBook\Strong',		// **strong**
			'\Skriv\Markup\DocBook\Em',		// ''em''
			'\Skriv\Markup\DocBook\Strikeout',	// --strikeout--
			'\Skriv\Markup\DocBook\Underline',	// __underline__
			'\Skriv\Markup\DocBook\Monospace',	// ##monospace##
			'\Skriv\Markup\DocBook\Superscript',	// ^^superscript^^
			'\Skriv\Markup\DocBook\Subscript',	// ,,subscript,,
			'\Skriv\Markup\DocBook\Abbr',		// ??abbr|text??
			'\Skriv\Markup\DocBook\Link',		// [[link|url]]		[[url]]
			'\Skriv\Markup\DocBook\Image',		// {{image|url}}	{{url}}
			'\Skriv\Markup\DocBook\Footnote',	// ((footnote))		((label|footnote))
			'\Skriv\Markup\DocBook\Anchor',		// ~~anchor~~
			'\Skriv\Markup\DocBook\UnofficialInlineExt'		// <<:name|params>>
		)
	);
	/** List of bloc markups. */
	public $blocktags = array(
		'\Skriv\Markup\DocBook\Title',
		'\Skriv\Markup\DocBook\WikiList',
		'\Skriv\Markup\DocBook\Code',
		'\Skriv\Markup\DocBook\Pre',
		'\Skriv\Markup\DocBook\Hr',
		'\Skriv\Markup\DocBook\Blockquote',
		'\Skriv\Markup\DocBook\Table',
		'\Skriv\Markup\DocBook\StyledBlock',
		'\Skriv\Markup\DocBook\Paragraph',
	);

	/* ************ SKRIV MARKUP SPECIFIC ATTRIBUTES ************* */
	/** @var DocBookRenderingContext */
	public $renderContext;

	private $_sectionLevel;
	private $_isTopConfig;

	/* ******************** CONSTRUCTION ****************** */
	/**
	 * Constructor.
	 */
	public function __construct(DocBookRenderingContext $renderContext, $isTopConfig = true) {
		$this->renderContext = $renderContext;
		$this->_isTopConfig = $isTopConfig;
	}
	/**
	 * Build an object of the same type, "child" of the current object.
	 * @return	\Skriv\Markup\\Html\Config	The new configuration object.
	 */
	public function subConstruct() {
		return new Config($this->renderContext, false);
	}

	/* *************** PARAMETERS MANAGEMENT ************* */
	/**
	 * Returns a specific configuration parameter. If a parent configuration object exists, the parameter is asked to it.
	 * @param	string	$param	Parameter's name.
	 * @return	mixed	Value of the configuration parameter.
	 */
	public function getParam($param) {
		return $this->renderContext->getParam($param);
	}

	/* *************** PARSING MANAGEMENT **************** */
	/**
	 * Method called for pre-parse processing.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function onStart($text) {
		$this->_sectionLevel = array();
		if ($this->_isTopConfig)
			$this->renderContext->reset();
		return $this->renderContext->onStart($text);
	}
	/**
	 * Method called for post-parse processing.
	 * @param	string	$finalText	The generated text.
	 * @return	string	The text after post-processing.
	 */
	public function onParse($finalText) {
		$finalText .= str_repeat('</section>', count($this->_sectionLevel));
		return $this->renderContext->onParse($finalText);
	}
	/**
	 * Links processing.
	 * @param	string	$url		The URL to process.
	 * @param	string	$tagName	Name of the calling tag.
	 * @return	array	Array with the processed URL and the generated label.
	 *			Third parameter is about blank targeting of the link. It could be
	 *			null (use the default behaviour), true (add a blank targeting) or
	 *			false (no blank targeting).
	 */
	public function processLink($url, $tagName='') {
		return $this->renderContext->processLink($url);
	}
}
