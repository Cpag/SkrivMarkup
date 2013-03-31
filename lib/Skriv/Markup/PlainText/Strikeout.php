<?php

namespace Skriv\Markup\PlainText;

/**
 * Gestion du texte barré.
 */
class Strikeout extends PlainTextTag {
	protected $name = 's';
	public $beginTag = '--';
	public $endTag = '--';
}

