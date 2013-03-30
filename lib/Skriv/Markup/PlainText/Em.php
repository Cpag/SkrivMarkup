<?php

namespace Skriv\Markup\PlainText;

/**
 * Gestion de l'italic.
 */
class Em extends PlainTextTag {
	protected $name = 'em';
	public $beginTag = '\'\'';
	public $endTag = '\'\'';
}

