<?php

namespace Skriv\Markup\DocBook;

class Anchor extends \WikiRenderer\TagXhtml {
	protected $name = 'anchor';
	public $beginTag = '~~';
	public $endTag = '~~';
	protected $attribute = array('name');
	public $separators = array('|');

	public function getContent() {
		/** @var $config Config */
		$config = $this->config;
		$identifier = $config->renderContext->textToIdentifier($this->wikiContentArr[0]);
		return ('<anchor id="' . $config->getParam('anchorsPrefix') . $identifier . '"/>');
	}
}

