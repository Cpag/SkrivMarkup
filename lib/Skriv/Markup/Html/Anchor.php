<?php

namespace Skriv\Markup\Html;

class Anchor extends \WikiRenderer\TagXhtml {
	protected $name = 'anchor';
	public $beginTag = '~~';
	public $endTag = '~~';
	protected $attribute = array('name');
	public $separators = array('|');

	public function getContent() {
		$label = $this->wikiContentArr[0];
		$baseId = $this->config->getParam('anchorsPrefix') . $this->config->titleToIdentifier(null, $label);
		return '<span class="anchor" id="' . $this->config->createMarkupId($baseId) . '">' . $this->config->escHtml($label) . '</span>';
	}
}

