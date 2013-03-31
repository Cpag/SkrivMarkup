<?php
namespace Skriv\Markup\Html;
use WikiRenderer\Tag;

class UnofficialInlineExt extends Tag {
	protected $name = 'ext';
	public $beginTag = '<<:';
	public $endTag = '>>';
	public $separators = array('|');

	protected function _doEscape($string) {
		return htmlspecialchars($string);
	}

	public function getContent() {
		$name = $this->wikiContentArr[0];
		$listedParams = array_slice($this->wikiContentArr, 1);
		/** @var $config Config */
		$config = $this->config;
		return $config->renderContext->getInlineExtensionContent($name, $listedParams, 'html');
	}
}

