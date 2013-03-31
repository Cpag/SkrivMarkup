<?php

namespace Skriv\Markup\PlainText;

/**
 * SkrivMarkup configuration object.
 * This object is based on the WikiRenderer project created by Laurent Jouanneau.
 *
 * @author	Amaury Bouchard <amaury@amaury.net>
 * @copyright	© 2012-2013, Amaury Bouchard
 * @package	SkrivMarkup
 * @see		WikiRenderer
 */
class Config extends \WikiRenderer\Config  {
	/** ??? */
	public $defaultTextLineContainer = '\WikiRenderer\TextLine';
	/** List of inline markups. */
	public $textLineContainers = array(
		'\WikiRenderer\TextLine' => array(
			'\Skriv\Markup\PlainText\Strong',		// **strong**
			'\Skriv\Markup\PlainText\Em',		// ''em''
			'\Skriv\Markup\PlainText\Strikeout',		// --strikeout--
			'\Skriv\Markup\PlainText\Underline',		// __underline__
			'\Skriv\Markup\PlainText\Monospace',		// ##monospace##
			'\Skriv\Markup\PlainText\Superscript',	// ^^superscript^^
			'\Skriv\Markup\PlainText\Subscript',		// ,,subscript,,
			'\Skriv\Markup\PlainText\Abbr',		// ??abbr|text??
			'\Skriv\Markup\PlainText\Link',		// [[link|url]]		[[url]]
			'\Skriv\Markup\PlainText\Image',		// {{image|url}}	{{url}}
			'\Skriv\Markup\PlainText\Footnote',		// ((footnote))		((label|footnote))
			'\Skriv\Markup\PlainText\Anchor'		// ~~anchor~~
		)
	);
	/** List of bloc markups. */
	public $blocktags = array(
//		'\Skriv\Markup\PlainText\Title',
		'\Skriv\Markup\PlainText\WikiList',
		'\Skriv\Markup\PlainText\Code',
		'\Skriv\Markup\PlainText\Pre',
		'\Skriv\Markup\PlainText\Hr',
//		'\Skriv\Markup\PlainText\Blockquote',
		'\Skriv\Markup\PlainText\Table',
		'\Skriv\Markup\PlainText\StyledBlock',
		'\Skriv\Markup\PlainText\Paragraph',
	);

	/* ************ SKRIV MARKUP SPECIFIC ATTRIBUTES ************* */

	/** @var PlainTextRenderingContext */
	public $renderContext;

	private $_isTopConfig;

	/* ******************** CONSTRUCTION ****************** */
	/**
	 * Constructor.
	 */
	public function __construct(PlainTextRenderingContext $renderContext, $isTopConfig = true) {
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
		return $this->renderContext->onParse($finalText);
	}
}

