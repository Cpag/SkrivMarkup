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
			'\Skriv\Markup\PlainText\Anchor',		// ~~anchor~~
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

	/** @var RenderContext */
	public $renderContext;

	private $isTopConfig, $forceInline;

	/* ******************** CONSTRUCTION ****************** */
	/**
	 * Constructor.
	 */
	public function __construct(RenderContext $renderContext, $isTopConfig = true) {
		$this->renderContext = $renderContext;
		$this->isTopConfig = $isTopConfig;
		$this->forceInline = $renderContext->getParam('forceInline');
		if ($this->forceInline) {
			$this->blocktags = array();
			$k = array_search('\Skriv\Markup\PlainText\Footnote', $this->textLineContainers['\WikiRenderer\HtmlTextLine']);
			if ($k !== false)
				array_splice($this->textLineContainers['\WikiRenderer\HtmlTextLine'], $k, 1);
		}
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
		if ($this->isTopConfig)
			$this->renderContext->reset();
		return $this->renderContext->onStart($text);
	}
	/**
	 * Method called for post-parse processing.
	 * @param	string	$finalText	The generated text.
	 * @return	string	The text after post-processing.
	 */
	public function onParse($finalText) {
		$finalText = $this->renderContext->onParse($finalText);
		if ($this->forceInline) {
			if ($this->renderContext->getParam('ignoreMultiCR'))
				$finalText = preg_replace('/\n\n+/', "\n", $finalText);
			$finalText = str_replace("\n", "<br/>\n", $finalText);
		}
		return $finalText;
	}

	/* ******************** TOC MANAGEMENT *************** */
	/**
	 * Add a TOC entry.
	 * @param	int	$depth		Depth in the tree.
	 * @param	string	$title		Name of the new entry.
	 * @param	string	$identifier	Identifier of the new entry.
	 */
	public function addTocEntry($depth, $title, $identifier) {
		$this->renderContext->addTocEntry($depth, $title, $identifier);
	}
	/**
	 * Returns the TOC content. By default, the rendered HTML is returned, but the
	 * raw TOC tree is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw TOC tree. False by default.
	 * @return	string|array	The TOC rendered HTML or the TOC tree.
	 */
	public function getToc($raw = false) {
		return $this->renderContext->getToc($raw);
	}

	/* ******************** FOOTNOTES MANAGEMENT **************** */
	/**
	 * Add a footnote.
	 * @param	string	$text	Footnote's text.
	 * @param	string	$label	(optionnel) Footnote's label. If not given, an auto-incremented
	 *				number will be used.
	 * @return	array	Hash with 'id' and 'index' keys.
	 */
	public function addFootnote($text, $label = null) {
		return $this->renderContext->addFootnote($text, $label);
	}
	/**
	 * Returns the footnotes content. By default, the rendered HTML is returned, but the
	 * raw list of footnotes is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw list of footnotes.
	 *				False by default.
	 * @return	string|array	The footnotes' rendered HTML or the list of footnotes.
	 */
	public function getFootnotes($raw = false) {
		return $this->renderContext->getFootnotes($raw);
	}
}

