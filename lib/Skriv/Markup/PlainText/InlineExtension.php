<?php
namespace Skriv\Markup\PlainText;

use WikiRenderer\Tag;

class InlineExtension extends Tag {
	protected $name = 'ext';
	public $beginTag = '<<';
	public $endTag = '>>';
	public $separators = array('|');

	protected function _doEscape($string) {
		return $string;
	}

	public function getContent() {
		$name = $this->wikiContentArr[0];
		$listedParams = array_slice($this->wikiContentArr, 1);
		/** @var $config Config */
		$config = $this->config;
		return $config->renderContext->getInlineExtensionContent($name, $listedParams, 'plain-text');
	}
}

