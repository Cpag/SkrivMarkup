<?php

namespace Skriv\Markup\PlainText;

/**
 * traite les signes de types table
 */
class Table extends \WikiRenderer\Block {
	public $type = 'table';
	protected $regexp = "/^(!!|\|\|) ?(.*)/";
	protected $_openTag = '';
	protected $_closeTag = '';
	protected $_colcount = 0;

	public function open() {
		$this->_colcount = 0;
		return ($this->_openTag);
	}
	public function getRenderedLine() {
		$str = '';
		$text = ' ' . $this->_detectMatch[0];
		$prevPos = 0;
		$loop = true;
		while ($loop) {
			$posTh = strpos($text, '!!', $prevPos);
			$posTd = strpos($text, '||', $prevPos);
			if ($posTh === false && $posTd === false) {
				$posTh = false;
				$posTd = strlen($text);
				$loop = false;
			}
			if ($posTh === false || (is_int($posTd) && $posTd < $posTh))
				$pos = $posTd;
			else
				$pos = $posTh;
			if ($prevPos) {
				$cell = substr($text, $prevPos, $pos - $prevPos);
				$str .= "|" . $this->_renderInlineTag(trim($cell));
			}
			$prevPos = $pos + 3;
		}
		return $str . '|';
	}
}

