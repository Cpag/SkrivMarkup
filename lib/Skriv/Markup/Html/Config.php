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
	/** List of bloc markups. */
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
	);

	/* ************ SKRIV MARKUP SPECIFIC ATTRIBUTES ************* */

	/** @var RenderContext */
	public $RenderContext;

	/** Parent configuration object, used for recursive calls. */
	private $_parentConfig;

	/* ******************** CONSTRUCTION ****************** */
	/**
	 * Constructor.
	 * @param	$param array See RenderContext::__construct
	 * @param	\Skriv\Markup\Html\Config	parentConfig	Parent configuration object, for recursive calls.
	 */
	public function __construct(array $param = null, Config $parentConfig = null) {
		$this->_parentConfig = $parentConfig;
		$this->RenderContext = isset($parentConfig) ? $parentConfig->RenderContext : new RenderContext($param);
	}
	/**
	 * Build an object of the same type, "child" of the current object.
	 * @return	\Skriv\Markup\\Html\Config	The new configuration object.
	 */
	public function subConstruct() {
		return new Config(null, $this);
	}

	/* *************** PARAMETERS MANAGEMENT ************* */
	/**
	 * Returns a specific configuration parameter. If a parent configuration object exists, the parameter is asked to it.
	 * @param	string	$param	Parameter's name.
	 * @return	mixed	Value of the configuration parameter.
	 */
	public function getParam($param) {
		return $this->RenderContext->getParam($param);
	}

	/* *************** TEXT MANAGEMENT ************* */
	/**
	 * Escape special characters
	 */
	public function escHtml($s) {
		return $this->RenderContext->escHtml($s);
	}
	/**
	 * Escape special characters
	 */
	public function escAttr($s) {
		return $this->RenderContext->escAttr($s);
	}
	/**
	 * Convert title string to a usable HTML identifier.
	 * @param	int	$depth	Depth of the title.
	 * @param	string	$text	Input string.
	 * @return	string	The converted string.
	 */
	public function titleToIdentifier($depth, $text) {
		return $this->RenderContext->titleToIdentifier($depth, $text);
	}

	public function textToIdentifier($text) {
		return $this->RenderContext->textToIdentifier($text);
	}

	/* *************** PARSING MANAGEMENT **************** */
	/**
	 * Method called for pre-parse processing.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function onStart($text) {
		$text = $this->RenderContext->onStart($text);
		// process of smileys and other special characters
		if ($this->getParam('convertSmileys'))
			$text = Smiley::convertSmileys($text);
		if ($this->getParam('convertSymbols'))
			$text = Smiley::convertSymbols($text);
		return $text;
	}
	/**
	 * Method called for post-parse processing.
	 * @param	string	$finalText	The generated text.
	 * @return	string	The text after post-processing.
	 */
	public function onParse($finalText) {
		return $this->RenderContext->onParse($finalText);
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
		return $this->RenderContext->processLink($url);
	}

	/* ******************** TOC MANAGEMENT *************** */
	/**
	 * Add a TOC entry.
	 * @param	int	$depth		Depth in the tree.
	 * @param	string	$title		Name of the new entry.
	 * @param	string	$identifier	Identifier of the new entry.
	 */
	public function addTocEntry($depth, $title, $identifier) {
		$this->RenderContext->addTocEntry($depth, $title, $identifier);
	}
	/**
	 * Returns the TOC content. By default, the rendered HTML is returned, but the
	 * raw TOC tree is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw TOC tree. False by default.
	 * @return	string|array	The TOC rendered HTML or the TOC tree.
	 */
	public function getToc($raw = false) {
		return $this->RenderContext->getToc($raw);
	}

	/* ******************** ID MANAGEMENT **************** */
	/**
	 * Create an ID for HTML markup. The id is unique.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function createMarkupId($baseId) {
		return $this->RenderContext->createMarkupId($baseId);
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
		return $this->RenderContext->addFootnote($text, $label);
	}
	/**
	 * Returns the footnotes content. By default, the rendered HTML is returned, but the
	 * raw list of footnotes is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw list of footnotes.
	 *				False by default.
	 * @return	string|array	The footnotes' rendered HTML or the list of footnotes.
	 */
	public function getFootnotes($raw = false) {
		return $this->RenderContext->getFootnotes($raw);
	}
}

