<?php

namespace Skriv\Markup\PlainText;
use Skriv\Markup\RenderContext;

/**
 * SkrivMarkup rendering context object.
 *
 * @author	Cpag
 * @copyright	© 2012-2013, Amaury Bouchard
 * @package	SkrivMarkup
 * @see		WikiRenderer
 */

class PlainTextRenderContext extends RenderContext {

	/* ************ SKRIV MARKUP SPECIFIC ATTRIBUTES ************* */
	/** List of the footnotes. */
	private $_footnotes = null;
	/** Tree representing the table of content. */
	private $_toc = null;

	/* ******************** CONSTRUCTION ****************** */
	/**
	 * Constructor.
	 * @param	$param array	(optional) Contains the specific configuration parameters:
	 * <ul>
	 * 	<li><strong>convertSymbols</strong> <em>bool</em>		Specifies if we must convert symbols. (default: true)
	 * 	<li><strong>preParseFunction</strong> <em>Closure</em>	Function for pre-parse process. (default: null)
	 * 	<li><strong>postParseFunction</strong> <em>Closure</em>	Function for post-parse process. (default: null)
	 * 	<li><strong>addFootnotes</strong> <em>bool</em>		Add footnotes' content at the end of the page.
	 * 	<li><strong>ignoreMultiCR</strong> <em>bool</em>	Ignore multiple carriage returns. (default: true)
	 * </ul>
	 */
	public function __construct(array $params = null) {
		parent::__construct($params, array(
			'convertSymbols' => ['bool', true],
			'preParseFunction' => ['closure', null],
			'postParseFunction' => ['closure', null],
			'addFootnotes'	 => ['bool', false],
			'ignoreMultiCR' => ['bool', true]
		));
	}

	/* *************** PARSING MANAGEMENT **************** */
	/**
	 * Method called for pre-parse processing once for all the document.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function reset() {
		$this->_footnotes = array();
		$this->_toc = null;
	}
	/**
	 * Method called for pre-parse processing.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function onStart($text) {
		// - Strip comments: %%
		$text = str_replace("\r", "\n", str_replace("\r\n", "\n", $text));
		$text = preg_replace('/\n?%%[^\n]*/', '', $text);
		// process of smileys and other special characters
		if ($this->getParam('convertSymbols'))
			$text = Smiley::convertSymbols($text);
		/** @var $func \Closure */
		$func = $this->getParam('preParseFunction');
		if (isset($func))
			$text = $func($text);
		return ($text);
	}
	/**
	 * Method called for post-parse processing.
	 * @param	string	$finalText	The generated text.
	 * @return	string	The text after post-processing.
	 */
	public function onParse($finalText) {
		// if a specific post-parse function was defined, it is called
		/** @var $func \Closure */
		$func = $this->getParam('postParseFunction');
		if (isset($func))
			$finalText = $func($finalText);
		// add footnotes' content if needed
		if ($this->getParam('addFootnotes')) {
			$footnotes = $this->getFootnotes();
			if (!empty($footnotes))
				$finalText .= "\n" . $footnotes;
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
		if (!isset($this->_toc))
			$this->_toc = array();
		$this->_addTocSubEntry($depth, $depth, $title, $identifier, $this->_toc);
	}
	/**
	 * Returns the TOC content. By default, the rendered HTML is returned, but the
	 * raw TOC tree is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw TOC tree. False by default.
	 * @return	string|array	The TOC rendered HTML or the TOC tree.
	 */
	public function getToc($raw=false) {
		if ($raw === true)
			return ($this->_toc['sub']);
		$html = $this->_getRenderedToc($this->_toc['sub']);
		return ($html);
	}

	/* ******************** FOOTNOTES MANAGEMENT **************** */
	/**
	 * Add a footnote.
	 * @param	string	$text	Footnote's text.
	 * @param	string	$label	(optionnel) Footnote's label. If not given, an auto-incremented
	 *				number will be used.
	 * @return	array	Hash with 'id' and 'index' keys.
	 */
	public function addFootnote($text, $label=null) {
		$index = count($this->_footnotes) + 1;
		$note = array(
			'label'	=> isset($label) ? $label : strval($index),
			'text'	=> $text,
			'id'	=> null,
			'index'	=> $index
		);
		$this->_footnotes[] = $note;
		return $note;
	}
	/**
	 * Returns the footnotes content. By default, the rendered HTML is returned, but the
	 * raw list of footnotes is available.
	 * @param	bool	$raw	(optional) Set to True to get the raw list of footnotes.
	 *				False by default.
	 * @return	string|array	The footnotes' rendered HTML or the list of footnotes.
	 */
	public function getFootnotes($raw=false) {
		if ($raw === true)
			return $this->_footnotes;
		if (empty($this->_footnotes))
			return null;
		$footnotes = '';
		foreach ($this->_footnotes as $note)
			$footnotes .= '(' . $note['label'] . ') ' . $note['text'] . "\n";
		return "\n$footnotes";
	}

	/* ****************** PRIVATE METHODS ******************** */
	/**
	 * Add a sub-TOC entry.
	 * @param	int	$depth		Depth in the tree.
	 * @param	int	$level		Level of the title.
	 * @param	string	$title		Name of the new entry.
	 * @param	string	$identifier	Identifier of the new entry.
	 * @param	array	$list		List of sibbling nodes.
	 */
	private function _addTocSubEntry($depth, $level, $title, $identifier, &$list) {
		if (!isset($list['sub']))
			$list['sub'] = array();
		$offset = count($list['sub']);
		if ($depth === 1) {
			$list['sub'][$offset] = array(
				'id'	=> $identifier,
				'value'	=> $title
			);
			return;
		}
		$offset--;
		if (!isset($list['sub'][$offset]))
			$list['sub'][$offset] = array();
		$this->_addTocSubEntry($depth - 1, $level, $title, $identifier, $list['sub'][$offset]);
	}
	/**
	 * Returns a chunk of rendered TOC.
	 * @param	array	$list	List of TOC entries.
	 * @param	int	$depth	(optional) Depth in the tree. 1 by default.
	 * @return	string	The rendered chunk.
	 */
	private function _getRenderedToc($list, $depth=1) {
		// TODO implement for plain text
		if (!isset($list) || empty($list))
			return ('');
		$html = "<ul class=\"toc-list\">\n";
		foreach ($list as $entry) {
			$html .= "<li class=\"toc-entry\">\n";
			$html .= '<a href="#' . $this->getParam('anchorsPrefix') . $this->titleToIdentifier($depth, $entry['value']) . '">'. $entry['value'] . "</a>\n";
			if (isset($entry['sub']))
				$html .= $this->_getRenderedToc($entry['sub'], ($depth + 1));
			$html .= "</li>\n";
		}
		$html .= "</ul>\n";
		return ($html);
	}
}

