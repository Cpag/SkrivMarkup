<?php

namespace Skriv\Markup\Html;

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
	public $defaultTextLineContainer = '\WikiRenderer\HtmlTextLine';
	/** List of inline markups. */
	public $textLineContainers = array(
		'\WikiRenderer\HtmlTextLine' => array(
			'\Skriv\Markup\Html\Strong',		// **strong**
			'\Skriv\Markup\Html\Em',		// ''em''
			'\Skriv\Markup\Html\Strikeout',		// --strikeout--
			'\Skriv\Markup\Html\Underline',		// __underline__
			'\Skriv\Markup\Html\Monospace',		// ##monospace##
			'\Skriv\Markup\Html\Superscript',	// ^^superscript^^
			'\Skriv\Markup\Html\Subscript',		// ,,subscript,,
			'\Skriv\Markup\Html\Abbr',		// ??abbr|text??
			'\Skriv\Markup\Html\Link',		// [[link|url]]		[[url]]
			'\Skriv\Markup\Html\Image',		// {{image|url}}	{{url}}
			'\Skriv\Markup\Html\Footnote',		// ((footnote))		((label|footnote))
			'\Skriv\Markup\Html\Anchor',		// ~~anchor~~
		)
	);
	/** List of block markups. */
	public $blocktags = array(
		'\Skriv\Markup\Html\Title',
		'\Skriv\Markup\Html\WikiList',
		'\Skriv\Markup\Html\Code',
		'\Skriv\Markup\Html\Pre',
		'\Skriv\Markup\Html\Hr',
		'\Skriv\Markup\Html\Blockquote',
		'\Skriv\Markup\Html\Table',
		'\Skriv\Markup\Html\StyledBlock',
		'\Skriv\Markup\Html\Paragraph',
		'\Skriv\Markup\Html\MultiCR'
	);
	// list of inline extensions
	private $_inlineExtensions = array(
		'ext-date'	=> '\Skriv\Markup\Html\ExtDate',
	);
	// list of block extensions
	private $_blockExtensions = array(
		'ext-lipsum'	=> '\Skriv\Markup\Html\ExtLipsum',
	);

	/* ************ SKRIV MARKUP SPECIFIC ATTRIBUTES ************* */
	/** @var HtmlRenderingContext */
	public $renderContext;

	private $_isTopConfig;

	/* ******************** CONSTRUCTION ****************** */
	/**
	 * Constructor.
	 */
	public function __construct(HtmlRenderingContext $renderContext, $isTopConfig = true) {
		$this->renderContext = $renderContext;
		$this->_isTopConfig = $isTopConfig;

		// add extensions to the lists of supported markups
		$inlineExt = array();
		$blockExt = array();
		foreach ($this->_inlineExtensions as $extName => $obj) {
			if ($renderContext->getParam($extName))
				$inlineExt[] = $obj;
		}
		foreach ($this->_blockExtensions as $extName => $obj) {
			if ($renderContext->getParam($extName))
				$blockExt[] = $obj;
		}
		if (!empty($inlineExt))
			$this->textLineContainers['\WikiRenderer\HtmlTextLine'] = array_merge($this->textLineContainers['\WikiRenderer\HtmlTextLine'], $inlineExt);
		if (!empty($blockExt))
			$this->blocktags = array_merge($blockExt, $this->blocktags);
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
