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
		/** @var $config Config */
		$config = $this->config;
		$baseId = $config->getParam('anchorsPrefix') . $config->renderContext->textToIdentifier($label);
		return '<span class="anchor" id="' . $config->renderContext->createMarkupId($baseId) . '">' . htmlspecialchars($label) . '</span>';
	}
}

