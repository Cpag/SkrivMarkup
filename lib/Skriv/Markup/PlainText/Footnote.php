<?php

namespace Skriv\Markup\PlainText;

/**
 * Gestion des notes de bas de page.
 */
class Footnote extends PlainTextTag {
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
		$note = $this->config->addFootnote($content, $label);
		return '(' . $note['label'] . ')';
	}
}

