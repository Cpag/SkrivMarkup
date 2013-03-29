<?php

namespace Skriv\Markup\Html;

/**
 * Gestion des notes de bas de page.
 * @todo	Gérer les notes identifiées par un titre et non par un numéro.
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
		$note = $this->config->addFootnote($content, $label);
		return '<sup class="footnote-ref"><a href="#' . $note['id'] . '">' . $this->config->escHtml($note['label']) . '</a></sup>';
	}
}

