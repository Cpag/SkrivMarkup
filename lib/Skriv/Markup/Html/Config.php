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
	private $_charset = 'UTF-8';
	/** List of the footnotes. */
	private $_footnotes = null;
	/** Used markup ids. */
	private $_markupIds;
	/** Tree representing the table of content. */
	private $_toc = null;
	/** Parent configuration object, used for recursive calls. */
	private $_parentConfig = null;
	/** Hash containing the configuration parameters. */
	private $_params = null;

	/* ******************** CONSTRUCTION ****************** */
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
	 * @param	\Skriv\Markup\Html\Config	parentConfig	Parent configuration object, for recursive calls.
	 */
	public function __construct(array $param=null, \Skriv\Markup\Html\Config $parentConfig=null) {
		// creation of the default parameters array
		$this->_params = array(
			'shortenLongUrl'	=> true,
			'convertSmileys'	=> true,
			'convertSymbols'	=> true,
			'markupIdsPrefix'	=> 'skriv-' . base_convert(rand(0, 50000), 10, 36) . '-',
			'anchorsPrefix'		=> '',
			'footnotesPrefix'	=> 'note-',
			'urlProcessFunction'	=> null,
			'preParseFunction'	=> null,
			'postParseFunction'	=> null,
			'titleToIdFunction'	=> null,
			'codeSyntaxHighlight'	=> true,
			'codeLineNumbers'	=> true,
			'firstTitleLevel'	=> 1,
			'targetBlank'		=> null,
			'nofollow'		=> null,
			'addFootnotes'		=> false,
			'codeInlineStyles'	=> false
		);
		// processing of specified parameters
		if (isset($param['charset']))
			$this->_charset = $param['charset'];
		if (isset($param['shortenLongUrl']) && $param['shortenLongUrl'] === false)
			$this->_params['shortenLongUrl'] = false;
		if (isset($param['convertSmileys']) && $param['convertSmileys'] === false)
			$this->_params['convertSmileys'] = false;
		if (isset($param['convertSymbols']) && $param['convertSymbols'] === false)
			$this->_params['convertymbols'] = false;
		if (isset($param['markupIdsPrefix']))
			$this->_params['markupIdsPrefix'] = $param['markupIdsPrefix'];
		if (isset($param['anchorsPrefix']))
			$this->_params['anchorsPrefix'] = $param['anchorsPrefix'];
		if (isset($param['footnotesPrefix']))
			$this->_params['footnotesPrefix'] = $param['footnotesPrefix'];
		if (isset($param['urlProcessFunction']) && is_a($param['urlProcessFunction'], 'Closure'))
			$this->_params['urlProcessFunction'] = $param['urlProcessFunction'];
		if (isset($param['preParseFunction']) && is_a($param['preParseFunction'], 'Closure'))
			$this->_params['preParseFunction'] = $param['preParseFunction'];
		if (isset($param['postParseFunction']) && is_a($param['postParseFunction'], 'Closure'))
			$this->_params['postParseFunction'] = $param['postParseFunction'];
		if (isset($param['titleToIdFunction']) && is_a($param['titleToIdFunction'], 'Closure'))
			$this->_params['titleToIdFunction'] = $param['titleToIdFunction'];
		if (isset($param['codeSyntaxHighlight']) && $param['codeSyntaxHighlight'] === false)
			$this->_params['codeSyntaxHighlight'] = $param['codeSyntaxHighlight'];
		if (isset($param['codeLineNumbers']) && $param['codeLineNumbers'] === false)
			$this->_params['codeLineNumbers'] = false;
		if (isset($param['firstTitleLevel']) && is_numeric($param['firstTitleLevel']) &&
		    $param['firstTitleLevel'] >= 1 && $param['firstTitleLevel'] <= 6)
			$this->_params['firstTitleLevel'] = $param['firstTitleLevel'];
		if (isset($param['targetBlank']) && is_bool($param['targetBlank']))
			$this->_params['targetBlank'] = $param['targetBlank'];
		if (isset($param['nofollow']) && is_bool($param['nofollow']))
			$this->_params['nofollow'] = $param['nofollow'];
		if (isset($param['addFootnotes']) && $param['addFootnotes'] === true)
			$this->_params['addFootnotes'] = $param['addFootnotes'];
		if (isset($param['codeInlineStyles']) && $param['codeInlineStyles'] === true)
			$this->_params['codeInlineStyles'] = $param['codeInlineStyles'];
		// storing the parent configuration object
		$this->_parentConfig = $parentConfig;
		// footnotes liste init
	}
	/**
	 * Build an object of the same type, "child" of the current object.
	 * @return	\Skriv\Markup\\Html\Config	The new configuration object.
	 */
	public function subConstruct() {
		return (new Config($this->_params, $this));
	}

	/* *************** PARAMETERS MANAGEMENT ************* */
	/**
	 * Returns a specific configuration parameter. If a parent configuration object exists, the parameter is asked to it.
	 * @param	string	$param	Parameter's name.
	 * @return	mixed	Value of the configuration parameter.
	 */
	public function getParam($param) {
		if (isset($this->_parentConfig))
			return ($this->_parentConfig->getParam($param));
		return (isset($this->_params[$param]) ? $this->_params[$param] : null);
	}

	/* *************** TEXT MANAGEMENT ************* */
	/**
	 * Escape special characters
	 */
	public function escHtml($s) {
		return htmlspecialchars($s, ENT_NOQUOTES, $this->_charset);
	}
	/**
	 * Escape special characters
	 */
	public function escAttr($s) {
		return htmlspecialchars($s, ENT_QUOTES, $this->_charset);
	}
	/**
	 * Convert title string to a usable HTML identifier.
	 * @param	int	$depth	Depth of the title.
	 * @param	string	$text	Input string.
	 * @return	string	The converted string.
	 */
	public function titleToIdentifier($depth, $text) {
		$func = $this->getParam('titleToIdFunction');
		if (isset($func))
			return ($func($depth, $text));
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

	/* *************** PARSING MANAGEMENT **************** */
	/**
	 * Method called for pre-parse processing.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function onStart($text) {
		$this->_footnotes = array();
		$this->_markupIds = array();
		$this->_toc = null;
		// process of smileys and other special characters
		if ($this->getParam('convertSmileys'))
			$text = Smiley::convertSmileys($text);
		if ($this->getParam('convertSymbols'))
			$text = Smiley::convertSymbols($text);
		// if a specific pre-parse function was defined, it is called
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
	 * @param	string	$tagName	Name of the calling tag.
	 * @return	array	Array with the processed URL and the generated label.
	 *			Third parameter is about blank targeting of the link. It could be
	 *			null (use the default behaviour), true (add a blank targeting) or
	 *			false (no blank targeting).
	 */
	public function processLink($url, $tagName='') {
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

	/* ******************** ID MANAGEMENT **************** */
	/**
	 * Create an ID for HTML markup. The id is unique.
	 * @param	string	$text	The input text.
	 * @return	string	The text that will be parsed.
	 */
	public function createMarkupId($baseId) {
		if (isset($this->_parentConfig))
			return $this->_parentConfig->createMarkupId($baseId);
		$prefixedBaseId = $this->getParam('markupIdsPrefix') . $baseId;
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

	/* ******************** FOOTNOTES MANAGEMENT **************** */
	/**
	 * Add a footnote.
	 * @param	string	$text	Footnote's text.
	 * @param	string	$label	(optionnel) Footnote's label. If not given, an auto-incremented
	 *				number will be used.
	 * @return	array	Hash with 'id' and 'index' keys.
	 */
	public function addFootnote($text, $label=null) {
		if (isset($this->_parentConfig))
			return ($this->_parentConfig->addFootnote($text, $label));
		$index = count($this->_footnotes) + 1;
		$note = array(
			'label'	=> $label, // nullable
			'text'	=> $text,
			'id'	=> $this->createMarkupId($this->getParam('footnotesPrefix') . $index),
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
		$index = 1;
		foreach ($this->_footnotes as $note) {
			$noteHtml = '<p class="footnote"><a href="#' . $note['id'] . '" id="' . $note['id'] . '">';
			if (isset($note['label']))
				$noteHtml .= $this->escHtml($note['label']) . '</a>. ' . $this->escHtml($note['text']);
			else
				$noteHtml .= $index . '</a>. ' . $this->escHtml($note['text']);
			$noteHtml .= "</p>\n";
			$footnotes .= $noteHtml;
			$index++;
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

