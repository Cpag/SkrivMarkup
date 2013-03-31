<?php

namespace Skriv\Markup\Html;

/**
 * Gestion des notes de bas de page.
 */
class Footnote extends \WikiRenderer\TagXhtml {
	protected $name = 'footnote';
	public $beginTag = '((';
	public $endTag = '))';
	public $separators = array('|');

	public function getContent() {
		if (isset($this->wikiContentArr[1])) {
			$label = $this->wikiContentArr[0];
			$content = $this->wikiContentArr[1];
		} else {
			$label = null;
			$content = $this->wikiContentArr[0];
		}
		/** @var $config Config */
		$config = $this->config;
		$note = $config->renderContext->addFootnote($content, $label);
		return '<sup class="footnote-ref"><a href="#' . $note['id'] . '">' . htmlspecialchars($note['label']) . '</a></sup>';
	}
}

