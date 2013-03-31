<?php

namespace Skriv\Markup\PlainText;

/**
 * traite les signes de types pre (pour afficher du code..)
 */
class Pre extends \WikiRenderer\Block {
	public $type = 'pre';
	protected $regexp = "/^\s(.*)/";
	protected $_openTag = '';
	protected $_closeTag = '';

	public function getRenderedLine() {
		$text = $this->_detectMatch[1];
		return $text;
	}
}

