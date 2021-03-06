<?php

namespace Skriv\Markup\PlainText;

/**
 * traite les signes de type paragraphe
 */
use WikiRenderer\Renderer;

class Paragraph extends \WikiRenderer\Block {
	public $type = 'p';
	protected $_openTag = '';
	protected $_closeTag = '';
	// attribut utilisé pour gérer les retours charriots dans les paragraphes
	private $_ignoreMultiCr, $_firstLine, $_crCount = 0;

	function __construct(Renderer $wr) {
		parent::__construct($wr);
		$this->_mustClone = false;
		$this->_ignoreMultiCr = $this->engine->getConfig()->getParam('ignoreMultiCR');
	}

	public function open() {
		$this->_firstLine = true;
		return parent::open();
	}

	/**
	 * Détection des paragraphes
	 * @param  string  $string    Ligne de texte servant pour la détection.
	 * @param  bool  $inBlock  (optional) True is the parser is already in the block.
	 * @return  bool  True si c'est un paragraphe.
	 */
	public function detect($string, $inBlock = false) {
		if (empty($string)) {
			if ($this->_ignoreMultiCr)
				return false;
			if (++$this->_crCount > 1) {
				$this->_detectMatch = array('', '');
				return true;
			}
			return false;
		} else
			$this->_crCount = 0;
		if (!preg_match("/^\s*\*{2}.*\*{2}\s*.*$/", $string) &&
				!preg_match("/^\s*#{2}.*#{2}\s*.*$/", $string) &&
				!preg_match("/^\s*<<[^<]*$/", $string) &&
				preg_match("/^\s*[\*#\-\!\| \t>;<=].*/", $string)
		)
			return (false);
		$this->_detectMatch = array($string, $string);
		return (true);
	}
}

