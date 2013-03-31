<?php
namespace Skriv\Markup\Html;
use Skriv\Markup\RenderingContext;

/**
 * SkrivMarkup rendering context object for HTML.
 *
 * @copyright	© 2012-2013, Amaury Bouchard
 * @package	SkrivMarkup
 * @see		WikiRenderer
 */

class HtmlRenderingContext extends RenderingContext {

	// list of extensions and their default configuration
	private $_extensions = array(
		'ext-lipsum'	=> true,
		'ext-date'	=> true,
	);

	/* ************ SKRIV MARKUP SPECIFIC ATTRIBUTES ************* */
	/** Used markup ids. */
	private $_markupIds;
	/** List of the footnotes. */
	private $_footnotes;
	/** Tree representing the table of content. */
	private $_toc;

	/* ******************** CONSTRUCTION ****************** */
	/**
	 * Constructor.
	 * @param	array	$params		(optionnel) Hash containing specific configuration parameters.
	 *		- bool		shortenLongUrl		Specifies if we must shorten URLs longer than 40 characters. (default: true)
	 *		- bool		convertSmileys		Specifies if we must convert smileys. (default: true)
	 *		- bool		convertSymbols		Specifies if we must convert symbols. (default: true)
	 *		- Closure	urlProcessFunction	URLs processing function. (default: null)
	 *		- Closure	preParseFunction	Function for pre-parse process. (default: null)
	 *		- Closure	postParseFunction	Function for post-parse process. (default: null)
	 *		- Closure	titleToIdFunction	Function that converts title strings into HTML identifiers. (default: null)
	 *		- string	markupIdsPrefix		Prefix for all identifiers. (default: "skriv-" + random value)
	 *		- string	anchorsPrefix		Prefix of anchors' identifiers. (default: '')
	 *		- string	footnotesPrefix		Prefix of footnotes' identifiers. (default: "note-")
	 *		- bool		codeSyntaxHighlight	Activate code highlighting. (default: true)
	 *		- bool		codeLineNumbers		Line numbers in code blocks. (default: true)
	 *		- int		firstTitleLevel		Offset of first level titles. (default: 1)
	 *		- bool		targetBlank		Add "target='_blank'" to every links.
	 *		- bool		nofollow		Add "rel='nofollow'" to every links.
	 *		- bool		addFootnotes		Add footnotes' content at the end of the page.
	 *		- bool		codeInlineStyles	Activate inline styles in code blocks. (default: false)
	 *		- bool		ignoreMultiCR		Ignore multiple carriage returns. (default: true)
	 *		- bool		forceInline		The produced HTML is with inline elements only. (default: false)
	 *		- bool		debugMode		Activate the debug mode, for development purposes. (default: false)
	 *		- bool		ext-lipsum		Activate the <<<lipsum>>> extension. (default: true)
	 *		- bool		ext-date		Activate the <<date>> extension. (default: true)
	 */
	public function __construct(array $params = null) {
		parent::__construct($params, array(
			'shortenLongUrl' => ['bool', true],
			'convertSmileys' => ['bool', true],
			'convertSymbols' => ['bool', true],
			'markupIdsPrefix' => ['string', 'skriv-' . base_convert(rand(0, 50000), 10, 36) . '-'],
			'anchorsPrefix' => ['string', ''],
			'footnotesPrefix' => ['string', 'note-'],
			'urlProcessFunction' => ['closure', null],
			'preParseFunction' => ['closure', null],
			'postParseFunction' => ['closure', null],
			'titleToIdFunction' => ['closure', null],
			'codeSyntaxHighlight' => ['bool', true],
			'codeLineNumbers' => ['bool', true],
			'firstTitleLevel' => ['int', 1],
			'targetBlank'	 => ['bool', false],
			'nofollow'	 => ['bool', false],
			'addFootnotes'	 => ['bool', false],
			'codeInlineStyles' => ['bool', false],
			'ignoreMultiCR' => ['bool', true],
			'forceInline' => ['bool', false],
			'debugMode' => ['bool', false]
		));

		// configuration of extensions
		foreach ($this->_extensions as $extensionName => $defaultConf) {
			if (isset($params[$extensionName]) && is_bool($params[$extensionName]))
				$this->_params[$extensionName] = $params[$extensionName];
			else
				$this->_params[$extensionName] = $defaultConf;
		}
	}

	/* *************** TEXT MANAGEMENT ************* */
	/**
	 * Convert title string to a usable HTML identifier.
	 * @param	int	$depth	Depth of the title.
	 * @param	string	$text	Input string.
	 * @return	string	The converted string.
	 */
	public function titleToIdentifier($depth, $text) {
		/** @var $func \Closure */
		$func = $this->getParam('titleToIdFunction');
		if (isset($func))
			return ($func($depth, $text));
		return $this->textToIdentifier($text);
	}

	public function textToIdentifier($text) {
		// conversion of accented characters
		// see http://www.weirdog.com/blog/php/supprimer-les-accents-des-caracteres-accentues.html
		$text = htmlentities($text, ENT_NOQUOTES, 'utf-8');
		$text = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $text);
		$text = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $text);	// for ligatures e.g. '&oelig;'
		$text = preg_replace('#&([lr]s|sb|[lrb]d)(quo);#', ' ', $text);	// for *quote (http://www.degraeve.com/reference/specialcharacters.php)
		$text = str_replace('&nbsp;', ' ', $text);                      // for non breaking space
		$text = preg_replace('#&[^;]+;#', '', $text);                   // strips other characters

		$text = preg_replace("/[^a-zA-Z0-9_-]/", ' ', $text);           // remove any other characters
		$text = str_replace(' ', '-', $text);
		$text = preg_replace('/\s+/', " ", $text);
		$text = preg_replace('/-+/', "-", $text);
		$text = trim($text, '-');
		$text = trim($text);
		$text = empty($text) ? '-' : $text;

		return ($text);
	}

	/* ******************** ID MANAGEMENT **************** */
	/**
	 * Create an ID for HTML markup. The id is unique.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function createMarkupId($baseId) {
		$prefixedBaseId = htmlentities($this->getParam('markupIdsPrefix') . $baseId, ENT_NOQUOTES, 'utf-8');
		$id = $prefixedBaseId;
		$num = 1;
		while (isset($this->_markupIds[$id])) {
			if ($num >= 100)
				throw new \Exception('Unable to find an ID based on "' . $baseId . '"');
			$id = $prefixedBaseId . '-' . ++$num;
		}
		$this->_markupIds[$id] = true;
		return $id;
	}

	/* *************** PARSING MANAGEMENT **************** */
	/**
	 * Method called for pre-parse processing once for all the document.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function reset() {
		$this->_markupIds = array();
		$this->_footnotes = array();
		$this->_toc = null;
	}
	/**
	 * Method called for pre-parse processing.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function onStart($text) {
		// process of smileys and other special characters
		if ($this->getParam('convertSmileys'))
			$text = Smiley::convertSmileys($text);
		if ($this->getParam('convertSymbols'))
			$text = Smiley::convertSymbols($text);
		// if a specific pre-parse function was defined, it is called
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
		return ($finalText);
	}
	/**
	 * Links processing.
	 * @param	string	$url		The URL to process.
	 * @return	array	Array with the processed URL and the generated label.
	 *			Third parameter is about blank targeting of the link. It could be
	 *			null (use the default behaviour), true (add a blank targeting) or
	 *			false (no blank targeting).
	 */
	public function processLink($url) {
		$label = $url = trim($url);
		$targetBlank = $this->getParam('targetBlank');
		$nofollow = $this->getParam('nofollow');
		// shortening of long URLs
		if ($this->getParam('shortenLongUrl') && strlen($label) > 40)
			$label = substr($label, 0, 40) . '...';
		// Javascript XSS check
		if (substr($url, 0, strlen('javascript:')) === 'javascript:')
			$url = '#';
		else {
			// email check
			if (filter_var($url, FILTER_VALIDATE_EMAIL)) {
				$url = "mailto:$url";
				$targetBlank = $nofollow = false;
			} else if (substr($url, 0, strlen('mailto:')) === 'mailto:') {
				$label = substr($url, strlen('mailto:'));
				$targetBlank = $nofollow = false;
			}
			// if a specific URL process function was defined, it is called
			/** @var $func \Closure */
			$func = $this->getParam('urlProcessFunction');
			if (isset($func))
				list($url, $label, $targetBlank, $nofollow) = $func($url, $label, $targetBlank, $nofollow);
		}

		return (array($url, $label, $targetBlank, $nofollow));
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
			'label' => isset($label) ? $label : strval($index),
			'text' => $text,
			'id' => $this->createMarkupId($this->getParam('footnotesPrefix') . $index),
			'index' => $index
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
		foreach ($this->_footnotes as $note) {
			$noteHtml = '<p class="footnote"><a href="#' . $note['id'] . '" id="' . $note['id'] . '">';
			$noteHtml .= htmlspecialchars($note['label']) . '</a>. ' . htmlspecialchars($note['text']);
			$noteHtml .= "</p>\n";
			$footnotes .= $noteHtml;
		}
		$footnotes = "<div class=\"footnotes\">\n$footnotes</div>\n";
		return $footnotes;
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

