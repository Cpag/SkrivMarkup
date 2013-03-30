<?php

namespace Skriv\Markup\Html;

/**
 * traite les signes de type paragraphe
 */
use WikiRenderer\Renderer;

class MultiCR extends \WikiRenderer\Block {
	public $type = '';
	protected $_openTag = '';
	protected $_closeTag = '';
	private $_ignoreMultiCr, $_crCount = 0;

	function __construct(Renderer $wr) {
		parent::__construct($wr);
		$this->_mustClone = false;
		$this->_ignoreMultiCr = $this->engine->getConfig()->getParam('ignoreMultiCR');
	}

	public function open() {
		return parent::open();
	}

	public function detect($string, $inBlock = false) {
		if ($this->_ignoreMultiCr)
			return false;
		if (empty($string))
			return true;
		$this->_crCount = 0;
		return false;
	}

	protected function _renderInlineTag($string) {
		if (++$this->_crCount < 2)
			return '';
		return '<br/>';
	}
}

