<?php

namespace Skriv\Markup\PlainText;

/**
 * traite les signes de types liste
 */
class WikiList extends \WikiRenderer\Block {
	public $type = 'list';
	protected $regexp = "/^([\*#]+)\s*(.*)/";

	public function getRenderedLine() {
		return $this->_detectMatch[1] . ' ' . $this->_renderInlineTag($this->_detectMatch[2]);
	}
}

